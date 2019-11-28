<?php

declare(strict_types=1);

namespace App\Games\Duizenden\StateCompiler\Action;

use App\Games\Duizenden\StateCompiler\AbstractAction;
use App\Games\Duizenden\StateCompiler\ActionType;

class DiscardEndTurnAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::DISCARD_END_TURN(), 'Discard end turn', 'Last card discarded, turn ended.');
	}
}
