<?php

declare(strict_types=1);

namespace App\Shufflers\Random;

use App\Shuffler\ShufflerInterface;

class RandomShuffle implements ShufflerInterface
{
	function shuffle(array $cards): array
	{
		shuffle($cards);

		return $cards;
	}
}