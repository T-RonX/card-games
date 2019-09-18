<?php

namespace App\Games\Duizenden\Workflow;

use App\Enum\Enum;

/**
 * @method static MarkingType CREATE()
 * @method static MarkingType CONFIGURED()
 * @method static MarkingType START_TURN()
 * @method static MarkingType CARD_DRAWN()
 * @method static MarkingType CARDS_MELTED()
 * @method static MarkingType MELT_EXTENDED()
 * @method static MarkingType TURN_END()
 * @method static MarkingType ROUND_END()
 * @method static MarkingType GAME_END()
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