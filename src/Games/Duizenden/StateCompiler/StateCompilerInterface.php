<?php

declare(strict_types=1);

namespace App\Games\Duizenden\StateCompiler;

interface StateCompilerInterface
{
	public function compile(StateData $state_data): array;
}