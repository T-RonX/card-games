<?php

declare(strict_types=1);

namespace App\Games\Duizenden\StateCompiler\Action;

use App\Games\Duizenden\StateCompiler\AbstractAction;
use App\Games\Duizenden\StateCompiler\ActionType;

class InvalidFirstMeldAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::INVALID_FIRST_MELD(), 'Invalid first meld', 'First meld did not meet the minimum point required.');
	}
}
