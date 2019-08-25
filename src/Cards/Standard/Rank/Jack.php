<?php

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Jack implements RankInterface
{
	/**
	 * @var int
	 */
	public const CODE = 'J';

	/**
	 * @return mixed
	 */
	public function getValue(): int
	{
		return 11;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return self::CODE;
	}
}