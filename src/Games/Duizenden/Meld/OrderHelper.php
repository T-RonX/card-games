<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Meld;

use App\Cards\Standard\Card;
use App\Cards\Standard\Rank\Ace;
use App\Cards\Standard\Rank\Rank_1;
use App\Cards\Standard\Rank\Rank_2;
use App\Cards\Standard\Suit\Joker;
use App\Deck\Card\CardInterface;
use App\Games\Duizenden\Meld\Exception\MeldException;
use App\Games\Duizenden\Score\PointMapping;

class OrderHelper
{
	/**
	 * @param CardInterface[] $cards
	 *
	 * @throws MeldException
	 */
	public static function orderCards(array &$cards): void
	{
		usort($cards, [self::class, 'sort']);

		$jokers = self::stripJokers($cards);
		self::placeJokers($cards, $jokers);

		if (self::requireShiftAce($cards))
		{
			self::shiftAce($cards);
		}
	}

	/**
	 * @param CardInterface[] $cards
	 *
	 * @return CardInterface[]
	 */
	private static function stripJokers(array &$cards): array
	{
		$jokers = [];

		foreach ($cards as $index => $card)
		{
			if ($card->getSuit() instanceof Joker)
			{
				$jokers[] = $cards[$index];
				unset($cards[$index]);
			}
		}

		$cards = array_values($cards);

		return $jokers;
	}

	/**
	 * @param CardInterface[] $cards
	 * @param CardInterface[] $jokers
	 *
	 * @throws MeldException
	 */
	private static function placeJokers(array &$cards, array &$jokers): void
	{
		if (!$cards)
		{
			$cards = $jokers;

			return;
		}

		$gaps_ace_last = self::findGapsForJokerAceLast($cards);
		$gaps_ace_first = self::findGapsForJokerAceFirst($cards);

		if (count($gaps_ace_last) < count($gaps_ace_first))
		{
			$gaps = $gaps_ace_last;
		}
		else
		{
			$gaps = $gaps_ace_first;

			if ($cards[count($cards) - 1]->getRank() instanceof Ace)
			{
				$ace = array_pop($cards);
				array_unshift($cards, new Card($ace->getBackColor(), $ace->getSuit(), new Rank_1()));
			}
		}

		if (count($gaps) > count($jokers))
		{
			throw new MeldException("Can not create consecutive meld, too few jokers.");
		}

		foreach ($gaps as $index)
		{
			$card_count = count($cards);

			for ($i = $card_count - 1; $i >= 0; --$i)
			{
				if ($i === $index)
				{
					array_splice($cards, $index, 0, [$jokers[0]]);
					unset($jokers[count($jokers) - 1]);
					break;
				}
			}
		}

		while (count($jokers))
		{
			self::findRemainingJokerFittingPlaces($cards, $jokers);
		}
	}

	/**
	 * @param CardInterface[] $cards
	 * @param CardInterface[] $jokers
	 *
	 * @throws MeldException
	 */
	private static function findRemainingJokerFittingPlaces(array &$cards, array &$jokers): void
	{
		[$rank_low, $suit_low] = self::getRankAndSuitOfFirstCard($cards);
		[$rank_high, $suit_high] = self::getRankAndSuitOfLastCard($cards);

		$is_set = $rank_low === $rank_high;

		$fits_at_start = $rank_low !== 14 && $rank_low !== 1;
		$fits_at_end = $rank_high < 14 || $is_set;

		if ($fits_at_start && $fits_at_end)
		{
			$replace_low = $rank_low == 2 ? 14 : $rank_low - 1;
			$replace_high = $rank_high + 1;

			$points_low = PointMapping::getPointsBySuitAndRankValue($suit_low, $replace_low);
			$points_high = PointMapping::getPointsBySuitAndRankValue($suit_high, $replace_high);

			if ($points_low <= $points_high)
			{
				array_unshift($cards, array_pop($jokers));
			}
			else
			{
				array_push($cards, array_pop($jokers));
			}

			return;
		}
		elseif ($fits_at_start)
		{
			array_unshift($cards, array_pop($jokers));

			return;

		}
		elseif ($fits_at_end)
		{
			array_push($cards, array_pop($jokers));

			return;
		}

		throw new MeldException("Nowhere to put joker in consecutive run.");
	}

	/**
	 * @param CardInterface[] $cards
	 *
	 * @return array
	 */
	private static function getRankAndSuitOfLastCard(array $cards): array
	{
		$last_rank = null;
		$last_suit = null;

		foreach ($cards as $card)
		{
			if ($card->getSuit() instanceof Joker)
			{
				++$last_rank;
			}
			else
			{
				$last_rank = $card->getRank()->getValue();
				$last_suit = $card->getSuit();
			}
		}

		return [$last_rank, $last_suit];
	}

	/**
	 * @param CardInterface[] $cards
	 *
	 * @return array
	 */
	private static function getRankAndSuitOfFirstCard(array $cards): array
	{
		$first_rank = null;
		$first_suit = null;

		foreach (array_reverse($cards) as $card)
		{
			if ($card->getSuit() instanceof Joker)
			{
				--$first_rank;
			}
			else
			{
				$first_rank = $card->getRank()->getValue();
				$first_suit = $card->getSuit();
			}
		}

		return [$first_rank, $first_suit];
	}

	/**
	 * @param CardInterface[] $cards
	 *
	 * @return int[]
	 */
	private static function findGapsForJokerAceLast(array &$cards): array
	{
		if (!$cards)
		{
			return [];
		}

		$prev_value = $cards[0]->getRank()->getValue() - 1;
		$card_count = count($cards);
		$indexs = [];

		for ($i = 0; $i < $card_count; ++$i)
		{
			if ($cards[$i]->getRank()->getValue() > $prev_value + 1)
			{
				$value_prev = $cards[$i - 1]->getRank()->getValue() + 1;
				$value_current = $cards[$i]->getRank()->getValue();

				do
				{
					$indexs[] = $i;
				}
				while(++$value_prev < $value_current);
			}

			$prev_value = $cards[$i]->getRank()->getValue();
		}

		return array_reverse($indexs);
	}

	/**
	 * @param CardInterface[] $cards
	 *
	 * @return int[]
	 *
	 * @todo Make this algorithm nicer. Perhaps getValue() should be game dependant instead of deck dependent.
	 */
	private static function findGapsForJokerAceFirst(array &$cards): array
	{
		if (!$cards)
		{
			return [];
		}

		$card_count = count($cards);
		$indexs = [];
		$ace_replaced = false;
		$ace = null;

		if ($cards[$card_count - 1]->getRank() instanceof Ace)
		{
			$ace_replaced = true;
			$ace = array_pop($cards);
			array_unshift($cards, new Card($ace->getBackColor(), $ace->getSuit(), new Rank_1()));
		}

		$prev_value = $cards[$card_count - 1]->getRank()->getValue() + 1;

		for ($i = $card_count - 1; $i >= 0; --$i)
		{
			if ($cards[$i]->getRank()->getValue() < $prev_value - 1)
			{
				$value_prev = $cards[$i + 1]->getRank()->getValue() - 1;
				$value_current = $cards[$i]->getRank()->getValue();

				do
				{
					$indexs[] = $i + 1;
				}
				while(--$value_prev > $value_current);
			}

			$prev_value = $cards[$i]->getRank()->getValue();
		}

		if ($ace_replaced)
		{
			array_shift($cards);
			array_push($cards, $ace);
		}

		return $indexs;
	}

	/**
	 * @param CardInterface[] $cards
	 *
	 * @return bool
	 */
	private static function requireShiftAce(array &$cards): bool
	{
		$count = count($cards);
		[$rank] = self::getRankAndSuitOfFirstCard($cards);

		return (
			$count > 1 &&
			!self::allCardsAreAce($cards) &&
			$cards[$count - 1]->getRank() instanceof Ace &&
			($cards[0]->getRank() instanceof Rank_2 || $rank == 2)
		);
	}

	/**
	 * @param CardInterface[] $cards
	 *
	 * @return bool
	 */
	private static function allCardsAreAce(array $cards): bool
	{
		foreach ($cards as $card)
		{
			if (!$card->getRank() instanceof Ace)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * @param CardInterface[] $cards
	 */
	private static function shiftAce(array &$cards): void
	{
		array_unshift($cards, array_pop($cards));
	}

	/**
	 * @param CardInterface $card1
	 * @param CardInterface $card2
	 *
	 * @return int
	 */
	private static function sort(CardInterface $card1, CardInterface $card2): int
	{
		return $card1->getRank()->getValue() <=> $card2->getRank()->getValue();
	}
}