<?php

namespace App\Games\Duizenden\StateCompiler;

interface StateCompilerInterface
{
	/**
	 * @param StateData $state_data
	 *
	 * @return array
	 */
	public function compile(StateData $state_data): array;
}