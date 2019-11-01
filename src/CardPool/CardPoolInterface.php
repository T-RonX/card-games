<?php

namespace App\CardPool;

use App\Deck\Card\CardInterface;
use App\Shuffler\ShufflerInterface;
use Iterator;

interface CardPoolInterface extends Iterator
{
	/**
	 * Clear the card pool
	 */
	function clear(): void;

	/**
	 * @param CardInterface[] $cards
	 */
	function setCards(array $cards): void;

	/**
	 * @param CardInterface[] $cards
	 */
	function addCards(array $cards): void;

	/**
	 * @param CardInterface $card
	 */
	function addCard(CardInterface $card): void;

	/**
	 * @return int
	 */
	function getCardCount(): int;

	/**
	 * @return CardInterface[]
	 */
	function getCards(): array;

	/**
	 * @return CardInterface
	 */
	function drawTopCard(): CardInterface;

	/**
	 * @return CardInterface
	 */
	public function getTopCard(): CardInterface;

	/**
	 * @return CardInterface[]
	 */
	function drawAllCards(): array;

	/**
	 * @param CardInterface $card
	 *
	 * @return CardInterface
	 */
	public function drawCard(CardInterface $card): CardInterface;

	/**
	 * @return string[]
	 */
	public function getIdentifiers(): array;

	/**
	 * @param ShufflerInterface $shuffler
	 */
	function shuffle(ShufflerInterface $shuffler): void;
}