<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Event;

use App\Games\Duizenden\Game;
use Symfony\Contracts\EventDispatcher\Event;

class GameEvent extends Event
{
	private Game $game;

	public function __construct(Game $game)
	{
		$this->game = $game;
	}

	public function getGame(): Game
	{
		return $this->game;
	}
}