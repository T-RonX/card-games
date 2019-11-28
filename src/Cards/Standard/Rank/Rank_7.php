<?php

declare(strict_types=1);

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_7 implements RankInterface
{
	public const CODE = '7';

	public function getValue(): int
	{
		return 7;
	}

	public function getName(): string
	{
		return self::CODE;
	}
}