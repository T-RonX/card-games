<?php

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_7 implements RankInterface
{
	/**
	 * @var int
	 */
	public const CODE = '7';

	/**
	 * @return mixed
	 */
	public function getValue(): int
	{
		return 7;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return self::CODE;
	}
}