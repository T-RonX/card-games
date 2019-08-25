<?php

namespace App\Cards\Standard\Suit;

use App\Deck\Card\Color\ColorInterface;

class JokerBlack extends Joker
{
	/**
	 * @var string
	 */
	public const CODE = 'X';

	/**
	 * @inheritDoc
	 */
	public function __construct(ColorInterface $color)
	{
		parent::__construct($color, '🃏', self::CODE);
	}
}