<?php

namespace App\Cards\Standard\Color;

use App\Deck\Card\Color\ColorInterface;

class Red implements ColorInterface
{
	const CODE = 'R';

	/**
	 * @inheritDoc
	 */
	public function getHex(): string
	{
		return 'ff0000';
	}

	/**
	 * @inheritDoc
	 */
	function getName(): string
	{
		return 'red';
	}

	function getNameShort(): string
	{
		return self::CODE;
	}
}