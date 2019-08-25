<?php

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class King implements RankInterface
{
	/**
	 * @var int
	 */
	public const CODE = 'K';

	/**
	 * @return mixed
	 */
	public function getValue(): int
	{
		return 13;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return self::CODE;
	}
}