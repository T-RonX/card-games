<?php

declare(strict_types=1);

namespace App\AI\Minimax\Context;

interface ContextClonerInterface
{
	public function cloneContext(ContextInterface $context): ContextInterface;

	public function supports(string $name): bool;
}
