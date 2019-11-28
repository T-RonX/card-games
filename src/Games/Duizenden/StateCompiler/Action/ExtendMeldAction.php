<?php

declare(strict_types=1);

namespace App\Games\Duizenden\StateCompiler\Action;

use App\Games\Duizenden\StateCompiler\AbstractAction;
use App\Games\Duizenden\StateCompiler\ActionType;

class ExtendMeldAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::EXTEND_MELD(), 'Add a card to a meld', 'Add a card to a meld on the table.');
	}
}
