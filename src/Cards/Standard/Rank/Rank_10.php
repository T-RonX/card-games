<?php

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_10 implements RankInterface
{
	/**
	 * @var int
	 */
	public const CODE = '10';

	/**
	 * @return mixed
	 */
	public function getValue(): int
	{
		return 10;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return self::CODE;
	}
}