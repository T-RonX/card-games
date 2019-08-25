<?php

namespace App\Deck\Card\Rank;

interface RankInterface
{
	/**
	 * @return int
	 */
	function getValue(): int;

	/**
	 * @return string
	 */
	function getName(): string;
}
