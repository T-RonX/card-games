<?php

declare(strict_types=1);

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Queen implements RankInterface
{
	public const CODE = 'Q';

	public function getValue(): int
	{
		return 12;
	}

	public function getName(): string
	{
		return self::CODE;
	}
}