<?php

declare(strict_types=1);

namespace App\Games\Duizenden\StateCompiler\Action;

use App\Games\Duizenden\StateCompiler\AbstractAction;
use App\Games\Duizenden\StateCompiler\ActionType;

class UndoLastActionAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::UNDO_LAST_ACTION(), 'Undo last action', 'Last game action was undone.');
	}
}
