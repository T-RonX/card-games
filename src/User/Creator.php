<?php

declare(strict_types=1);

namespace App\User;

use App\Entity\Player;
use App\Security\ProgrammaticLogin;
use App\User\Exception\UsernameAlreadyInUseException;
use App\User\User\UserFactory;

class Creator
{
    private UserFactory $user_factory;
    private ProgrammaticLogin $programmatic_login;


    public function __construct(
        UserFactory $user_factory,
        ProgrammaticLogin $programmatic_login
    )
    {
        $this->user_factory = $user_factory;
        $this->programmatic_login = $programmatic_login;
    }

    /**
     * @throws UsernameAlreadyInUseException
     */
    public function createUserAndLogin(Player $player, string $username, string $password): void
    {
        $user = $this->user_factory->create($player, $username, $password);
        $this->programmatic_login->login($user);
    }
}