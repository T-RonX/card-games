<?php

declare(strict_types=1);

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class King implements RankInterface
{
	public const CODE = 'K';

	public function getValue(): int
	{
		return 13;
	}

	public function getName(): string
	{
		return self::CODE;
	}
}