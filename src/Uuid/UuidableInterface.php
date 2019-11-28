<?php

declare(strict_types=1);

namespace App\Uuid;

interface UuidableInterface
{
	public function setUuid(string $uuid);

	public function hasUuid(): bool;

	public function getUuid(): string;
}
