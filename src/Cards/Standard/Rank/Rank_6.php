<?php

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_6 implements RankInterface
{
	/**
	 * @var int
	 */
	public const CODE = '6';

	/**
	 * @return mixed
	 */
	public function getValue(): int
	{
		return 6;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return self::CODE;
	}
}