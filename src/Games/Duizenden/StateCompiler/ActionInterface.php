<?php

declare(strict_types=1);

namespace App\Games\Duizenden\StateCompiler;

interface ActionInterface
{
	public function getType(): ActionType;

	public function getTitle(): string;

	public function getDescription(): string;
}
