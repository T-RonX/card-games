<?php

namespace App\Cards\Standard\Rank;

use App\Deck\Card\Rank\RankInterface;

class Queen implements RankInterface
{
	/**
	 * @var int
	 */
	public const CODE = 'Q';

	/**
	 * @return mixed
	 */
	public function getValue(): int
	{
		return 12;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return self::CODE;
	}
}