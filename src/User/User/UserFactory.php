<?php

declare(strict_types=1);

namespace App\User\User;

use App\Entity\Player;
use App\Entity\User;
use App\User\Exception\UsernameAlreadyInUseException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFactory
{
    private EntityManagerInterface $entity_manager;
    private UserPasswordEncoderInterface $password_encoder;
    private UserRepository $user_repository;

    public function __construct(
        EntityManagerInterface $entity_manager,
        UserPasswordEncoderInterface $password_encoder,
        UserRepository $user_repository)
    {
        $this->entity_manager = $entity_manager;
        $this->password_encoder = $password_encoder;
        $this->user_repository = $user_repository;
    }

    /**
     * @throws UsernameAlreadyInUseException
     */
    public function create(Player $player, string $username, string $password): User
    {
        if (!$this->user_repository->isUsernameAvailable($username))
        {
            throw new UsernameAlreadyInUseException(sprintf("Username '%s' is already in use.", $username));
        }

        $user = $this->createUserEntity($player, $username, $this->createSalt());
        $user->setPassword($this->password_encoder->encodePassword($user, $password));
        $player->setUser($user);
        $this->saveUser($user);

        return $user;
    }

    /**
     * @throws
     */
    private function createSalt(): string
    {
        return base64_encode(random_bytes(32));
    }

    private function createUserEntity(Player $player, string $username, string $salt): User
    {
        return (new User())
            ->setPlayer($player)
            ->setUsername($username)
            ->setSalt($salt);
    }

    private function saveUser(User $user): void
    {
        $this->entity_manager->persist($user);
        $this->entity_manager->flush();
    }
}