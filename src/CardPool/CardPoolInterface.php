<?php

declare(strict_types=1);

namespace App\CardPool;

use App\Deck\Card\CardInterface;
use App\Shuffler\ShufflerInterface;
use Iterator;

interface CardPoolInterface extends Iterator
{
	function clear(): void;

	/**
	 * @param CardInterface[] $cards
	 */
	function setCards(array $cards): void;

	/**
	 * @param CardInterface[] $cards
	 */
	function addCards(array $cards): void;

	function addCard(CardInterface $card, int $target = null): void;

	function getCardCount(): int;

	/**
	 * @return CardInterface[]
	 */
	function getCards(): array;

	function drawTopCard(): CardInterface;

	public function getTopCard(): CardInterface;

	/**
	 * @return CardInterface[]
	 */
	function drawAllCards(): array;

	public function drawCard(CardInterface $card): CardInterface;

	/**
	 * @return string[]
	 */
	public function getIdentifiers(): array;

	function shuffle(ShufflerInterface $shuffler): void;
}