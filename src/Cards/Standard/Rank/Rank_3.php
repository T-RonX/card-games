<?php

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_3 implements RankInterface
{
	/**
	 * @var int
	 */
	public const CODE = '3';

	/**
	 * @return mixed
	 */
	public function getValue(): int
	{
		return 3;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return self::CODE;
	}
}