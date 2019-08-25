<?php

namespace App\Uuid;

trait UuidTrait
{
	/**
	 * @var string
	 */
	private $uuid;

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
