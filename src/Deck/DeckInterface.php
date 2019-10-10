<?php

namespace App\Deck;

use App\Deck\Card\CardInterface;
use App\Deck\Card\Color\ColorInterface;

interface DeckInterface
{
	/**
	 * @return CardInterface[]
	 */
	function getCards(): array;

	function getBackImage(): ColorInterface;
}