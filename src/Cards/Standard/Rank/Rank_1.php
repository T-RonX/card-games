<?php

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_1 implements RankInterface
{
	/**
	 * @var int
	 */
	public const CODE = '1';

	/**
	 * @return mixed
	 */
	public function getValue(): int
	{
		return 1;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return self::CODE;
	}
}