<?php

declare(strict_types=1);

namespace App\Uuid;

trait UuidTrait
{
	private ?string $uuid = null;

	public function getUuid(): string
	{
		return $this->uuid;
	}

	public function hasUuid(): bool
	{
		return null !== $this->uuid;
	}

	public function setUuid(string $uuid): self
	{
		$this->uuid = $uuid;

		return $this;
	}
}
