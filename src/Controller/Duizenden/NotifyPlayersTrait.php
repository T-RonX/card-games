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
	 * @param TopicType $topic_type
	 *
	 * @throws EmptyCardPoolException
	 * @throws InvalidActionException
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	private function notifyPlayers(Game $game, PlayerInterface $source_player, ActionType $source_action): void
	{
		$message = $this->createNotifyPlayerMessage($game->getId(), $game, $source_player, $source_action, TopicType::GAME_EVENT());

		$this->game_notifier->notifyMessage($message);
	}

	/**
	 * @param string $identifier
	 * @param Game $game
	 * @param PlayerInterface $source_player
	 * @param ActionType $source_action
	 * @param TopicType $topic_type
	 *
	 * @return GameEventMessage
	 *
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	private function createNotifyPlayerMessage(string $identifier, Game $game, PlayerInterface $source_player, ActionType $source_action, TopicType $topic_type): GameEventMessage
	{
		$message = $this->game_notifier->createGameMessageBuilder($identifier, $game, $topic_type);
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