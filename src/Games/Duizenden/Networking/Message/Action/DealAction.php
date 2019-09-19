<?php

namespace App\Games\Duizenden\Networking\Message\Action;

use App\Games\Duizenden\Networking\Message\AbstractAction;
use App\Games\Duizenden\Networking\Message\ActionType;

class DealAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::DEAL(), 'Deal cards', 'Deal the initial set of cards to all players.');
	}
}
