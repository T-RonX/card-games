<?php

declare(strict_types=1);

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Ace implements RankInterface
{
	public const CODE = 'A';

	public function getValue(): int
	{
		return 14;
	}

	public function getName(): string
	{
		return self::CODE;
	}
}