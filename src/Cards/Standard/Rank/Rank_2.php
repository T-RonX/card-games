<?php

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_2 implements RankInterface
{
	/**
	 * @var int
	 */
	public const CODE = '2';

	/**
	 * @return mixed
	 */
	public function getValue(): int
	{
		return 2;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return self::CODE;
	}
}