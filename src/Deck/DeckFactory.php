<?php

declare(strict_types=1);

namespace App\Deck;

use App\Decks\DeckType;
use Symfony\Component\DependencyInjection\ServiceLocator;

class DeckFactory
{
	private ServiceLocator $locator;

	public function __construct(ServiceLocator $locator)
	{
		$this->locator = $locator;
	}

	public function create(DeckType $type): DeckInterface
	{
		return $this->locator->get($type->getValue());
	}
}