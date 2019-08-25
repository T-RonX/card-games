<?php

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
	private $players = [];

	/**
	 * @var int
	 */
	private $pointer = 0;

	/**
	 * @return PlayerInterface[]
	 */
	public function getPlayers(): array
	{
		return $this->players;
	}

	/**
	 * @return PlayerInterface
	 *
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
	 * @param PlayerInterface $player
	 *
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

	/**
	 * @return PlayerInterface|null
	 */
	public function getCurrentPlayer(): ?PlayerInterface
	{
		if (!$this->valid())
		{
			$this->rewind();
		}

		return $this->current();
	}

	/**
	 * @param string $id
	 *
	 * @return PlayerInterface|null
	 */
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

	/**
	 * @return PlayerInterface|null
	 */
	public function getRandomPlayer(): ?PlayerInterface
	{
		$key = max(0, rand(0, $this->count() - 1));

		return array_key_exists($key, $this->players) ? $this->players[$key] : null;
	}

	/**
	 * Sets the next player.
	 */
	public function nextPayer(): void
	{
		$this->pointer = $this->getNextPointer();
	}

	/**
	 * @param PlayerInterface $player
	 *
	 * @return bool
	 */
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
	 * @param bool $start_at_next_player
	 *
	 * @return Iterator|PlayerInterface[]
	 */
	public function getContinueLoopIterator(bool $start_at_next_player = false): Iterator
	{
		return new ContinueLoopIterator($this->players, $start_at_next_player ? $this->getNextPointer() : $this->pointer);
	}

	/**
	 * @param PlayerInterface|null $starting_player
	 *
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

	/**
	 * @return int
	 */
	private function getNextPointer(): int
	{
		return array_key_exists($this->pointer + 1, $this->players) ? $this->pointer + 1 : 0;
	}

	/**
	 * @param PlayerInterface $player
	 */
	public function addPlayer(PlayerInterface $player)
	{
		$this->players[] = $player;
	}

	/**
	 * @param PlayerInterface[] $players
	 */
	public function setPlayers(array $players)
	{
		$this->players = array_values($players);
	}

	/**
	 * @inheritDoc
	*/
	public function current()
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

	/**
	 * @inheritDoc
	 */
	public function next()
	{
		++$this->pointer;
	}

	/**
	 * Previous player
	 */
	public function prev()
	{
		--$this->pointer;
	}

	/**
	 * @inheritDoc
	 */
	public function key()
	{
		return $this->pointer;
	}

	/**
	 * @inheritDoc
	 */
	public function valid()
	{
		return array_key_exists($this->pointer, $this->players);
	}

	/**
	 * @inheritDoc
	 */
	public function rewind()
	{
		$this->pointer = 0;
	}

	/**
	 * @inheritDoc
	 */
	public function count()
	{
		return count($this->players);
	}
}