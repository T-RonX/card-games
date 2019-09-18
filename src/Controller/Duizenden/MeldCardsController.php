<?php

namespace App\Controller\Duizenden;

use App\CardPool\CardPool;
use App\CardPool\Exception\CardNotFoundException;
use App\Cards\Standard\Card;
use App\Cards\Standard\Exception\InvalidCardIdException;
use App\Common\Meld\Exception\InvalidMeldException;
use App\Enum\Exception\EnumConstantsCouldNotBeResolvedException;
use App\Enum\Exception\EnumNotDefinedException;
use App\Games\Duizenden\Actions\Meld\ExtendMeld;
use App\Games\Duizenden\Actions\Meld\MeldCards;
use App\Games\Duizenden\Exception\HandException;
use App\Games\Duizenden\Meld\Exception\MeldException;
use App\Games\Duizenden\Notifier\GameNotifier;
use App\Games\Duizenden\Persistence\Exception\GameNotFoundException;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Security\Voter\Duizenden\GameVoter;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MeldCardsController extends AbstractController
{
	use LoadGameTrait;

	/**
	 * @var GameNotifier
	 */
	private $game_notifier;

	/**
	 * @var MeldCards
	 */
	private $meld_cards;

	/**
	 * @var ExtendMeld
	 */
	private $extend_meld;

	/**
	 * @param MeldCards $meld_cards
	 * @param ExtendMeld $extend_meld
	 * @param GameNotifier $game_notifier
	 */
	public function __construct(
		MeldCards $meld_cards,
		ExtendMeld $extend_meld,
		GameNotifier $game_notifier
	)
	{
		$this->game_notifier = $game_notifier;
		$this->meld_cards = $meld_cards;
		$this->extend_meld = $extend_meld;
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
	 */
	public function meld(CardPool $cards): Response
	{
		$game = $this->loadGame();
		$this->denyAccessUnlessGranted(GameVoter::MELD, $game);

		$this->meld_cards->meld($game, $cards->getCards());

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
		$game = $this->loadGame();
		$this->denyAccessUnlessGranted(GameVoter::EXTEND_MELD, $game);

		$this->extend_meld->extend($game, $meld_id, $card);

		$this->game_notifier->notify($game->getId(), $game->getState()->getPlayers()->getCurrentPlayer()->getId());

		return $this->render('Duizenden\game.html.twig', [
			'game' => $game
		]);
	}
}