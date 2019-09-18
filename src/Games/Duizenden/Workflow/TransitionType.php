<?php

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
	private const CONFIGURE = 'configure';
	private const DEAL = 'deal';
	private const DRAW_FROM_UNDRAWN = 'draw_from_undrawn';
	private const DRAW_FROM_DISCARDED = 'draw_from_discarded';
	private const MELD = 'meld';
	private const EXTEND_MELD = 'extend_meld';
	private const DISCARD_END_TURN = 'discard_end_turn';
	private const DISCARD_END_ROUND = 'discard_end_round';
	private const DISCARD_END_GAME = 'discard_end_game';
	private const RESTART_GAME = 'restart_game';
}