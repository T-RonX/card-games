<?php

namespace App\Games\Duizenden\Score;

use App\Cards\Standard\Rank\Ace;
use App\Cards\Standard\Rank\Jack;
use App\Cards\Standard\Rank\King;
use App\Cards\Standard\Rank\Queen;
use App\Cards\Standard\Rank\Rank_10;
use App\Cards\Standard\Rank\Rank_2;
use App\Cards\Standard\Rank\Rank_3;
use App\Cards\Standard\Rank\Rank_4;
use App\Cards\Standard\Rank\Rank_5;
use App\Cards\Standard\Rank\Rank_6;
use App\Cards\Standard\Rank\Rank_7;
use App\Cards\Standard\Rank\Rank_8;
use App\Cards\Standard\Rank\Rank_9;
use App\Cards\Standard\Suit\Joker;
use App\Cards\Standard\Suit\JokerBlack;
use App\Cards\Standard\Suit\JokerRed;
use App\Deck\Card\CardInterface;
use App\Deck\Card\Suit\SuitInterface;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;

class PointMapping
{
	private static $value_map = [
		0 => ['X' => 25, 'Y' => 25],
		2 => ['S' => 5, 'H' => 5, 'D' => 5, 'C' => 5],
		3 => ['S' => 5, 'H' => 5, 'D' => 5, 'C' => 5],
		4 => ['S' => 5, 'H' => 5, 'D' => 5, 'C' => 5],
		5 => ['S' => 5, 'H' => 5, 'D' => 5, 'C' => 5],
		6 => ['S' => 5, 'H' => 5, 'D' => 5, 'C' => 5],
		7 => ['S' => 5, 'H' => 5, 'D' => 5, 'C' => 5],
		8 => ['S' => 5, 'H' => 5, 'D' => 5, 'C' => 5],
		9 => ['S' => 5, 'H' => 5, 'D' => 5, 'C' => 5],
		10 => ['S' => 10, 'H' => 10, 'D' => 10, 'C' => 10],
		11 => ['S' => 10, 'H' => 10, 'D' => 10, 'C' => 10],
		12 => ['S' => 100, 'H' => 10, 'D' => 10, 'C' => 10],
		13 => ['S' => 10, 'H' => 10, 'D' => 10, 'C' => 10],
		14 => ['S' => 20, 'H' => 20, 'D' => 20, 'C' => 20],
		1 => ['S' => 20, 'H' => 20, 'D' => 20, 'C' => 20],
	];

	private static $name_map = [
		0 => ['X' => 25, 'Y' => 25],
		JokerRed::CODE => ['X' => 25, 'Y' => 25],
		JokerBlack::CODE => ['X' => 25, 'Y' => 25],
		Rank_2::CODE => ['S' => 5, 'H' => 5, 'D' => 5, 'C' => 5],
		Rank_3::CODE => ['S' => 5, 'H' => 5, 'D' => 5, 'C' => 5],
		Rank_4::CODE => ['S' => 5, 'H' => 5, 'D' => 5, 'C' => 5],
		Rank_5::CODE => ['S' => 5, 'H' => 5, 'D' => 5, 'C' => 5],
		Rank_6::CODE => ['S' => 5, 'H' => 5, 'D' => 5, 'C' => 5],
		Rank_7::CODE => ['S' => 5, 'H' => 5, 'D' => 5, 'C' => 5],
		Rank_8::CODE => ['S' => 5, 'H' => 5, 'D' => 5, 'C' => 5],
		Rank_9::CODE => ['S' => 5, 'H' => 5, 'D' => 5, 'C' => 5],
		Rank_10::CODE => ['S' => 10, 'H' => 10, 'D' => 10, 'C' => 10],
		Jack::CODE => ['S' => 10, 'H' => 10, 'D' => 10, 'C' => 10],
		Queen::CODE => ['S' => 100, 'H' => 10, 'D' => 10, 'C' => 10],
		King::CODE => ['S' => 10, 'H' => 10, 'D' => 10, 'C' => 10],
		Ace::CODE => ['S' => 20, 'H' => 20, 'D' => 20, 'C' => 20],
		1 => ['S' => 20, 'H' => 20, 'D' => 20, 'C' => 20],
	];

	/**
	 * @param SuitInterface $suit
	 * @param int $rank_value
	 *
	 * @return int
	 */
	public static function getPointsBySuitAndRankValue(SuitInterface $suit, int $rank_value): int
	{
		return self::$value_map[$rank_value][$suit->getName()];
	}

	/**
	 * @param CardInterface $card
	 *
	 * @return int
	 *
	 * @throws UnmappedCardException
	 */
	public static function getPointsByCard(CardInterface $card): int
	{
		if (!isset(self::$value_map[$card->getRank()->getValue()][$card->getSuit()->getName()]))
		{
			throw new UnmappedCardException(sprintf("Card with '%s' was not found in point value mapping.",
				$card->getRank()->getValue() . $card->getSuit()->getName())
			);
		}

		return self::$value_map[$card->getRank()->getValue()][$card->getSuit()->getName()];
	}

	/**
	 * @param string $id
	 *
	 * @return int
	 *
	 * @throws UnmappedCardException
	 */
	public static function getPointsByCardId(string $id): int
	{
		if (
			preg_match('/^([SHDCXY]{1})([0-9]{1,2}|[JQKA]{1})$/', $id, $matches) &&
			isset(self::$name_map[$matches[2]][$matches[1]])
		)
		{
			return self::$name_map[$matches[2]][$matches[1]];
		}

		throw new UnmappedCardException(sprintf("Card with '%s' was not found in point name mapping.", $id));
	}
}