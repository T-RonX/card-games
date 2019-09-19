<?php

namespace App\Games\Duizenden\Networking\Message;

use App\Enum\Enum;

/**
 * @method static ActionType DEAL()
 * @method static ActionType DRAW_CARD()
 * @method static ActionType MELD_CARDS()
 * @method static ActionType EXTEND_MELD()
 * @method static ActionType DISCARD_CARD()
 * @method static ActionType REORDER_CARDS()
 */
class ActionType extends Enum
{
	public const DEAL = 'deal';
	public const DRAW_CARD = 'draw_card';
	public const MELD_CARDS = 'meld_cards';
	public const EXTEND_MELD = 'extend_meld';
	public const DISCARD_CARD = 'discard_card';
	public const REORDER_CARDS = 'reorder_cards';
}