<?php

declare(strict_types=1);

namespace App\Games\Duizenden\StateCompiler\Action;

use App\Games\Duizenden\StateCompiler\AbstractAction;
use App\Games\Duizenden\StateCompiler\ActionType;

class MeldCardsAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::MELD_CARDS(), 'Meld cards', 'Create a set of cards from the hand.');
	}
}
