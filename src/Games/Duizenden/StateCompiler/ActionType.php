<?php

declare(strict_types=1);

namespace App\Games\Duizenden\StateCompiler;

use App\Enum\Enum;

/**
 * @method static ActionType DEAL()
 * @method static ActionType DRAW_FROM_UNDRAWN()
 * @method static ActionType DRAW_FROM_DISCARDED()
 * @method static ActionType DRAW_FROM_DISCARDED_AND_MELD()
 * @method static ActionType MELD_CARDS()
 * @method static ActionType EXTEND_MELD()
 * @method static ActionType DISCARD_END_TURN()
 * @method static ActionType DISCARD_END_ROUND()
 * @method static ActionType DISCARD_END_GAME()
 * @method static ActionType REORDER_CARDS()
 * @method static ActionType UNDO_LAST_ACTION()
 * @method static ActionType INVALID_FIRST_MELD()
 * @method static ActionType INVALID_ROUND_END()
 */
class ActionType extends Enum
{
	public const DEAL = 'deal';
	public const DRAW_FROM_UNDRAWN = 'draw_from_undrawn';
	public const DRAW_FROM_DISCARDED = 'draw_from_discarded';
	public const DRAW_FROM_DISCARDED_AND_MELD = 'draw_from_discarded_and_meld';
	public const MELD_CARDS = 'meld_cards';
	public const EXTEND_MELD = 'extend_meld';
	public const DISCARD_END_TURN = 'discard_end_turn';
	public const DISCARD_END_ROUND = 'discard_end_round';
	public const DISCARD_END_GAME = 'discard_end_game';
	public const REORDER_CARDS = 'reorder_cards';
	public const UNDO_LAST_ACTION = 'undo_last_action';
	public const INVALID_FIRST_MELD = 'invalid_first_meld';
	public const INVALID_ROUND_END = 'invalid_round_end';
}