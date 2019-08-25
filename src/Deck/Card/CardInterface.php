<?php

namespace App\Deck\Card;

use App\Deck\Card\Rank\RankInterface;
use App\Deck\Card\Suit\SuitInterface;

interface CardInterface
{
	/**
	 * @return SuitInterface
	 */
	function getSuit(): SuitInterface;

	/**
	 * @return RankInterface
	 */
	function getRank(): RankInterface;

	/**
	 * @param CardInterface $card
	 *
	 * @return bool
	 */
	public function equals(CardInterface $card): bool;
}