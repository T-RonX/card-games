<?php

declare(strict_types=1);

namespace App\AI\Minimax\Action;

use App\AI\Minimax\Context\ContextInterface;
use Generator;

interface ActionGeneratorInterface
{
	public function getGameName(): string;

	/**
	 * @return ActionSequence[]|Generator
	 */
	public function getActionSequences(ContextInterface $context): Generator;
}