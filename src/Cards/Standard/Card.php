<?php

namespace App\Cards\Standard;

use App\Deck\Card\CardInterface;
use App\Deck\Card\Rank\RankInterface;
use App\Deck\Card\Suit\SuitInterface;

class Card implements CardInterface
{
	/**
	 * @var SuitInterface
	 */
	private $suit;
	/**
	 * @var RankInterface
	 */
	private $rank;

	/**
	 * Card constructor.
	 * @param SuitInterface $suit
	 * @param RankInterface $rank
	 */
	public function __construct(SuitInterface $suit, RankInterface $rank)
	{
		$this->suit = $suit;
		$this->rank = $rank;
	}

	/**
	 * @return SuitInterface
	 */
	public function getSuit(): SuitInterface
	{
		return $this->suit;
	}

	/**
	 * @return RankInterface
	 */
	public function getRank(): RankInterface
	{
		return $this->rank;
	}

	/**
	 * @inheritDoc
	 */
	public function equals(CardInterface $card): bool
	{
		return
			$card->getSuit() instanceof $this->suit &&
			$card->getRank() instanceof $this->rank;
	}
}