<?php

declare(strict_types=1);

namespace App\Uuid\Exception;

use RuntimeException;
use Throwable;

class InvalidUuidFormatException extends RuntimeException
{
	public function __construct(string $uuid, int $code = 0, Throwable $previous = null)
	{
		parent::__construct(sprintf("Expected '%s' to be in a valid Uuid format.", $uuid), $code, $previous);
	}
}
