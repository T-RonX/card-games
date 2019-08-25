<?php

namespace App\Uuid;

interface UuidableInterface
{
	/**
	 * Sets the UUID.
	 *
	 * @param string $uuid
	 */
	public function setUuid(string $uuid);

	/**
	 * Gets whether or not an uuid is set.
	 *
	 * @return bool
	 */
	public function hasUuid(): bool;

	/**
	 * Gets the UUID.
	 *
	 * @return string
	 */
	public function getUuid(): string;
}
