<?php

declare(strict_types=1);

namespace App\Games\Duizenden\StateCompiler\Action;

use App\Games\Duizenden\StateCompiler\AbstractAction;
use App\Games\Duizenden\StateCompiler\ActionType;

class DealAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::DEAL(), 'Deal cards', 'Deal the initial set of cards to all players.');
	}
}
