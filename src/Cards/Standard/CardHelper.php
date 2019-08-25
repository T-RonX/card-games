<?php

namespace App\Cards\Standard;

use App\Cards\Standard\Color\Black;
use App\Cards\Standard\Color\Red;
use App\Cards\Standard\Exception\InvalidCardIdException;
use App\Cards\Standard\Rank\Ace;
use App\Cards\Standard\Rank\Jack;
use App\Cards\Standard\Rank\King;
use App\Cards\Standard\Rank\Queen;
use App\Cards\Standard\Rank\Rank_0;
use App\Cards\Standard\Rank\Rank_1;
use App\Cards\Standard\Rank\Rank_10;
use App\Cards\Standard\Rank\Rank_2;
use App\Cards\Standard\Rank\Rank_3;
use App\Cards\Standard\Rank\Rank_4;
use App\Cards\Standard\Rank\Rank_5;
use App\Cards\Standard\Rank\Rank_6;
use App\Cards\Standard\Rank\Rank_7;
use App\Cards\Standard\Rank\Rank_8;
use App\Cards\Standard\Rank\Rank_9;
use App\Cards\Standard\Suit\Clubs;
use App\Cards\Standard\Suit\Diamonds;
use App\Cards\Standard\Suit\Harts;
use App\Cards\Standard\Suit\JokerBlack;
use App\Cards\Standard\Suit\JokerRed;
use App\Cards\Standard\Suit\Spades;
use App\Deck\Card\Rank\RankInterface;
use App\Deck\Card\Suit\SuitInterface;

class CardHelper
{
	/**
	 * @param string $id
	 *
	 * @return Card
	 *
	 * @throws InvalidCardIdException
	 */
	public static function createCardFromId(string $id)
	{
		if (!preg_match('/^([SHDCXY]{1})([0-9]{1,2}|[JQKA]{1})$/', $id, $matches))
		{
			throw new InvalidCardIdException(sprintf("Can not create card, id '%s' is not valid.", $id));
		}

		$suit = self::createSuitByCode($matches[1]);
		$rank = self::createRankByValue($matches[2]);

		return new Card($suit, $rank);
	}

	/**
	 * @param string $code
	 *
	 * @return RankInterface
	 *
	 * @throws InvalidCardIdException
	 */
	private static function createRankByValue(string $code): RankInterface
	{
		switch ($code)
		{
			case Rank_0::CODE;
				return self::createRank0();

			case Rank_1::CODE;
				return self::createRank1();

			case Rank_2::CODE;
				return self::createRank2();
			case Rank_3::CODE;
				return self::createRank3();

			case Rank_4::CODE;
				return self::createRank4();

			case Rank_5::CODE;
				return self::createRank5();

			case Rank_6::CODE;
				return self::createRank6();

			case Rank_7::CODE;
				return self::createRank7();

			case Rank_8::CODE;
				return self::createRank8();

			case Rank_9::CODE;
				return self::createRank9();

			case Rank_10::CODE;
				return self::createRank10();

			case Jack::CODE;
				return self::createRankJack();

			case Queen::CODE;
				return self::createRankQueen();

			case King::CODE;
				return self::createRankKing();

			case Ace::CODE;
				return self::createRankAce();

		}

		throw new InvalidCardIdException(sprintf("Rank with code '%s' is not recognised.", $code));
	}

	/**
	 * @param string $code
	 *
	 * @return SuitInterface
	 *
	 * @throws InvalidCardIdException
	 */
	private static function createSuitByCode(string $code): SuitInterface
	{
		switch ($code)
		{
			case Spades::CODE:
				return self::createSpadesSuit();

			case Harts::CODE:
				return self::createHartsSuit();

			case Diamonds::CODE:
				return self::createDiamondsSuit();

			case Clubs::CODE:
				return self::createClubsSuit();

			case JokerBlack::CODE:
				return self::createJokerBlackSuit();

			case JokerRed::CODE:
				return self::createJokerRedSuit();
		}

		throw new InvalidCardIdException(sprintf("Suit with code '%s' is not recognised.", $code));
	}


	/**
	 * @return Rank_0
	 */
	private static function createRank0(): Rank_0
	{
		static $rank = null;

		if (null === $rank)
		{
			$rank = new Rank_0();
		}

		return $rank;
	}

	/**
	 * @return Rank_1
	 */
	private static function createRank1(): Rank_1
	{
		static $rank = null;

		if (null === $rank)
		{
			$rank = new Rank_1();
		}

		return $rank;
	}

	/**
	 * @return Rank_2
	 */
	private static function createRank2(): Rank_2
	{
		static $rank = null;

		if (null === $rank)
		{
			$rank = new Rank_2();
		}

		return $rank;
	}

	/**
	 * @return Rank_3
	 */
	private static function createRank3(): Rank_3
	{
		static $rank = null;

		if (null === $rank)
		{
			$rank = new Rank_3();
		}

		return $rank;
	}

	/**
	 * @return Rank_4
	 */
	private static function createRank4(): Rank_4
	{
		static $rank = null;

		if (null === $rank)
		{
			$rank = new Rank_4();
		}

		return $rank;
	}

	/**
	 * @return Rank_5
	 */
	private static function createRank5(): Rank_5
	{
		static $rank = null;

		if (null === $rank)
		{
			$rank = new Rank_5();
		}

		return $rank;
	}

	/**
	 * @return Rank_6
	 */
	private static function createRank6(): Rank_6
	{
		static $rank = null;

		if (null === $rank)
		{
			$rank = new Rank_6();
		}

		return $rank;
	}

	/**
	 * @return Rank_7
	 */
	private static function createRank7(): Rank_7
	{
		static $rank = null;

		if (null === $rank)
		{
			$rank = new Rank_7();
		}

		return $rank;
	}

	/**
	 * @return Rank_8
	 */
	private static function createRank8(): Rank_8
	{
		static $rank = null;

		if (null === $rank)
		{
			$rank = new Rank_8();
		}

		return $rank;
	}

	/**
	 * @return Rank_9
	 */
	private static function createRank9(): Rank_9
	{
		static $rank = null;

		if (null === $rank)
		{
			$rank = new Rank_9();
		}

		return $rank;
	}

	/**
	 * @return Rank_10
	 */
	private static function createRank10(): Rank_10
	{
		static $rank = null;

		if (null === $rank)
		{
			$rank = new Rank_10();
		}

		return $rank;
	}

	/**
	 * @return Jack
	 */
	private static function createRankJack(): Jack
	{
		static $rank = null;

		if (null === $rank)
		{
			$rank = new Jack();
		}

		return $rank;
	}

	/**
	 * @return Queen
	 */
	private static function createRankQueen(): Queen
	{
		static $rank = null;

		if (null === $rank)
		{
			$rank = new Queen();
		}

		return $rank;
	}

	/**
	 * @return King
	 */
	private static function createRankKing(): King
	{
		static $rank = null;

		if (null === $rank)
		{
			$rank = new King();
		}

		return $rank;
	}

	/**
	 * @return Ace
	 */
	private static function createRankAce(): Ace
	{
		static $rank = null;

		if (null === $rank)
		{
			$rank = new Ace();
		}

		return $rank;
	}

	/**
	 * @return Spades
	 */
	private static function createSpadesSuit(): Spades
	{
		static $suit = null;

		if (null === $suit)
		{
			$suit = new Spades(self::createBlackColor());
		}

		return $suit;
	}

	/**
	 * @return Harts
	 */
	private static function createHartsSuit(): Harts
	{
		static $suit = null;

		if (null === $suit)
		{
			$suit = new Harts(self::createRedColor());
		}

		return $suit;
	}

	/**
	 * @return Diamonds
	 */
	private static function createDiamondsSuit(): Diamonds
	{
		static $suit = null;

		if (null === $suit)
		{
			$suit = new Diamonds(self::createRedColor());
		}

		return $suit;
	}

	/**
	 * @return Clubs
	 */
	private static function createClubsSuit(): Clubs
	{
		static $suit = null;

		if (null === $suit)
		{
			$suit = new Clubs(self::createBlackColor());
		}

		return $suit;
	}

	/**
	 * @return JokerBlack
	 */
	private static function createJokerBlackSuit(): JokerBlack
	{
		static $suit = null;

		if (null === $suit)
		{
			$suit = new JokerBlack(self::createBlackColor());
		}

		return $suit;
	}

	/**
	 * @return JokerRed
	 */
	private static function createJokerRedSuit(): JokerRed
	{
		static $suit = null;

		if (null === $suit)
		{
			$suit = new JokerRed(self::createRedColor());
		}

		return $suit;
	}

	/**
	 * @return Red
	 */
	private static function createRedColor(): Red
	{
		static $color = null;

		if (null === $color)
		{
			$color = new Red();
		}

		return $color;
	}

	/**
	 * @return Black
	 */
	private static function createBlackColor(): Black
	{
		static $color = null;

		if (null === $color)
		{
			$color = new Black();
		}

		return $color;
	}
}