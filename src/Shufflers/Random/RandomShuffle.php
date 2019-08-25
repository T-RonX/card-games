<?php

namespace App\Shufflers\Random;

use App\Shuffler\ShufflerInterface;

class RandomShuffle implements ShufflerInterface
{
	/**
	 * @inheritDoc
	 */
	function shuffle(array $cards): array
	{
		shuffle($cards);

		return $cards;
	}
}