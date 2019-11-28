<?php

declare(strict_types=1);

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_0 implements RankInterface
{
	public const CODE = '0';

	public function getValue(): int
	{
		return 0;
	}

	public function getName(): string
	{
		return self::CODE;
	}
}