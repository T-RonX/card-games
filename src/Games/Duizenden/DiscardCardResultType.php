<?php

namespace App\Games\Duizenden;

use App\Enum\Enum;

/**
 * @method static DiscardCardResultType END_TURN()
 * @method static DiscardCardResultType END_ROUND()
 * @method static DiscardCardResultType END_GAME()
 */
class DiscardCardResultType extends Enum
{
	private const END_TURN = 'end_turn';
	private const END_ROUND = 'end_round';
	private const END_GAME = 'end_game';
}