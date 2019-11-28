<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Player\Iterator;

use App\Games\Duizenden\Player\PlayerInterface;
use Iterator;

class FreshLoopIterator implements Iterator
{
	/**
	 * @var PlayerInterface[]
	 */
	private array $players;

	private int $pointer = 0;

	/**
	 * @param PlayerInterface[] $players
	 */
	public function __construct(array &$players)
	{
		$this->players = $players;
	}

	public function current(): PlayerInterface
	{
		return $this->players[$this->pointer];
	}

	public function next(): void
	{
		++$this->pointer;
	}

	public function key(): int
	{
		return $this->pointer;
	}

	public function valid(): bool
	{
		return array_key_exists($this->pointer, $this->players);
	}

	public function rewind(): void
	{
		$this->pointer = 0;
	}
}
