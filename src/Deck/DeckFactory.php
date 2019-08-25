<?php

namespace App\Deck;

use App\Decks\DeckType;
use Symfony\Component\DependencyInjection\ServiceLocator;

class DeckFactory
{
	/**
	 * @var DeckInterface[]|ServiceLocator
	 */
	private $locator;

	/**
	 * @param ServiceLocator $locator
	 */
	public function __construct(ServiceLocator $locator)
	{
		$this->locator = $locator;
	}

	/**
	 * @param DeckType $type
	 *
	 * @return DeckInterface
	 */
	public function create(DeckType $type): DeckInterface
	{
		return $this->locator->get($type->getValue());
	}
}