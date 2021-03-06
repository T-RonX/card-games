<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Networking\Message;

use App\CardPool\Exception\EmptyCardPoolException;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;
use App\Games\Duizenden\StateCompiler\InvalidActionException;
use App\Games\Duizenden\StateCompiler\StateCompiler;
use App\Games\Duizenden\StateCompiler\StateCompilerInterface;
use App\Games\Duizenden\StateCompiler\StateData;
use RuntimeException;

class GameEventMessageCompiler implements StateCompilerInterface
{
	private StateCompiler $state_compiler;

	public function __construct(StateCompiler $state_compiler)
	{
		$this->state_compiler = $state_compiler;
	}

	/**
	 * @throws EmptyCardPoolException
	 * @throws InvalidActionException
	 * @throws UnmappedCardException
	 */
	public function compile(StateData $state_data): array
	{
		if (!$state_data instanceof GameEventData)
		{
			throw new RuntimeException(sprintf("Message must be of type '%s'.", GameEventData::class));
		}

		$data = [
			'type' => 'game_state',
			'status' => $state_data->getStatus()->getValue(),
			'message' => $state_data->getLogMessages(),
			'game_id' => $state_data->getGameId()
		];

		if ($state_data->hasSource())
		{
			$data['source'] = [
				'player' => $this->state_compiler->createPlayerIdData($state_data->getSourcePlayer()),
				'action' => $this->state_compiler->createActionData($state_data->getSourceAction()),
				'extra' => $this->state_compiler->createExtrasData($state_data->getExtras()),
			];
		}

		$data['game_state'] = $this->state_compiler->compile($state_data);

		return $data;
	}
}