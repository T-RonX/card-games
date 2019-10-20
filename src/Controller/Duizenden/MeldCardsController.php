<?php

namespace App\Controller\Duizenden;

use App\CardPool\CardPool;
use App\CardPool\Exception\CardNotFoundException;
use App\CardPool\Exception\EmptyCardPoolException;
use App\Cards\Standard\Card;
use App\Cards\Standard\Exception\InvalidCardIdException;
use App\Common\Meld\Exception\InvalidMeldException;
use App\Enum\Exception\EnumConstantsCouldNotBeResolvedException;
use App\Enum\Exception\EnumNotDefinedException;
use App\Games\Duizenden\Actions\Meld\ExtendMeld;
use App\Games\Duizenden\Actions\Meld\MeldCards;
use App\Games\Duizenden\Exception\HandException;
use App\Games\Duizenden\Meld\Exception\MeldException;
use App\Games\Duizenden\Persistence\Exception\GameNotFoundException;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;
use App\Games\Duizenden\StateCompiler\ActionType;
use App\Games\Duizenden\StateCompiler\InvalidActionException;
use App\Security\Voter\Duizenden\GameVoter;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MeldCardsController extends AbstractController
{
	use LoadGameTrait;
	use NotifyPlayersTrait;

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
	 */
	public function __construct(
		MeldCards $meld_cards,
		ExtendMeld $extend_meld
	)
	{
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
	 * @throws UnmappedCardException
	 */
	public function meld(CardPool $cards): Response
	{
		$game = $this->loadGame();
		$this->denyAccessUnlessGranted(GameVoter::MELD, $game);

		$this->meld_cards->meld($game, $cards->getCards());

		$this->notifyPlayers($game, $game->getState()->getPlayers()->getCurrentPlayer(), ActionType::MELD_CARDS());

		return $this->json([]);
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
	 * @throws UnmappedCardException
	 */
	public function extendMeld(Card $card, int $meld_id): Response
	{
		$game = $this->loadGame();
		$this->denyAccessUnlessGranted(GameVoter::EXTEND_MELD, $game);

		$this->extend_meld->extend($game, $meld_id, $card);

		$this->notifyPlayers($game, $game->getState()->getPlayers()->getCurrentPlayer(), ActionType::EXTEND_MELD(), ['meld_id' => $meld_id]);

		return $this->json([]);
	}
}