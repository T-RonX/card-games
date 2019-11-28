<?php

declare(strict_types=1);

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Jack implements RankInterface
{
	public const CODE = 'J';

	public function getValue(): int
	{
		return 11;
	}

	public function getName(): string
	{
		return self::CODE;
	}
}