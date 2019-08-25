<?php

namespace App\Cards\Standard\Suit;

use App\Deck\Card\Color\ColorInterface;

class Spades extends Suit
{
	/**
	 * @var string
	 */
	public const CODE = 'S';

	/**
	 * @inheritDoc
	 */
	public function __construct(ColorInterface $color)
	{
		parent::__construct($color, '♠', self::CODE);
	}
}