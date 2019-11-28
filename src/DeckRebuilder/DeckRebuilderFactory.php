<?php

declare(strict_types=1);

namespace App\DeckRebuilder;

use App\DeckRebuilders\DeckRebuilderType;
use Symfony\Component\DependencyInjection\ServiceLocator;

class DeckRebuilderFactory
{
	private ServiceLocator $locator;

	public function __construct(ServiceLocator $locator)
	{
		$this->locator = $locator;
	}

	public function create(DeckRebuilderType $algorithm): DeckRebuilderInterface
	{
		return $this->locator->get($algorithm->getValue());
	}
}