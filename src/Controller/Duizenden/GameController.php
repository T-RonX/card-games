<?php

namespace App\Controller\Duizenden;

use App\Cards\Standard\Exception\InvalidCardIdException;
use App\Entity\Player;
use App\Enum\Exception\EnumConstantsCouldNotBeResolvedException;
use App\Enum\Exception\EnumNotDefinedException;
use App\Form\Duizenden\CreateGameType;
use App\Games\Duizenden\Configurator;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\GameDeleter;
use App\Games\Duizenden\GameManipulator;
use App\Games\Duizenden\Initializer\Exception\InvalidDealerPlayerException;
use App\Games\Duizenden\Notifier\GameNotifier;
use App\Games\Duizenden\Persistence\Exception\GameNotFoundException;
use App\Games\Duizenden\Player\Exception\EmptyPlayerSetException;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Player\PlayerFactory;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;
use App\Games\Duizenden\StateBuilder\StateBuilder;
use App\Games\Duizenden\StateCompiler\ActionType;
use App\Games\Duizenden\StateCompiler\StatusType;
use App\Games\Duizenden\StateCompiler\TopicType;
use App\Lobby\Entity\Invitation;
use App\Lobby\Inviter;
use App\Lobby\LobbyNotifier;
use App\Repository\PlayerRepository;
use App\Security\Voter\Duizenden\GameVoter;
use App\Security\Voter\InvitationVoter;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GameController extends AbstractController
{
	use LoadGameTrait;

	/**
	 * @var PlayerFactory
	 */
	private $player_factory;

	/**
	 * @var PlayerRepository
	 */
	private $player_repository;

	/**
	 * @var GameDeleter
	 */
	private $game_deleter;

	/**
	 * @var Inviter
	 */
	private $inviter;

	/**
	 * @var LobbyNotifier
	 */
	private $lobby_notifier;

	/**
	 * @var StateBuilder
	 */
	private $state_builder;

	/**
	 * @var GameManipulator
	 */
	private $game_manipulator;

	/**
	 * @var GameNotifier
	 */
	private $game_notifier;

	/**
	 * @param PlayerRepository $player_repository
	 * @param PlayerFactory $player_factory
	 * @param GameDeleter $game_deleter
	 * @param GameManipulator $game_manipulator
	 * @param Inviter $inviter
	 * @param LobbyNotifier $notifier
	 * @param StateBuilder $state_builder
	 * @param GameNotifier $game_notifier
	 */
	public function __construct(
		PlayerRepository $player_repository,
		PlayerFactory $player_factory,
		GameDeleter $game_deleter,
		GameManipulator $game_manipulator,
		Inviter $inviter,
		LobbyNotifier $notifier,
		StateBuilder $state_builder,
		GameNotifier $game_notifier
	)
	{
		$this->player_repository = $player_repository;
		$this->player_factory = $player_factory;
		$this->game_deleter = $game_deleter;
		$this->inviter = $inviter;
		$this->lobby_notifier = $notifier;
		$this->state_builder = $state_builder;
		$this->game_manipulator = $game_manipulator;
		$this->game_notifier = $game_notifier;
	}

	/**
	 * @param Invitation|null $invitation
	 *
	 * @return Response
	 */
	public function newGame(?Invitation $invitation = null): Response
	{
		$form = $this->createForm(CreateGameType::class, null, [
			'available_players' => $this->getAvailablePlayers($invitation),
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
	public function createGame(Request $request, ?Invitation $invitation = null): Response
	{
		$form = $this->createForm(CreateGameType::class, null, [
			'available_players' => $this->getAvailablePlayers($invitation),
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

			$game = $this->create($configurator);
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
	 * @param Configurator $configurator
	 *
	 * @return Game
	 *
	 * @throws EmptyPlayerSetException
	 * @throws InvalidDealerPlayerException
	 */
	private function create(Configurator $configurator): Game
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
	 * @return Response
	 *
	 * @throws EnumConstantsCouldNotBeResolvedException
	 * @throws EnumNotDefinedException
	 * @throws GameNotFoundException
	 * @throws InvalidCardIdException
	 * @throws NonUniqueResultException
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	public function playGame(string $uuid): Response
	{
		$this->session->set('game_id', $uuid);
		$game = $this->loadGame($uuid);
		$this->denyAccessUnlessGranted(GameVoter::ENTER_GAME, $game);

		$state_date = $this->state_builder->createStateData($game);
		$state = $state_date->create();

		return $this->render('Duizenden\game.html.twig', [
			'game' => $game,
			'state_data' => $state
		]);
	}

	/**
	 * @param string $uuid
	 *
	 * @return Response
	 */
	public function deleteGame(string $uuid): Response
	{
		$this->game_deleter->delete($uuid);

		return $this->redirect($this->generateUrl('game.saved'));
	}

	/**
	 * @param Invitation|null $invitation
	 *
	 * @return PlayerInterface[]
	 */
	private function getAvailablePlayers(?Invitation $invitation): array
	{
		if (null !== $invitation)
		{
			$this->denyAccessUnlessGranted(InvitationVoter::CREATE_GAME, $invitation);
			return $invitation->getAllPlayers(true);
		}

		return $this->player_repository->createQueryBuilder('p', 'p.uuid')->getQuery()->execute() ?? [];
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
	 * @throws ORMException
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	public function undoLastAction(string $uuid)
	{
		$game = $this->loadGame($uuid);

		$this->game_manipulator->undoLastAction($uuid);
		$message = $this->game_notifier->createGameMessageBuilder($game->getId(), $game, TopicType::GAME_EVENT());
		$message->setSourceAction(ActionType::UNDO_LAST_ACTION());
		$message->setSourcePlayer($game->getGamePlayerById($this->getUser()->getUuid()));

		$this->game_notifier->notifyMessage($message);
		return new Response();
	}
}