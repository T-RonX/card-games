<?php

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_9 implements RankInterface
{
	/**
	 * @var int
	 */
	public const CODE = '9';

	/**
	 * @return mixed
	 */
	public function getValue(): int
	{
		return 9;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return self::CODE;
	}
}