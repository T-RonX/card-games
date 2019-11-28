<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Networking\Message;

use App\Games\Duizenden\StateCompiler\StateCompilerInterface;
use App\Games\Duizenden\StateCompiler\StateData;
use App\Games\Duizenden\StateCompiler\StatusType;
use App\Games\Duizenden\StateCompiler\TopicType;

class GameEventData extends StateData
{
	private TopicType $topic_type;

	private StatusType $status;

	private string$identifier;

	/**
	 * @var string[]
	 */
	private array $log_messages = [];

	public function __construct(StateCompilerInterface $builder, TopicType $topic, string $identifier, StatusType $status)
	{
		parent::__construct($builder);

		$this->topic_type = $topic;
		$this->status = $status;
		$this->identifier = $identifier;
	}

	public function getTopicType(): TopicType
	{
		return $this->topic_type;
	}

	public function getIdentifier(): string
	{
		return $this->identifier;
	}

	public function setStatus(StatusType $status): self
	{
		$this->status = $status;

		return $this;
	}

	public function getStatus(): StatusType
	{
		return $this->status;
	}

	public function addLogMessages(string $message): self
	{
		$this->log_messages[] = $message;

		return $this;
	}

	public function getLogMessages(): array
	{
		return $this->log_messages;
	}
}