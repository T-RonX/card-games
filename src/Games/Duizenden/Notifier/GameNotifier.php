<?php

namespace App\Games\Duizenden\Notifier;

use App\Games\Duizenden\Game;
use App\Games\Duizenden\Networking\Message\GameEventData;
use App\Games\Duizenden\Networking\Message\GameEventMessageCompiler;
use App\Games\Duizenden\Networking\Message\MessageBuilder;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;
use App\Games\Duizenden\StateBuilder\StateBuilder;
use App\Games\Duizenden\StateCompiler\StatusType;
use App\Games\Duizenden\StateCompiler\TopicType;
use Symfony\Component\Mercure\PublisherInterface;

class GameNotifier
{
	/**
	 * @var PublisherInterface
	 */
	private $publisher;

	/**
	 * @var StateBuilder
	 */
	private $state_builder;

	/**
	 * @var MessageBuilder
	 */
	private $message_builder;

	/**
	 * @var GameEventMessageCompiler
	 */
	private $message_compiler;

	public function __construct(
		PublisherInterface $publisher,
		StateBuilder $state_builder,
		GameEventMessageCompiler $state_compiler,
		MessageBuilder $message_builder
	)
	{
		$this->publisher = $publisher;
		$this->state_builder = $state_builder;
		$this->message_compiler = $state_compiler;
		$this->message_builder = $message_builder;
	}

	/**
	 * @param string $identifier
	 * @param Game $game
	 * @param TopicType $topic
	 * @param StatusType|null $status
	 *
	 * @return GameEventData
	 *
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	public function createGameMessageBuilder(string $identifier, Game $game, TopicType $topic, ?StatusType $status = null): GameEventData
	{
		$message = $this->createMessageBuilder($identifier, $topic, $status);
		$this->state_builder->fillStateData($message, $game);

		return $message;
	}

	public function createMessageBuilder(string $identifier, TopicType $topic, ?StatusType $status = null): GameEventData
	{
		return new GameEventData($this->message_compiler, $topic, $identifier, $status ?? StatusType::OK());
	}

	/**
	 * @param GameEventData $message
	 */
	public function notifyMessage(GameEventData $message): void
	{
		($this->publisher)($this->message_builder->compile($message));
	}
}