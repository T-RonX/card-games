<?php

declare(strict_types=1);

namespace App\Deck\Card;

use App\Deck\Card\Color\ColorInterface;
use App\Deck\Card\Rank\RankInterface;
use App\Deck\Card\Suit\SuitInterface;

interface CardInterface
{
	function getIdentifier(): string;

	function getSuit(): SuitInterface;

	function getRank(): RankInterface;

	function getBackColor(): ColorInterface;

	function equals(CardInterface $card): bool;
}