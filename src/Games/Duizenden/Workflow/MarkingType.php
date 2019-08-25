<?php

namespace App\Games\Duizenden\Workflow;

use App\Enum\Enum;

/**
 * @method static self CREATE()
 * @method static self CONFIGURED()
 * @method static self START_TURN()
 * @method static self CARD_DRAWN()
 * @method static self CARDS_MELTED()
 * @method static self MELT_EXTENDED()
 * @method static self TURN_END()
 * @method static self ROUND_END()
 * @method static self GAME_END()
 */
class MarkingType extends Enum
{
	private const CREATE = 'create';
	private const CONFIGURED = 'configured';
	private const START_TURN = 'start_turn';
	private const CARD_DRAWN = 'card_drawn';
	private const CARDS_MELTED = 'cards_melted';
	private const TURN_END = 'turn_end';
	private const ROUND_END = 'round_end';
	private const GAME_END = 'game_end';
}