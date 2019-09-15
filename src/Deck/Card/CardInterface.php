<?php

namespace App\Deck\Card;

use App\Deck\Card\Rank\RankInterface;
use App\Deck\Card\Suit\SuitInterface;

interface CardInterface
{
	/**
	 * @return string
	 */
	function getIdentifier(): string;

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
	function equals(CardInterface $card): bool;
}