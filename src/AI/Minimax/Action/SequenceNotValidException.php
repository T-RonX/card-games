<?php

declare(strict_types=1);

namespace App\AI\Minimax\Action;

use Exception;

class SequenceNotValidException extends Exception
{
	public function __construct(string $sequence_description)
	{
		parent::__construct(sprintf("Sequence '%s' is not valid in the given context.", $sequence_description), null);
	}
}