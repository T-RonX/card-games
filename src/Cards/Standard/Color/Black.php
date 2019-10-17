<?php

namespace App\Cards\Standard\Color;

use App\Deck\Card\Color\ColorInterface;

class Black implements ColorInterface
{
	const CODE = 'K';

	/**
	 * @inheritDoc
	 */
	public function getHex(): string
	{
		return '000000';
	}

	/**
	 * @inheritDoc
	 */
	function getName(): string
	{
		return 'black';
	}

	function getNameShort(): string
	{
		return self::CODE;
	}
}