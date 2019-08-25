<?php

namespace App\Deck\Card\Color;

interface ColorInterface
{
	/**
	 * @return string
	 */
	function getHex(): string;

	/**
	 * @return string
	 */
	function getName(): string;
}