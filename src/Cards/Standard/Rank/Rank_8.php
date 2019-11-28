<?php

declare(strict_types=1);

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_8 implements RankInterface
{
	public const CODE = '8';

	public function getValue(): int
	{
		return 8;
	}

	public function getName(): string
	{
		return self::CODE;
	}
}