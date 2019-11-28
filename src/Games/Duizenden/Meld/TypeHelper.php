<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Meld;

use App\Cards\Standard\Suit\Joker;
use App\Cards\Standard\Suit\JokerBlack;
use App\Cards\Standard\Suit\JokerRed;
use App\Common\Meld\MeldType;
use App\Deck\Card\CardInterface;

class TypeHelper
{
	/**
	 * @param CardInterface[] $cards
	 *
	 * @return MeldType|null
	 */
	public static function detectMeldType(array &$cards): ?MeldType
	{
		if (count($cards) > 2)
		{
			if (self::detectRun($cards))
			{
				return MeldType::RUN();
			}
			elseif (self::detectSet($cards))
			{
				return MeldType::SET();
			}
		}

		return null;
	}

	/**
	 * @param CardInterface[] $cards
	 *
	 * @return bool
	 */
	private static function detectRun(array &$cards): bool
	{
		$first_card_index = self::getFirstCard($cards);
		$value = $cards[$first_card_index]->getRank()->getValue() - ($first_card_index + 1);
		$suit = $cards[$first_card_index]->getSuit()->getName();

		foreach ($cards as $card)
		{
			$current_value = $card->getRank()->getValue();
			$current_suit = $card->getSuit()->getName();

			if (($current_value !== $value + 1 && !($current_value === 0)) || ($current_suit != $suit && !in_array($current_suit, [JokerBlack::CODE, JokerRed::CODE])))
			{
				return false;
			}

			++$value;
		}

		return true;
	}

	/**
	 * @param CardInterface[] $cards
	 *
	 * @return bool
	 */
	private static function detectSet(array &$cards): bool
	{
		$first_card_index = self::getFirstCard($cards);
		$value = $cards[$first_card_index]->getRank()->getValue();

		foreach ($cards as $card)
		{
			$current_value = $card->getRank()->getValue();

			if ($current_value !== $value && ($current_value !== 0))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * @param CardInterface[] $cards
	 *
	 * @return int
	 */
	private static function getFirstCard(array &$cards): int
	{
		$card_count = count($cards);
		$index = 0;

		for ($i = 0; $i < $card_count - 1; ++$i)
		{
			if (!$cards[$i]->getSuit() instanceof Joker)
			{
				$index = $i;
				break;
			}
		}

		return $index;
	}
}