<?php

namespace App\Games\Duizenden;

use App\DeckRebuilder\DeckRebuilderInterface;
use App\Game\GameInterface;
use App\Games\Duizenden\Initializer\Exception\InvalidDealerPlayerException;
use App\Games\Duizenden\Initializer\Initializer;
use App\Games\Duizenden\Player\Exception\EmptyPlayerSetException;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Score\ScoreCalculator;
use App\Games\Duizenden\Workflow\TransitionType;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\StateMachine;

class Game implements GameInterface
{
	/**
	 * @var string
	 */
	public const NAME = 'duizenden';

	/**
	 * @var State
	 */
	private $state;

	/**
	 * @var DeckRebuilderInterface
	 */
	private $deck_rebuilder;

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var Initializer
	 */
	private $initializer;

	/**
	 * @var ScoreCalculator
	 */
	private $score_calculator;

	/**
	 * @var StateMachine
	 */
	private $state_machine;

	/**
	 * @param Initializer $initializer
	 * @param ScoreCalculator $score_calculator
	 * @param StateMachine $state_machine
	 */
	public function __construct(
		Initializer $initializer,
		ScoreCalculator $score_calculator,
		StateMachine $state_machine
	)
	{
		$this->initializer = $initializer;
		$this->score_calculator = $score_calculator;
		$this->state_machine = $state_machine;
	}

	/**
	 * @param Configurator $configurator
	 *
	 * @throws InvalidDealerPlayerException
	 * @throws EmptyPlayerSetException
	 */
	public function configure(Configurator $configurator): void
	{
		$this->state = $this->initializer->createInitialState($configurator);
		$this->deck_rebuilder = $this->initializer->getDeckRebuilderFromConfig($configurator);

		$this->state_machine->apply($this, TransitionType::CONFIGURE()->getValue());
	}

	public function getGamePlayerById(string $id): PlayerInterface
	{
		return $this->state->getPlayers()->getPlayerById($id);
	}

	/**
	 * @return string
	 */
	public function getId(): ?string
	{
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function setId(string $id): void
	{
		$this->id = $id;
	}

	/**
	 * @return ScoreCalculator
	 */
	public function getScoreCalculator(): ScoreCalculator
	{
		return $this->score_calculator;
	}

	/**
	 * @param State $state
	 */
	public function setState(State $state): void
	{
		$this->state = $state;
	}

	/**
	 * @return State
	 */
	public function getState(): State
	{
		return $this->state;
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): string
	{
		return self::NAME;
	}

	/**
	 * @return void
	 */
	public function setCreatedMarking(): void
	{
		$this->state_machine->getMarkingStore()->setMarking($this, $this->getMarking());
	}

	public function getMarking(): Marking
	{
		return $this->state_machine->getMarking($this);
	}

	/**
	 * @return DeckRebuilderInterface
	 */
	public function getDeckRebuilder(): DeckRebuilderInterface
	{
		return $this->deck_rebuilder;
	}

	/**
	 * @param DeckRebuilderInterface $deck_rebuilder
	 */
	public function setDeckRebuilder(DeckRebuilderInterface $deck_rebuilder): void
	{
		$this->deck_rebuilder = $deck_rebuilder;
	}
}
