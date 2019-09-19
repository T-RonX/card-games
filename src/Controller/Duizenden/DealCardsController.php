<?php

namespace App\Controller\Duizenden;

use App\CardPool\Exception\EmptyCardPoolException;
use App\Cards\Standard\Exception\InvalidCardIdException;
use App\Enum\Exception\EnumConstantsCouldNotBeResolvedException;
use App\Enum\Exception\EnumNotDefinedException;
use App\Games\Duizenden\Actions\Deal\Deal;
use App\Games\Duizenden\Networking\Message\Action\DealAction;
use App\Games\Duizenden\Networking\Message\ActionType;
use App\Games\Duizenden\Networking\Message\InvalidActionException;
use App\Games\Duizenden\Persistence\Exception\GameNotFoundException;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;
use App\Security\Voter\Duizenden\GameVoter;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DealCardsController extends AbstractController
{
	use LoadGameTrait;
	use NotifyPlayersTrait;

	/**
	 * @var Deal
	 */
	private $deal;

	/**
	 * @param Deal $deal
	 */
	public function __construct(Deal $deal)
	{
		$this->deal = $deal;
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
	 * @throws UnmappedCardException
	 * @throws InvalidActionException
	 */
	public function deal(): Response
	{
		$game = $this->loadGame();
		$this->denyAccessUnlessGranted(GameVoter::DEAL, $game);

		$player = $this->deal->deal($game);

		$this->notifyPlayers($game, $player, ActionType::DEAL());

		return $this->json([]);
	}
}