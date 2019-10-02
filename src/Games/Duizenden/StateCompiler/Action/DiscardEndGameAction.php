<?php

namespace App\Games\Duizenden\StateCompiler\Action;

use App\Games\Duizenden\StateCompiler\AbstractAction;
use App\Games\Duizenden\StateCompiler\ActionType;

class DiscardEndGameAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::DISCARD_END_GAME(), 'Discard end game', 'Last card discarded, game finished.');
	}
}
