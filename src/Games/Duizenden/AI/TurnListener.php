<?php

declare(strict_types=1);

namespace App\Games\Duizenden\AI;

use App\Games\Duizenden\Event\GameEvent;
use App\Games\Duizenden\Event\GameEventType;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Player\PlayerType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TurnListener implements EventSubscriberInterface
{
	private AI $ai;

	public function __construct(AI $ai)
	{
		$this->ai = $ai;
	}

	public static function getSubscribedEvents(): array
	{
		return [
			GameEventType::GAME_STARTED()->getValue() => ['gameStarted'],
			GameEventType::TURN_STARTED()->getValue() => ['turnStarted'],
		];
	}

	public function gameStarted(GameEvent $event): void
	{
		if ($this->isAIPlayer($event->getGame()->getState()->getDealingPlayer()))
		{
			$this->act($event->getGame());
		}
	}

	public function turnStarted(GameEvent $event): void
	{
		if ($this->isAIPlayer($event->getGame()->getState()->getPlayers()->getCurrentPlayer()))
		{
			$this->act($event->getGame());
		}
	}

	private function act(Game $game): void
	{
		$this->ai->act($game);
	}

	private function isAIPlayer(?PlayerInterface $player): bool
	{
		return $player !== null && $player->isType(PlayerType::AI());
	}
}