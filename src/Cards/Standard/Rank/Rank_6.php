<?php

declare(strict_types=1);

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_6 implements RankInterface
{
	public const CODE = '6';

	public function getValue(): int
	{
		return 6;
	}

	public function getName(): string
	{
		return self::CODE;
	}
}