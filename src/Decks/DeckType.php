<?php

declare(strict_types=1);

namespace App\Decks;

use App\Enum\Enum;

/**
 * @method static DeckType STANDARD108_BLUE()
 * @method static DeckType STANDARD108_RED()
 */
class DeckType extends Enum
{
	private const STANDARD108_BLUE = 'standard108_blue';
	private const STANDARD108_RED = 'standard108_red';
}