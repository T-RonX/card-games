<?php

declare(strict_types=1);

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_1 implements RankInterface
{
	public const CODE = '1';

	public function getValue(): int
	{
		return 1;
	}

	public function getName(): string
	{
		return self::CODE;
	}
}