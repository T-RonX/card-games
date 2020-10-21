<?php

declare(strict_types=1);

namespace App\AI\Minimax\Action;

use App\AI\Minimax\Context\ContextCloner;
use App\AI\Minimax\Context\ContextInterface;
use App\AI\Minimax\State\PossibleState;
use App\AI\Minimax\State\PossibleStateFactory;
use App\Games\Duizenden\Workflow\PersistingMarkingStore;
use RuntimeException;
use Symfony\Component\Workflow\StateMachine;

class ActionSequence
{
	/**
	 * @var Action[]
	 */
	private array $actions;
	private ContextInterface $initial_context;
	private PossibleStateFactory $possible_state_factory;
	private PossibleState $resulting_state;
	private ContextCloner $context_cloner;
	/**
	 * @var StateMachine
	 */
	private StateMachine $state_machine;

	/**
	 * @param Action[] $actions
	 */
	public function __construct(
		StateMachine $state_machine,
		PossibleStateFactory $possible_state_factory,
		ContextCloner $context_cloner,
		ContextInterface $context,
		array $actions
	)
	{
		$this->state_machine = $state_machine;
		$this->possible_state_factory = $possible_state_factory;
		$this->context_cloner = $context_cloner;
		$this->initial_context = $context;
		$this->actions = $actions;
	}

	public function executeSequence(): void
	{
		foreach ($this->actions as $action)
		{
			$action->runAndGetNewContext($this->initial_context);
		}
	}

	public function executeSequenceInSandbox(): void
	{
		$this->getMarkingStore()->sandbox(fn() => $this->runSequenceSandboxed());
	}

	private function runSequenceSandboxed(): void
	{
		$new_context = $this->initial_context;
		$last_action = null;

		foreach ($this->actions as $action)
		{
			$cloned_context = $this->context_cloner->cloneContext($new_context);
			$new_context = $action->runAndGetNewContext($cloned_context);
			$last_action = $action;
		}

		$is_last_action = $last_action !== null && $last_action->isIsFinalAction();

		$this->resulting_state = $this->possible_state_factory->create($new_context, $is_last_action);
	}

	private function getMarkingStore(): PersistingMarkingStore
	{
		$marking_store = $this->state_machine->getMarkingStore();

		if (!$marking_store instanceof PersistingMarkingStore)
		{
			throw new RuntimeException("Unexpected type of marking store returned.");
		}

		return $marking_store;
	}

	public function getResultingState(): PossibleState
	{
		return $this->resulting_state;
	}
}
