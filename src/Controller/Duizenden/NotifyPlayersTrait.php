<?php

namespace App\Controller\Duizenden;

use App\CardPool\Exception\EmptyCardPoolException;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Networking\Message\GameEventData;
use App\Games\Duizenden\Notifier\GameNotifier;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;
use App\Games\Duizenden\StateCompiler\ActionType;
use App\Games\Duizenden\StateCompiler\InvalidActionException;
use App\Games\Duizenden\StateCompiler\TopicType;

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
	 * @param array|null $extras
	 *
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	private function notifyPlayers(Game $game, PlayerInterface $source_player, ActionType $source_action, ?array $extras = null): void
	{
		$message = $this->createNotifyPlayerMessage($game->getId(), $game, $source_player, $source_action, TopicType::GAME_EVENT(), $extras);

		$this->game_notifier->notifyMessage($message);
	}

	/**
	 * @param string $identifier
	 * @param Game $game
	 * @param PlayerInterface $source_player
	 * @param ActionType $source_action
	 * @param TopicType $topic_type
	 * @param array|null $extras
	 *
	 * @return GameEventData
	 *
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	private function createNotifyPlayerMessage(string $identifier, Game $game, PlayerInterface $source_player, ActionType $source_action, TopicType $topic_type, ?array $extras = null): GameEventData
	{
		$message = $this->game_notifier->createGameMessageBuilder($identifier, $game, $topic_type);
		$message->setSourcePlayer($source_player);
		$message->setSourceAction($source_action);

		if (null !== $extras)
		{
			$message->setExtras($extras);
		}

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