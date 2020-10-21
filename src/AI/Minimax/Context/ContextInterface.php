<?php

declare(strict_types=1);

namespace App\AI\Minimax\Context;

use App\Game\GameInterface;

interface ContextInterface
{
	public function getGame(): GameInterface;
}