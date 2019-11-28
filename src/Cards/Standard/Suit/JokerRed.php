<?php

declare(strict_types=1);

namespace App\Cards\Standard\Suit;

use App\Deck\Card\Color\ColorInterface;

class JokerRed extends Joker
{
	public const CODE = 'Y';

	public function __construct(ColorInterface $color)
	{
		parent::__construct($color, '🃏', self::CODE);
	}
}