<?php

declare(strict_types=1);

namespace App\Games\Duizenden\AI\ActionGenerator;

use App\AI\Minimax\Action\ActionSequence;
use App\Games\Duizenden\Actions\ActionType;
use App\Games\Duizenden\AI\Context\Context;
use Generator;

interface SequenceCalculatorInterface
{
	/**
	 * @return ActionSequence[]|Generator
	 */
	public function getActionSequences(Context $context): Generator;

	/**
	 * @param ActionType[] $actions
	 */
	public function supports(array $actions): bool;
}