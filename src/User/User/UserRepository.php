<?php

declare(strict_types=1);

namespace App\User\User;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @method User findOneByUuid(string $uuid): ?User
 * @method User findOneByUsername(string $username): ?User
 */
final class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function loadUserByUsername(string $username): ?User
    {
        return $this->createQueryBuilder('u')
            ->select('u, p')
            ->join('u.Player', 'p')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

	/**
	 * @throws
	 */
	public function isUsernameAvailable(string $username): bool
	{
		return !$this->createQueryBuilder('u')
            ->select('1')
			->where('u.username = (:username)')
			->setParameter('username', $username)
			->getQuery()
			->getOneOrNullResult(Query::HYDRATE_SINGLE_SCALAR);
	}
}