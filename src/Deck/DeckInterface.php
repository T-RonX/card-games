<?php

declare(strict_types=1);

namespace App\Deck;

use App\Deck\Card\CardInterface;

interface DeckInterface
{
	/**
	 * @return CardInterface[]
	 */
	function getCards(): array;
}