<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Workflow;

use App\Enum\Enum;

/**
 * @method static TransitionType CONFIGURE()
 * @method static TransitionType DEAL()
 * @method static TransitionType DRAW_FROM_UNDRAWN()
 * @method static TransitionType DRAW_FROM_DISCARDED()
 * @method static TransitionType MELD()
 * @method static TransitionType EXTEND_MELD()
 * @method static TransitionType DISCARD_END_TURN()
 * @method static TransitionType DISCARD_END_ROUND()
 * @method static TransitionType DISCARD_END_GAME()
 * @method static TransitionType RESTART_GAME()
 */
class TransitionType extends Enum
{
	public const CONFIGURE = 'configure';
	public const DEAL = 'deal';
	public const DRAW_FROM_UNDRAWN = 'draw_from_undrawn';
	public const DRAW_FROM_DISCARDED = 'draw_from_discarded';
	public const MELD = 'meld';
	public const EXTEND_MELD = 'extend_meld';
	public const DISCARD_END_TURN = 'discard_end_turn';
	public const DISCARD_END_ROUND = 'discard_end_round';
	public const DISCARD_END_GAME = 'discard_end_game';
	public const RESTART_GAME = 'restart_game';
}