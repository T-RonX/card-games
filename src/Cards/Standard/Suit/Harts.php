<?php

declare(strict_types=1);

namespace App\Cards\Standard\Suit;

use App\Deck\Card\Color\ColorInterface;

class Harts extends Suit
{
	public const CODE = 'H';

	public function __construct(ColorInterface $color)
	{
		parent::__construct($color, '♥', self::CODE);
	}
}