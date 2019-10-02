<?php

namespace App\Games\Duizenden\StateCompiler;

class AbstractAction implements ActionInterface
{
	/**
	 * @var ActionType
	 */
	private $type;

	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var string
	 */
	private $description;

	public function __construct(ActionType $type, string $title, string $description)
	{
		$this->type = $type;
		$this->title = $title;
		$this->description = $description;
	}

	public function getType(): ActionType
	{
		return $this->type;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function getDescription(): string
	{
		return $this->description;
	}
}
