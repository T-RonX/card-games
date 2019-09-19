<?php

namespace App\Controller\Duizenden;

use App\CardPool\Exception\EmptyCardPoolException;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Networking\Message\ActionInterface;
use App\Games\Duizenden\Networking\Message\ActionType;
use App\Games\Duizenden\Networking\Message\GameEventMessage;
use App\Games\Duizenden\Networking\Message\InvalidActionException;
use App\Games\Duizenden\Networking\Message\TopicType;
use App\Games\Duizenden\Notifier\GameNotifier;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;

trait NotifyPlayersTrait
{
	/**
	 * @var GameNotifier
	 */
	private $game_notifier;

	/**
	 * @param Game $game
	 * @param PlayerInterface $source_player
	 * @param ActionType $source_action
	 *
	 * @throws EmptyCardPoolException
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 * @throws InvalidActionException
	 */
	private function notifyPlayers(Game $game, PlayerInterface $source_player, ActionType $source_action): void
	{
		$message = $this->createNotifyPlayerMessage($game, $source_player, $source_action);

		$this->game_notifier->notifyMessage($message);
	}

	/**
	 * @param Game $game
	 * @param PlayerInterface $source_player
	 * @param ActionType $source_action
	 *
	 * @return GameEventMessage
	 *
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	private function createNotifyPlayerMessage(Game $game, PlayerInterface $source_player, ActionType $source_action): GameEventMessage
	{
		$message = $this->game_notifier->createGameMessageBuilder($game, TopicType::GAME_EVENT());
		$message->setSourcePlayer($source_player);
		$message->setSourceAction($source_action);

		return $message;
	}

	/**
	 * @required
	 */
	public function setGameNotifier(GameNotifier $notifier): void
	{
		$this->game_notifier = $notifier;
	}
}