<?php

declare(strict_types=1);

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_4 implements RankInterface
{
	public const CODE = '4';

	public function getValue(): int
	{
		return 4;
	}

	public function getName(): string
	{
		return self::CODE;
	}
}