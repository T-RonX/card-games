<?php

declare(strict_types=1);

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_10 implements RankInterface
{
	public const CODE = '10';

	public function getValue(): int
	{
		return 10;
	}

	public function getName(): string
	{
		return self::CODE;
	}
}