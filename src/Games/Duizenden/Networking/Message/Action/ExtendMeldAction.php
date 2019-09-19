<?php

namespace App\Games\Duizenden\Networking\Message\Action;

use App\Games\Duizenden\Networking\Message\AbstractAction;
use App\Games\Duizenden\Networking\Message\ActionType;

class ExtendMeldAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::EXTEND_MELD(), 'Add a card to a meld', 'Add a card to a meld on the table.');
	}
}
