<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Event;

use App\Enum\Enum;

/**
 * @method static GameEventType GAME_STARTED()
 * @method static GameEventType TURN_STARTED()
 */
class GameEventType extends Enum
{
	private const GAME_STARTED = 'duizenden.game.started';
	private const TURN_STARTED = 'duizenden.turn.started';
}