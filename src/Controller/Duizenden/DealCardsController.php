<?php

namespace App\Controller\Duizenden;

use App\CardPool\Exception\EmptyCardPoolException;
use App\Cards\Standard\Exception\InvalidCardIdException;
use App\Enum\Exception\EnumConstantsCouldNotBeResolvedException;
use App\Enum\Exception\EnumNotDefinedException;
use App\Games\Duizenden\Actions\Deal\Deal;
use App\Games\Duizenden\Notifier\GameNotifier;
use App\Games\Duizenden\Persistence\Exception\GameNotFoundException;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Security\Voter\Duizenden\GameVoter;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DealCardsController extends AbstractController
{
	use LoadGameTrait;

	/**
	 * @var GameNotifier
	 */
	private $game_notifier;

	/**
	 * @var Deal
	 */
	private $deal;

	/**
	 * @param Deal $deal
	 * @param GameNotifier $game_notifier
	 */
	public function __construct(
		Deal $deal,
		GameNotifier $game_notifier
	)
	{
		$this->game_notifier = $game_notifier;
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
	 */
	public function deal(): Response
	{
		$game = $this->loadGame();
		$this->denyAccessUnlessGranted(GameVoter::DEAL, $game);

		$player = $this->deal->deal($game);

		$this->game_notifier->notify($game->getId(), $player->getId());

		return $this->render('Duizenden\game.html.twig', [
			'game' => $game
		]);
	}
}