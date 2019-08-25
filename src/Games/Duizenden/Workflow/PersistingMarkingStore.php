<?php

namespace App\Games\Duizenden\Workflow;

use App\Games\Duizenden\Game;
use App\Games\Duizenden\Persistence\Exception\GameNotFoundException;
use App\Games\Duizenden\Persistence\GamePersistence;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;

class PersistingMarkingStore implements MarkingStoreInterface
{
	/**
	 * @var GamePersistence
	 */
	private $game_persistence;

	/**
	 * @var Marking|null
	 */
	private $current_marking;

	/**
	 * @param GamePersistence $game_persistence
	 */
	public function __construct(GamePersistence $game_persistence)
	{
		$this->game_persistence = $game_persistence;
	}

	/**
	 * @inheritDoc
	 *
	 * @param Game $game
	 *
	 * @return Marking
	 * 
	 * @throws NonUniqueResultException
	 */
	public function getMarking($game)
	{
		return $this->current_marking ?? $this->current_marking = new Marking([$this->game_persistence->getMarking($game) => 1]);
	}

	/**
	 * @inheritDoc
	 *
	 * @param Game $game
	 * @param Marking $marking
	 * @param array $context
	 *
	 * @throws GameNotFoundException
	 * @throws NonUniqueResultException
	 */
	public function setMarking($game, Marking $marking, array $context = [])
	{
		$this->current_marking = $marking;

		$this->game_persistence->persist($game, key($marking->getPlaces()), $context);
	}
}