<?php

declare(strict_types=1);

namespace App\User\Validator;

use Symfony\Component\Validator\Constraint;

class UsernameAvailableConstraint extends Constraint
{
    public string $message = "Username '{{ username }}' is already in use.";

    public function validatedBy(): string
    {
        return UsernameAvailableConstraintValidator::class;
    }
}