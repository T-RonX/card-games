<?php

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_4 implements RankInterface
{
	/**
	 * @var int
	 */
	public const CODE = '4';

	/**
	 * @return mixed
	 */
	public function getValue(): int
	{
		return 4;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return self::CODE;
	}
}