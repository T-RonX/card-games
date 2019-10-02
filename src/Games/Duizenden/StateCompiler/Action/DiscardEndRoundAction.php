<?php

namespace App\Games\Duizenden\StateCompiler\Action;

use App\Games\Duizenden\StateCompiler\AbstractAction;
use App\Games\Duizenden\StateCompiler\ActionType;

class DiscardEndRoundAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::DISCARD_END_ROUND(), 'Discard round end', 'Last card discarded, round finished.');
	}
}
