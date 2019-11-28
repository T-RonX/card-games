<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Player;

use App\Games\Duizenden\Player\Exception\EmptyPlayerSetException;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Player\Iterator\FreshLoopIterator;
use App\Games\Duizenden\Player\Iterator\ContinueLoopIterator;
use App\Games\Duizenden\Player\Iterator\InfiniteLoopIterator;
use Countable;
use Iterator;

class Players implements Iterator, Countable
{
	/**
	 * @var PlayerInterface[]
	 */
	private array $players = [];

	private int $pointer = 0;

	/**
	 * @return PlayerInterface[]
	 */
	public function getPlayers(): array
	{
		return $this->players;
	}

	/**
	 * @throws EmptyPlayerSetException
	 */
	public function getFirstPlayer(): PlayerInterface
	{
		if (!$this->count())
		{
			throw new EmptyPlayerSetException("Can not get first player, there are no players set.");
		}

		return $this->players[0];
	}

	/**
	 * @throws PlayerNotFoundException
	 */
	public function setCurrentPlayer(PlayerInterface $player): void
	{
		for ($i = 0; $i < count($this); ++$i)
		{
			if ($this->players[$i]->equals($player))
			{
				$this->pointer = $i;
				return;
			}
		}

		throw new PlayerNotFoundException(sprintf("Player with id '%s' was not found.", $player->getId()));
	}

	public function getCurrentPlayer(): ?PlayerInterface
	{
		if (!$this->valid())
		{
			$this->rewind();
		}

		return $this->current();
	}

	public function getPlayerById(string $id): ?PlayerInterface
	{
		foreach ($this->players as $player)
		{
			if ($id === $player->getId())
			{
				return $player;
			}
		}

		return null;
	}

	public function getRandomPlayer(): ?PlayerInterface
	{
		$key = max(0, rand(0, $this->count() - 1));

		return array_key_exists($key, $this->players) ? $this->players[$key] : null;
	}

	public function nextPayer(): void
	{
		$this->pointer = $this->getNextPointer();
	}

	public function has(PlayerInterface $player): bool
	{
		foreach ($this->players as $p)
		{
			if ($p->equals($player))
			{
				return true;
			}
		}

		return false;
	}

	public function hasId(string $player_id): bool
	{
		foreach ($this->players as $p)
		{
			if ($player_id === $p->getId())
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * @return Iterator|PlayerInterface[]
	 */
	public function getContinueLoopIterator(bool $start_at_next_player = false): Iterator
	{
		return new ContinueLoopIterator($this->players, $start_at_next_player ? $this->getNextPointer() : $this->pointer);
	}

	/**
	 * @return Iterator|PlayerInterface[]
	 */
	public function getInfiniteLoopIterator(PlayerInterface $starting_player = null): Iterator
	{
		$pointer = 0;

		if ($starting_player)
		{
			foreach ($this->getFreshLoopIterator() as $key => $player)
			{
				if ($player->equals($starting_player))
				{
					$pointer = $key;
					break;
				}
			}
		}

		return new InfiniteLoopIterator($this->players, $pointer);
	}

	/**
	 * @return Iterator|PlayerInterface[]
	 */
	public function getFreshLoopIterator(): Iterator
	{
		return new FreshLoopIterator($this->players);
	}

	private function getNextPointer(): int
	{
		return array_key_exists($this->pointer + 1, $this->players) ? $this->pointer + 1 : 0;
	}

	public function addPlayer(PlayerInterface $player): void
	{
		$this->players[] = $player;
	}

	/**
	 * @param PlayerInterface[] $players
	 */
	public function setPlayers(array $players): void
	{
		$this->players = array_values($players);
	}

	public function current(): PlayerInterface
	{
		return $this->players[$this->pointer];
	}

	/**
	 * Empties the hand card pool for all players.
	 */
	public function resetCards(): void
	{
		foreach ($this->players as $player)
		{
			$player->getHand()->clear();
			$player->getMelds()->clear();
		}
	}

	public function next(): void
	{
		++$this->pointer;
	}

	public function prev(): void
	{
		--$this->pointer;
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

	public function count(): int
	{
		return count($this->players);
	}
}