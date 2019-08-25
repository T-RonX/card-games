<?php

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_8 implements RankInterface
{
	/**
	 * @var int
	 */
	public const CODE = '8';

	/**
	 * @return mixed
	 */
	public function getValue(): int
	{
		return 8;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return self::CODE;
	}
}