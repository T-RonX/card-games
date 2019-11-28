<?php

declare(strict_types=1);

namespace App\Games\Duizenden\StateCompiler;

class AbstractAction implements ActionInterface
{
	private ActionType $type;

	private string $title;

	private string $description;

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
