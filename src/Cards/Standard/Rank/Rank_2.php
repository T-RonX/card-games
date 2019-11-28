<?php

declare(strict_types=1);

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_2 implements RankInterface
{
	public const CODE = '2';

	public function getValue(): int
	{
		return 2;
	}

	public function getName(): string
	{
		return self::CODE;
	}
}