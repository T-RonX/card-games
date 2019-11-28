<?php

declare(strict_types=1);

namespace App\Cards\Standard\Suit;

use App\Deck\Card\Color\ColorInterface;
use App\Deck\Card\Suit\SuitInterface;

class Suit implements SuitInterface
{
	private ColorInterface $color;

	private string $symbol;

	private string $name;

	public function __construct(ColorInterface $color, string $symbol, string $name)
	{
		$this->color = $color;
		$this->symbol = $symbol;
		$this->name = $name;
	}

	function getSymbol(): string
	{
		return $this->symbol;
	}

	function getName(): string
	{
		return $this->name;
	}

	function getColor(): ColorInterface
	{
		return $this->color;
	}
}