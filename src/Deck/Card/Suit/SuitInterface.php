<?php

namespace App\Deck\Card\Suit;

use App\Deck\Card\Color\ColorInterface;

interface SuitInterface
{
	/**
	 * @return string
	 */
	function getSymbol(): string;

	/**
	 * @return string
	 */
	function getName(): string;

	/**
	 * @return ColorInterface
	 */
	function getColor(): ColorInterface;
}