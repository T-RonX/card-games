<?php

declare(strict_types=1);

namespace App\Games\Duizenden;

use App\Enum\Enum;

/**
 * @method static DiscardCardResultType END_TURN()
 * @method static DiscardCardResultType END_ROUND()
 * @method static DiscardCardResultType END_GAME()
 * @method static DiscardCardResultType INVALID_FIRST_MELD()
 */
class DiscardCardResultType extends Enum
{
	private const END_TURN = 'end_turn';
	private const END_ROUND = 'end_round';
	private const END_GAME = 'end_game';
	private const INVALID_FIRST_MELD = 'invalid_first_meld';
}