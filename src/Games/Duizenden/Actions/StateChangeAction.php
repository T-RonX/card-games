<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Actions;

use App\Games\Duizenden\Workflow\MarkingType;
use App\Games\Duizenden\Workflow\PersistingMarkingStore;
use RuntimeException;
use Symfony\Component\Workflow\StateMachine;

abstract class StateChangeAction
{
	protected StateMachine $state_machine;

	public function __construct(StateMachine $state_machine)
	{
		$this->state_machine = $state_machine;
	}

	protected function isState(MarkingType $state): bool
	{
		return $this->state_machine->getMarking($this)->has($state->getValue());
	}

	protected function isSandboxed(): bool //@TODO this is a convenience method. Should should make Sandboxed version of action class instead.
	{
		return $this->getMarkingStore()->isSandboxed();
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
}