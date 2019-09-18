<?php

namespace App\Games\Duizenden\Notifier;

use App\Games\Duizenden\Networking\Message\MessageBuilder;
use Symfony\Component\Mercure\Publisher;

class GameNotifier
{
	/**
	 * @var Publisher
	 */
	private $publisher;

	/**
	 * @var MessageBuilder
	 */
	private $builder;

	public function __construct(
		Publisher $publisher,
		MessageBuilder $message_builder
	)
	{
		$this->publisher = $publisher;
		$this->builder = $message_builder;
	}

	public function notify(string $game_id, string $cause_player_id, ?string $type = 'refresh'): void
	{
		$message = $this->builder->createMessageBuilder($game_id, 'refresh', $type)
			->addLogMessages("Refresh me please!")
			->setCausePlayer($cause_player_id)
			->create();

		($this->publisher)($message);
	}
}