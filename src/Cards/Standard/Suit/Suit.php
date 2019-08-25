<?php

namespace App\Cards\Standard\Suit;

use App\Deck\Card\Color\ColorInterface;
use App\Deck\Card\Suit\SuitInterface;

class Suit implements SuitInterface
{
	/**
	 * @var ColorInterface
	 */
	private $color;
	/**
	 * @var string
	 */
	private $symbol;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @param ColorInterface $color
	 * @param string $symbol
	 * @param string $name
	 */
	public function __construct(ColorInterface $color, string $symbol, string $name)
	{
		$this->color = $color;
		$this->symbol = $symbol;
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	function getSymbol(): string
	{
		return $this->symbol;
	}

	/**
	 * @return string
	 */
	function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return ColorInterface
	 */
	function getColor(): ColorInterface
	{
		return $this->color;
	}
}