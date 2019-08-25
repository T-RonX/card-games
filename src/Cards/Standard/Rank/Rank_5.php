<?php

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_5 implements RankInterface
{
	/**
	 * @var int
	 */
	public const CODE = '5';

	/**
	 * @return mixed
	 */
	public function getValue(): int
	{
		return 5;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return self::CODE;
	}
}