<?php

declare(strict_types=1);

namespace App\Deck\Card\Suit;

use App\Deck\Card\Color\ColorInterface;

interface SuitInterface
{
	function getSymbol(): string;

	function getName(): string;

	function getColor(): ColorInterface;
}