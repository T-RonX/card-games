<?php

declare(strict_types=1);

namespace App\Games\Duizenden\AI\Context;

class RiskLevel
{
	private int $risk_level;

	public function __construct(int $risk_level)
	{
		$this->risk_level = $risk_level;
	}

	public function getRiskLevel(): int
	{
		return $this->risk_level;
	}
}