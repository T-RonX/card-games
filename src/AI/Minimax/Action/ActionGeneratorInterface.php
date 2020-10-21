<?php

declare(strict_types=1);

namespace App\AI\Minimax\Action;

use App\AI\Minimax\Context\ContextInterface;

interface ActionGeneratorInterface
{
	public function getGameName(): string;

	public function getActionSequences(): iterable;

	public function setContext(ContextInterface $context): void;
}