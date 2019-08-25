<?php

namespace App\Deck;

use App\Deck\Card\CardInterface;

interface DeckInterface
{
	/**
	 * @return CardInterface[]
	 */
	function getCards(): array;
}