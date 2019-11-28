<?php

declare(strict_types=1);

namespace App\Cards\Standard\Color;

use App\Deck\Card\Color\ColorInterface;

class Blue implements ColorInterface
{
	const CODE = 'B';

	public function getHex(): string
	{
		return '0000ff';
	}

	function getName(): string
	{
		return 'blue';
	}

	function getNameShort(): string
	{
		return self::CODE;
	}
}