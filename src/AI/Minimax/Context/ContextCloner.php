<?php

declare(strict_types=1);

namespace App\AI\Minimax\Context;

use RuntimeException;

class ContextCloner
{
	private iterable $cloners;

	/**
	 * @param ContextClonerInterface[] $cloners
	 */
	public function __construct(iterable $cloners)
	{
		$this->cloners = $cloners;
	}

	public function cloneContext(ContextInterface $context): ContextInterface
	{
		$cloner = $this->getContextCloner($context->getGame()->getName());

		return $cloner->cloneContext($context);
	}

	private function getContextCloner(string $name): ContextClonerInterface
	{
		foreach ($this->cloners as $cloner)
		{
			if ($cloner->supports($name))
			{
				return $cloner;
			}
		}

		throw new RuntimeException(sprintf("No context cloner found supporting '%s'.", $name));
	}
}