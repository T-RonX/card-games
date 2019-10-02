<?php

namespace App\Games\Duizenden\StateCompiler;

use App\Enum\Enum;

/**
 * @method static TopicType GAME_EVENT()
 * @method static TopicType PLAYER_EVENT()
 */
class TopicType extends Enum
{
	private const GAME_EVENT = 'game_event';
	private const PLAYER_EVENT = 'player_event';
}