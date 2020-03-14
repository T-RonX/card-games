<?php

declare(strict_types=1);

namespace App\User\Validator;

use App\User\User\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class UsernameAvailableConstraintValidator extends ConstraintValidator
{
    private UserRepository $user_repository;

    public function __construct(UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    public function validate($username, Constraint $constraint)
    {
        if (!$constraint instanceof UsernameAvailableConstraint)
        {
            throw new UnexpectedTypeException($constraint, UsernameAvailableConstraint::class);
        }

        if (null === $username || '' === $username)
        {
            return;
        }

        if (!is_string($username))
        {
            throw new UnexpectedValueException($username, 'string');
        }

        if (!$this->user_repository->isUsernameAvailable($username))
        {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ username }}', $username)
                ->addViolation();
        }
    }
}