<?php

namespace App\Games\Duizenden\Networking\Message;

use Symfony\Component\Mercure\Update;

class GameEventMessage
{
	/**
	 * @var MessageBuilder
	 */
	private $builder;

	/**
	 * @var string
	 */
	private $topic_type;

	/**
	 * @var string
	 */
	private $identifier;

	/**
	 * @var string[]
	 */
	private $log_messages = [];

	/**
	 * @var string
	 */
	private $cause_player;

	/**
	 * @var string
	 */
	private $type;

	public function __construct(MessageBuilder $builder, string $target, string $identifier, string $status, string $type)
	{
		$this->topic_type = $target;
		$this->identifier = $identifier;
		$this->builder = $builder;
		$this->status = $status;
		$this->type = $type;
	}

	public function create(): Update
	{
		return $this->builder->compile($this);
	}

	public function setStatus(string $status): self
	{
		$this->status = $status;

		return $this;
	}

	public function getStatus(): string
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

	public function getTopicType(): string
	{
		return $this->topic_type;
	}

	public function getIdentifier(): string
	{
		return $this->identifier;
	}

	public function setCausePlayer(string $cause_player): self
	{
		$this->cause_player = $cause_player;

		return $this;
	}

	public function getCausePlayer(): string
	{
		return $this->cause_player;
	}

	public function setType(string $type): self
	{
		$this->type = $type;

		return $this;
	}

	public function getType(): string
	{
		return $this->type;
	}
}