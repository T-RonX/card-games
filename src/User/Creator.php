<?php

declare(strict_types=1);

namespace App\User;

use App\Entity\Player;
use App\Security\ProgrammaticLogin;
use App\User\Exception\UsernameAlreadyInUseException;
use App\User\Player\PlayerFactory;
use App\User\User\UserFactory;
use RuntimeException;

class Creator
{
    private UserFactory $user_factory;
    private ProgrammaticLogin $programmatic_login;
    /**
     * @var PlayerFactory
     */
    private PlayerFactory $player_factory;


    public function __construct(
        PlayerFactory $player_factory,
        UserFactory $user_factory,
        ProgrammaticLogin $programmatic_login
    )
    {
        $this->player_factory = $player_factory;
        $this->user_factory = $user_factory;
        $this->programmatic_login = $programmatic_login;
    }

    /**
     * @throws UsernameAlreadyInUseException
     */
    public function createUserAndLogin(?Player $player, string $username, string $password, ?string $playername): void
    {
        if (null === $player)
        {
            if (!$playername)
            {
                throw new RuntimeException("Unable to create user. Invalid playername given.");
            }

            $player = $this->player_factory->create($playername);
        }

        $user = $this->user_factory->create($player, $username, $password);
        $this->programmatic_login->login($user);
    }
}