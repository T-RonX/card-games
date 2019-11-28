<?php

declare(strict_types=1);

namespace App\Deck\Card\Rank;

interface RankInterface
{
	function getValue(): int;

	function getName(): string;
}
