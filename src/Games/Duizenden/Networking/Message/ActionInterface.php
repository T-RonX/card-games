<?php

namespace App\Games\Duizenden\Networking\Message;

interface ActionInterface
{
	public function getType(): ActionType;

	public function getTitle(): string;

	public function getDescription(): string;
}
