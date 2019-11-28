<?php

declare(strict_types=1);

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_9 implements RankInterface
{
	public const CODE = '9';

	public function getValue(): int
	{
		return 9;
	}

	public function getName(): string
	{
		return self::CODE;
	}
}