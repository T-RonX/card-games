<?php

declare(strict_types=1);

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
use App\Cards\Standard\Suit\JokerBlack;
use App\Cards\Standard\Suit\JokerRed;
use App\Cards\Standard\Suit\Spades;
use App\Deck\Card\CardInterface;
use App\Deck\Card\Color\ColorInterface;
use App\Deck\DeckInterface;

class Deck implements DeckInterface
{
	/**
	 * @var CardInterface[]
	 */
	private array $cards;
	
	public function __construct(ColorInterface $back_color)
	{
		$this->initialize($back_color);
	}

	private function initialize(ColorInterface $back): void
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

		$this->cards[] = new Card($back, $spades, $rank_2);
		$this->cards[] = new Card($back, $spades, $rank_3);
		$this->cards[] = new Card($back, $spades, $rank_4);
		$this->cards[] = new Card($back, $spades, $rank_5);
		$this->cards[] = new Card($back, $spades, $rank_6);
		$this->cards[] = new Card($back, $spades, $rank_7);
		$this->cards[] = new Card($back, $spades, $rank_8);
		$this->cards[] = new Card($back, $spades, $rank_9);
		$this->cards[] = new Card($back, $spades, $rank_10);
		$this->cards[] = new Card($back, $spades, $jack);
		$this->cards[] = new Card($back, $spades, $queen);
		$this->cards[] = new Card($back, $spades, $king);
		$this->cards[] = new Card($back, $spades, $ace);

		$this->cards[] = new Card($back, $harts, $rank_2);
		$this->cards[] = new Card($back, $harts, $rank_3);
		$this->cards[] = new Card($back, $harts, $rank_4);
		$this->cards[] = new Card($back, $harts, $rank_5);
		$this->cards[] = new Card($back, $harts, $rank_6);
		$this->cards[] = new Card($back, $harts, $rank_7);
		$this->cards[] = new Card($back, $harts, $rank_8);
		$this->cards[] = new Card($back, $harts, $rank_9);
		$this->cards[] = new Card($back, $harts, $rank_10);
		$this->cards[] = new Card($back, $harts, $jack);
		$this->cards[] = new Card($back, $harts, $queen);
		$this->cards[] = new Card($back, $harts, $king);
		$this->cards[] = new Card($back, $harts, $ace);

		$this->cards[] = new Card($back, $diamonds, $rank_2);
		$this->cards[] = new Card($back, $diamonds, $rank_3);
		$this->cards[] = new Card($back, $diamonds, $rank_4);
		$this->cards[] = new Card($back, $diamonds, $rank_5);
		$this->cards[] = new Card($back, $diamonds, $rank_6);
		$this->cards[] = new Card($back, $diamonds, $rank_7);
		$this->cards[] = new Card($back, $diamonds, $rank_8);
		$this->cards[] = new Card($back, $diamonds, $rank_9);
		$this->cards[] = new Card($back, $diamonds, $rank_10);
		$this->cards[] = new Card($back, $diamonds, $jack);
		$this->cards[] = new Card($back, $diamonds, $queen);
		$this->cards[] = new Card($back, $diamonds, $king);
		$this->cards[] = new Card($back, $diamonds, $ace);

		$this->cards[] = new Card($back, $clubs, $rank_2);
		$this->cards[] = new Card($back, $clubs, $rank_3);
		$this->cards[] = new Card($back, $clubs, $rank_4);
		$this->cards[] = new Card($back, $clubs, $rank_5);
		$this->cards[] = new Card($back, $clubs, $rank_6);
		$this->cards[] = new Card($back, $clubs, $rank_7);
		$this->cards[] = new Card($back, $clubs, $rank_8);
		$this->cards[] = new Card($back, $clubs, $rank_9);
		$this->cards[] = new Card($back, $clubs, $rank_10);
		$this->cards[] = new Card($back, $clubs, $jack);
		$this->cards[] = new Card($back, $clubs, $queen);
		$this->cards[] = new Card($back, $clubs, $king);
		$this->cards[] = new Card($back, $clubs, $ace);

		$this->cards[] = new Card($back, $joker_black, $rank_0);
		$this->cards[] = new Card($back, $joker_red, $rank_0);
	}

	/**
	 * @return CardInterface[]
	 */
	public function getCards(): array
	{
		return $this->cards;
	}

	private function clear(): void
	{
		$this->cards = [];
	}
}