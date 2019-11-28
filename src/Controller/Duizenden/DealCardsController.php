<?php

declare(strict_types=1);

namespace App\Controller\Duizenden;

use App\CardPool\Exception\EmptyCardPoolException;
use App\Cards\Standard\Exception\InvalidCardIdException;
use App\Enum\Exception\EnumConstantsCouldNotBeResolvedException;
use App\Enum\Exception\EnumNotDefinedException;
use App\Games\Duizenden\Actions\Deal\Deal;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Persistence\Exception\GameNotFoundException;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;
use App\Games\Duizenden\StateCompiler\ActionType;
use App\Games\Duizenden\StateCompiler\TopicType;
use App\Security\Voter\Duizenden\GameVoter;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DealCardsController extends AbstractController
{
	use LoadGameTrait;
	use NotifyPlayersTrait;

	private Deal $deal;

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
	 * @throws NoResultException
	 */
	public function deal(): Response
	{
		$game = $this->loadGame();
		$this->denyAccessUnlessGranted(GameVoter::DEAL, $game);

		$player = $this->deal->deal($game);

		$this->notifyPlayers($game, $player);

		return $this->json([]);
	}

	/**
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	private function notifyPlayers(Game $game, PlayerInterface $current_player): void
	{
		foreach ($game->getState()->getPlayers()->getFreshLoopIterator() as $player)
		{
			$message = $this->createNotifyPlayerMessage($player->getId(), $game, $current_player, ActionType::DEAL(), TopicType::PLAYER_EVENT());
			$message->addPlayersFullCardPool($player->getId());
			$this->game_notifier->notifyMessage($message);
		}
	}
}