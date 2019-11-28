<?php

declare(strict_types=1);

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_5 implements RankInterface
{
	public const CODE = '5';

	public function getValue(): int
	{
		return 5;
	}

	public function getName(): string
	{
		return self::CODE;
	}
}