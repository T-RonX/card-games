<?php

declare(strict_types=1);

namespace App\Shuffler;

use App\Deck\Card\CardInterface;

interface ShufflerInterface
{
	/**
	 * @param CardInterface[] $cards
	 *
	 * @return CardInterface[]
	 */
	function shuffle(array $cards): array;
}