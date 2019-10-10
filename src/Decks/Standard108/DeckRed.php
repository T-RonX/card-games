<?php

namespace App\Decks\Standard108;

use App\Cards\Standard\Card;
use App\Cards\Standard\Color\Black;
use App\Cards\Standard\Color\Red;
use App\Cards\Standard\Rank\Ace;
use App\Cards\Standard\Rank\Jack;
use App\Cards\Standard\Rank\King;
use App\Cards\Standard\Rank\Queen;
use App\Cards\Standard\Rank\Rank_0;
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
use App\Cards\Standard\Suit\Joker;
use App\Cards\Standard\Suit\JokerBlack;
use App\Cards\Standard\Suit\JokerRed;
use App\Cards\Standard\Suit\Spades;
use App\Deck\Card\CardInterface;
use App\Deck\Card\Color\ColorInterface;
use App\Deck\DeckInterface;

class DeckRed implements DeckInterface
{
	/**
	 * @var CardInterface[]
	 */
	private $cards;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->initialize();
	}

	/**
	 * @inheritDoc
	 */
	private function initialize(): void
	{
		$this->clear();

		$red = new Red();
		$black = new Black();

		$spades = new Spades($black);
		$harts = new Harts($red);
		$diamonds = new Diamonds($red);
		$clubs = new Clubs($black);
		$joker_black = new JokerBlack($black);
		$joker_red = new JokerRed($red);

		$rank_0 = new Rank_0();
		$rank_2 = new Rank_2();
		$rank_3 = new Rank_3();
		$rank_4 = new Rank_4();
		$rank_5 = new Rank_5();
		$rank_6 = new Rank_6();
		$rank_7 = new Rank_7();
		$rank_8 = new Rank_8();
		$rank_9 = new Rank_9();
		$rank_10 = new Rank_10();
		$jack = new Jack();
		$queen = new Queen();
		$king = new King();
		$ace = new Ace();

		$this->cards[] = new Card('s2', $spades, $rank_2);
		$this->cards[] = new Card('s3', $spades, $rank_3);
		$this->cards[] = new Card('s4', $spades, $rank_4);
		$this->cards[] = new Card('s5', $spades, $rank_5);
		$this->cards[] = new Card('s6', $spades, $rank_6);
		$this->cards[] = new Card('s7', $spades, $rank_7);
		$this->cards[] = new Card('s8', $spades, $rank_8);
		$this->cards[] = new Card('s9', $spades, $rank_9);
		$this->cards[] = new Card('s10', $spades, $rank_10);
		$this->cards[] = new Card('sj', $spades, $jack);
		$this->cards[] = new Card('sq', $spades, $queen);
		$this->cards[] = new Card('sk', $spades, $king);
		$this->cards[] = new Card('sa', $spades, $ace);

		$this->cards[] = new Card('h2', $harts, $rank_2);
		$this->cards[] = new Card('h3', $harts, $rank_3);
		$this->cards[] = new Card('h4', $harts, $rank_4);
		$this->cards[] = new Card('h5', $harts, $rank_5);
		$this->cards[] = new Card('h6', $harts, $rank_6);
		$this->cards[] = new Card('h7', $harts, $rank_7);
		$this->cards[] = new Card('h8', $harts, $rank_8);
		$this->cards[] = new Card('h9', $harts, $rank_9);
		$this->cards[] = new Card('h10', $harts, $rank_10);
		$this->cards[] = new Card('hj', $harts, $jack);
		$this->cards[] = new Card('hq', $harts, $queen);
		$this->cards[] = new Card('hk', $harts, $king);
		$this->cards[] = new Card('ha', $harts, $ace);

		$this->cards[] = new Card('d2', $diamonds, $rank_2);
		$this->cards[] = new Card('d3', $diamonds, $rank_3);
		$this->cards[] = new Card('d4', $diamonds, $rank_4);
		$this->cards[] = new Card('d5', $diamonds, $rank_5);
		$this->cards[] = new Card('d6', $diamonds, $rank_6);
		$this->cards[] = new Card('d7', $diamonds, $rank_7);
		$this->cards[] = new Card('d8', $diamonds, $rank_8);
		$this->cards[] = new Card('d9', $diamonds, $rank_9);
		$this->cards[] = new Card('d10', $diamonds, $rank_10);
		$this->cards[] = new Card('dj', $diamonds, $jack);
		$this->cards[] = new Card('dq', $diamonds, $queen);
		$this->cards[] = new Card('dk', $diamonds, $king);
		$this->cards[] = new Card('da', $diamonds, $ace);

		$this->cards[] = new Card('c2', $clubs, $rank_2);
		$this->cards[] = new Card('c3', $clubs, $rank_3);
		$this->cards[] = new Card('c4', $clubs, $rank_4);
		$this->cards[] = new Card('c5', $clubs, $rank_5);
		$this->cards[] = new Card('c6', $clubs, $rank_6);
		$this->cards[] = new Card('c7', $clubs, $rank_7);
		$this->cards[] = new Card('c8', $clubs, $rank_8);
		$this->cards[] = new Card('c9', $clubs, $rank_9);
		$this->cards[] = new Card('c10', $clubs, $rank_10);
		$this->cards[] = new Card('cj', $clubs, $jack);
		$this->cards[] = new Card('cq', $clubs, $queen);
		$this->cards[] = new Card('ck', $clubs, $king);
		$this->cards[] = new Card('ca', $clubs, $ace);

		$this->cards[] = new Card('x0', $joker_black, $rank_0);
		$this->cards[] = new Card('y0', $joker_red, $rank_0);
	}

	/**
	 * @inheritDoc
	 */
	public function getCards(): array
	{
		return $this->cards;
	}

	/**
	 * @inheritDoc
	 */
	public function getBackImage(): ColorInterface
	{
		return new Red();
	}

	/**
	 * Clears the cards in the deck.
	 */
	private function clear(): void
	{
		$this->cards = [];
	}
}