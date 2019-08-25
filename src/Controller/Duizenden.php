<?php

namespace App\Controller;

use App\CardPool\CardPool;
use App\CardPool\Exception\CardNotFoundException;
use App\CardPool\Exception\EmptyCardPoolException;
use App\Cards\Standard\Card;
use App\Cards\Standard\Exception\InvalidCardIdException;
use App\Common\Meld\Exception\InvalidMeldException;
use App\Entity\Player;
use App\Enum\Exception\EnumConstantsCouldNotBeResolvedException;
use App\Enum\Exception\EnumNotDefinedException;
use App\Form\Duizenden\CreateGameType;
use App\Game\GameFactory;
use App\Game\Meta\GameMetaLoader;
use App\Games\Duizenden\Configurator;
use App\Games\Duizenden\DiscardCardResultType;
use App\Games\Duizenden\Exception\DiscardCardException;
use App\Games\Duizenden\Exception\DrawCardException;
use App\Games\Duizenden\Exception\HandException;
use App\Games\Duizenden\Exception\OutOfCardsException;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\GameDeleter;
use App\Games\Duizenden\GameLoader;
use App\Games\Duizenden\Initializer\Exception\InvalidDealerPlayerException;
use App\Games\Duizenden\Meld\Exception\MeldException;
use App\Games\Duizenden\Notifier\GameNotifier;
use App\Games\Duizenden\Persistence\Exception\GameNotFoundException;
use App\Games\Duizenden\Player\Exception\EmptyPlayerSetException;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Player\PlayerFactory;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;
use App\Lobby\Entity\Invitation;
use App\Lobby\Inviter;
use App\Lobby\LobbyNotifier;
use App\Repository\PlayerRepository;
use App\Security\Voter\Duizenden\GameVoter;
use App\Security\Voter\InvitationVoter;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Workflow\StateMachine;

class Duizenden extends AbstractController
{
	/**
	 * @var PlayerFactory
	 */
	private $player_factory;

	/**
	 * @var GameFactory
	 */
	private $game_factory;

	/**
	 * @var GameLoader
	 */
	private $game_loader;

	/**
	 * @var SessionInterface
	 */
	private $session;

	/**
	 * @var PlayerRepository
	 */
	private $player_repository;

	/**
	 * @var GameMetaLoader
	 */
	private $game_meta_loader;

	/**
	 * @var GameDeleter
	 */
	private $game_deleter;
	/**
	 * @var Publisher
	 */
	private $publisher;

	/**
	 * @var StateMachine
	 */
	private $state_machine;

	/**
	 * @var Inviter
	 */
	private $inviter;

	/**
	 * @var LobbyNotifier
	 */
	private $lobby_notifier;

	/**
	 * @var GameNotifier
	 */
	private $game_notifier;

	/**
	 * @param PlayerFactory $player_factory
	 * @param PlayerRepository $player_repository
	 * @param GameDeleter $game_deleter
	 * @param GameFactory $game_factory
	 * @param GameLoader $game_loader
	 * @param SessionInterface $session
	 * @param GameMetaLoader $game_meta_loader
	 * @param Publisher $publisher
	 * @param StateMachine $state_machine
	 * @param Inviter $inviter
	 * @param LobbyNotifier $notifier
	 * @param GameNotifier $game_notifier
	 */
	public function __construct(
		PlayerFactory $player_factory,
		PlayerRepository $player_repository,
		GameDeleter $game_deleter,
		GameFactory $game_factory,
		GameLoader $game_loader,
		SessionInterface $session,
		GameMetaLoader $game_meta_loader,
		Publisher $publisher,
		StateMachine $state_machine,
		Inviter $inviter,
		LobbyNotifier $notifier,
		GameNotifier $game_notifier
	//	MessageBusInterface $bus
	)
	{
		$this->player_factory = $player_factory;
		$this->game_factory = $game_factory;
		$this->game_loader = $game_loader;
		$this->session = $session;
		$this->player_repository = $player_repository;
		$this->game_meta_loader = $game_meta_loader;
		$this->game_deleter = $game_deleter;
		$this->publisher = $publisher;
		$this->state_machine = $state_machine;
		$this->inviter = $inviter;
		$this->lobby_notifier = $notifier;
		$this->game_notifier = $game_notifier;
	}

	/**
	 * @param string $uuid
	 *
	 * @return Response
	 *
	 * @throws EnumConstantsCouldNotBeResolvedException
	 * @throws EnumNotDefinedException
	 * @throws GameNotFoundException
	 * @throws InvalidCardIdException
	 * @throws NonUniqueResultException
	 * @throws PlayerNotFoundException
	 */
	public function play(string $uuid): Response
	{
		$this->session->set('game_id', $uuid);
		$game = $this->loadGame($uuid);
		$this->denyAccessUnlessGranted(GameVoter::ENTER_GAME, $game);

		return $this->render('Duizenden\game.html.twig', [
			'game' => $game
		]);
	}

	/**
	 * @param Invitation|null $invitation
	 *
	 * @return Response
	 */
	public function newGame(?Invitation $invitation = null): Response
	{
		if ($invitation)
		{
			$this->denyAccessUnlessGranted(InvitationVoter::CREATE_GAME, $invitation);
			$available_players = $invitation->getAllPlayers(true);
		}
		else
		{
			$available_players = $this->player_repository->createQueryBuilder('p', 'p.uuid')->getQuery()->execute();
		}

		$form = $this->createForm(CreateGameType::class, null, [
			'available_players' => $available_players ?? [],
			'by_invitation' => null !== $invitation
		]);

		return $this->render('Duizenden\new.html.twig', [
			'form' => $form->createView(),
			'invitation' => $invitation
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @param Invitation|null $invitation
	 * @return Response
	 *
	 * @throws EmptyPlayerSetException
	 * @throws InvalidDealerPlayerException
	 */
	public function create(Request $request, ?Invitation $invitation = null): Response
	{
		if ($invitation)
		{
			$this->denyAccessUnlessGranted(InvitationVoter::CREATE_GAME, $invitation);
			$available_players = $invitation->getAllPlayers(true);
		}
		else
		{
			$available_players = $this->player_repository->createQueryBuilder('p', 'p.uuid')->getQuery()->execute();
		}

		$form = $this->createForm(CreateGameType::class, null, [
			'available_players' => $available_players ?? [],
			'by_invitation' => null !== $invitation
		]);

		if ($form->handleRequest($request) && $form->isValid())
		{
			/**
			 * @var Player[] $players
			 * @var Player $first_dealer
			 */
			$players = $form->has('players') ? $form['players']->getData() : $invitation->getAllPlayers(true);
			$initial_shuffle = $form['initial_shuffle']->getData();
			$initial_shuffle_algorithm = $form['initial_shuffle_algorithm']->getData();
			$is_dealer_random = $form['is_dealer_random']->getData();
			$first_dealer = $form['first_dealer']->getData();
			$target_score = $form['target_score']->getData();
			$first_meld_minimum_points = $form['first_meld_minimum_points']->getData();
			$game_players = [];

			foreach ($players as $player)
			{
				$game_players[] = $this->player_factory->create($player->getUuid());
			}

			$configurator = (new Configurator())
				->setPlayers($game_players)
				->setDoInitialShuffle($initial_shuffle)
				->setInitialShuffleAlgorithm($initial_shuffle ? $initial_shuffle_algorithm : null)
				->setFirstDealer($this->player_factory->create($first_dealer->getUuid()))
				->setIsDealerRandom($is_dealer_random)
				->setTargetScore($target_score)
				->setFirstMeldMinimumPoints($first_meld_minimum_points);

			$game = $this->createGame($configurator);
			$this->session->set('game_id', $game->getId());

			if ($invitation)
			{
				$this->inviter->assignGame($invitation, $game->getId());
				$this->lobby_notifier->publishInvitationGameStarted($invitation);
			}

			return $this->redirect($this->generateUrl('duizenden.play', [
				'uuid' => $game->getId()
			]));
		}

		return $this->render('Duizenden\new.html.twig', [
			'form' => $form->createView(),
			'invitation' => $invitation
		]);
	}

	/**
	 * @param string $uuid
	 *
	 * @return Response
	 */
	public function delete(string $uuid): Response
	{
		$this->game_deleter->delete($uuid);

		return $this->redirect($this->generateUrl('game.saved'));
	}

	/**
	 * @return Response
	 *
	 * @throws EmptyCardPoolException
	 * @throws EnumConstantsCouldNotBeResolvedException
	 * @throws EnumNotDefinedException
	 * @throws GameNotFoundException
	 * @throws InvalidCardIdException
	 * @throws NonUniqueResultException
	 * @throws PlayerNotFoundException
	 */
	public function deal(): Response
	{
		$game = $this->loadGame($this->session->get('game_id'));
		$this->denyAccessUnlessGranted(GameVoter::DEAL, $game);

		$player = $game->deal();

		$this->game_notifier->notify($game->getId(), $player->getId());

		return $this->render('Duizenden\game.html.twig', [
			'game' => $game
		]);
	}

	/**
	 * @return Response
	 *
	 * @throws EmptyCardPoolException
	 * @throws EnumConstantsCouldNotBeResolvedException
	 * @throws EnumNotDefinedException
	 * @throws GameNotFoundException
	 * @throws InvalidCardIdException
	 * @throws NonUniqueResultException
	 * @throws OutOfCardsException
	 * @throws PlayerNotFoundException
	 */
	public function drawFromUndrawn(): Response
	{
		$game = $this->loadGame($this->session->get('game_id'));
		$this->denyAccessUnlessGranted(GameVoter::DRAW_FROM_UNDRAWN, $game);

		$card = $game->drawCardFromUndrawnPool();

		$this->game_notifier->notify($game->getId(), $game->getState()->getPlayers()->getCurrentPlayer()->getId());

		return $this->render('Duizenden\game.html.twig', [
			'game' => $game,
			'drawn_card' => $card,
		]);
	}

	/**
	 * @param CardPool $meld_cards
	 *
	 * @return Response
	 *
	 * @throws CardNotFoundException
	 * @throws DrawCardException
	 * @throws EmptyCardPoolException
	 * @throws EnumConstantsCouldNotBeResolvedException
	 * @throws EnumNotDefinedException
	 * @throws GameNotFoundException
	 * @throws InvalidCardIdException
	 * @throws InvalidMeldException
	 * @throws MeldException
	 * @throws NonUniqueResultException
	 * @throws PlayerNotFoundException
	 */
	public function drawFromDiscarded(CardPool $meld_cards = null): Response
	{
		$game = $this->loadGame($this->session->get('game_id'));
		$this->denyAccessUnlessGranted(GameVoter::DRAW_FROM_DISCARDED, $game);

		if (null !== $meld_cards && $meld_cards->hasCards())
		{
			$game->drawCardFromDiscardedPoolAndMeld($meld_cards->getCards());
		}
		else
		{
			$game->drawCardFromDiscardedPool();
		}

		$this->game_notifier->notify($game->getId(), $game->getState()->getPlayers()->getCurrentPlayer()->getId());

		return $this->render('Duizenden\game.html.twig', [
			'game' => $game
		]);
	}

	/**
	 * @param CardPool $cards
	 *
	 * @return Response
	 *
	 * @throws CardNotFoundException
	 * @throws EnumConstantsCouldNotBeResolvedException
	 * @throws EnumNotDefinedException
	 * @throws GameNotFoundException
	 * @throws HandException
	 * @throws InvalidCardIdException
	 * @throws InvalidMeldException
	 * @throws MeldException
	 * @throws NonUniqueResultException
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	public function meld(CardPool $cards): Response
	{
		$game = $this->loadGame($this->session->get('game_id'));
		$this->denyAccessUnlessGranted(GameVoter::MELD, $game);

		$game->meldCards($cards->getCards());

		$this->game_notifier->notify($game->getId(), $game->getState()->getPlayers()->getCurrentPlayer()->getId());

		return $this->render('Duizenden\game.html.twig', [
			'game' => $game
		]);
	}

	/**
	 * @param Card $card
	 * @param int $meld_id
	 *
	 * @return Response
	 *
	 * @throws CardNotFoundException
	 * @throws EnumConstantsCouldNotBeResolvedException
	 * @throws EnumNotDefinedException
	 * @throws GameNotFoundException
	 * @throws HandException
	 * @throws InvalidCardIdException
	 * @throws InvalidMeldException
	 * @throws MeldException
	 * @throws NonUniqueResultException
	 * @throws PlayerNotFoundException
	 */
	public function extendMeld(Card $card, int $meld_id): Response
	{
		$game = $this->loadGame($this->session->get('game_id'));
		$this->denyAccessUnlessGranted(GameVoter::EXTEND_MELD, $game);

		$game->extendMeld($meld_id, $card);

		$this->game_notifier->notify($game->getId(), $game->getState()->getPlayers()->getCurrentPlayer()->getId());

		return $this->render('Duizenden\game.html.twig', [
			'game' => $game
		]);
	}

	/**
	 * @param Card $card
	 *
	 * @return Response
	 *
	 * @throws CardNotFoundException
	 * @throws DiscardCardException
	 * @throws EnumConstantsCouldNotBeResolvedException
	 * @throws EnumNotDefinedException
	 * @throws GameNotFoundException
	 * @throws InvalidCardIdException
	 * @throws NonUniqueResultException
	 * @throws PlayerNotFoundException
	 * @throws Exception
	 */
	public function discard(Card $card): Response
	{
		$game = $this->loadGame($this->session->get('game_id'));
		$this->denyAccessUnlessGranted(GameVoter::DISCARD, $game);

		$current_player = $game->getState()->getPlayers()->getCurrentPlayer();
		$result = $game->discardCard($card);

		switch ($result)
		{
			case DiscardCardResultType::END_TURN():
				break;

			case DiscardCardResultType::END_ROUND():
				break;

			case DiscardCardResultType::END_GAME():
				throw new Exception('Game ended');
		}

		$this->game_notifier->notify($game->getId(), $current_player->getId());

		return $this->render('Duizenden\game.html.twig', [
			'game' => $game
		]);
	}

	/**
	 * @param int $source
	 * @param int $target
	 *
	 * @return Response
	 *
	 * @throws EnumConstantsCouldNotBeResolvedException
	 * @throws EnumNotDefinedException
	 * @throws GameNotFoundException
	 * @throws InvalidCardIdException
	 * @throws NonUniqueResultException
	 * @throws PlayerNotFoundException
	 * @throws ORMException
	 * @throws OptimisticLockException
	 */
	public function reorderCard(int $source, int $target): Response
	{
		$game = $this->loadGame($this->session->get('game_id'));
		$this->denyAccessUnlessGranted(GameVoter::REORDER_CARDS, $game);

		$game->reorderCard($this->getGamePlayer($game), $source - 1, $target - 1);

		$this->game_notifier->notify($game->getId(), $game->getState()->getPlayers()->getCurrentPlayer()->getId());

		return new JsonResponse(['success' => true], 200);
	}

	/**
	 * @param Game $game
	 * @param Player $player
	 *
	 * @return PlayerInterface
	 */
	private function getGamePlayer(Game $game, Player $player = null): PlayerInterface
	{
		return $game->getGamePlayerById($player ? $player->getUuid() : $this->getUser()->getUuid());
	}

	/**
	 * @param Configurator $configurator
	 *
	 * @return Game
	 *
	 * @throws EmptyPlayerSetException
	 * @throws InvalidDealerPlayerException
	 */
	private function createGame(Configurator $configurator): Game
	{
		/** @var Game $game */
		$game = $this->game_factory->create(Game::NAME);
		$game->setCreatedMarking();
		$game->configure($configurator);

		return $game;
	}

	/**
	 * @param string $uuid
	 *
	 * @return Game
	 *
	 * @throws EnumConstantsCouldNotBeResolvedException
	 * @throws EnumNotDefinedException
	 * @throws InvalidCardIdException
	 * @throws NonUniqueResultException
	 * @throws PlayerNotFoundException
	 * @throws GameNotFoundException
	 */
	private function loadGame(string $uuid): Game
	{
		/** @var Game $game */
		$game = $this->game_factory->create(Game::NAME);
		$this->game_loader->load($game, $uuid);

		return $game;
	}
}