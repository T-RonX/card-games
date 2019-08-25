<?php

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Ace implements RankInterface
{
	/**
	 * @var int
	 */
	public const CODE = 'A';

	/**
	 * @return mixed
	 */
	public function getValue(): int
	{
		return 14;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return self::CODE;
	}
}