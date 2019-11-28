<?php

declare(strict_types=1);

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_3 implements RankInterface
{
	public const CODE = '3';

	public function getValue(): int
	{
		return 3;
	}

	public function getName(): string
	{
		return self::CODE;
	}
}