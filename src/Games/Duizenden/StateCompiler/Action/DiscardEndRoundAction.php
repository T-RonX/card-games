<?php

declare(strict_types=1);

namespace App\Games\Duizenden\StateCompiler\Action;

use App\Games\Duizenden\StateCompiler\AbstractAction;
use App\Games\Duizenden\Actions\ActionType;

class DiscardEndRoundAction extends AbstractAction
{
	public function __construct()
	{
		parent::__construct(ActionType::DISCARD_END_ROUND(), 'Discard round end', 'Last card discarded, round finished.');
	}
}
