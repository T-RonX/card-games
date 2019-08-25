<?php

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Rank_0 implements RankInterface
{
	/**
	 * @var int
	 */
	public const CODE = '0';


	/**
	 * @return mixed
	 */
	public function getValue(): int
	{
		return 0;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return self::CODE;
	}
}