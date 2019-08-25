<?php

namespace App\Uuid\Validator;

/**
 * Class validating Uuid format.
 */
class UuidValidator
{
	/**
	 * @param string $uuid
	 *
	 * @return bool
	 */
	public static function appearsValid(string $uuid): bool
	{
		return (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid);
	}
}
