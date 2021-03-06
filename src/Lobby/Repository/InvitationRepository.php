<?php

declare(strict_types=1);

namespace App\Lobby\Repository;

use App\Entity\Player;
use App\Lobby\Entity\Invitation;
use App\Lobby\Entity\Invitee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;

final class InvitationRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Invitation::class);
	}

	/**
	 * @throws NonUniqueResultException
	 */
	public function getInvitation(string $uuid): ?Invitation
	{
		return $this->createQueryBuilder('i')
			->select('i, its, p')
			->join('i.Invitees', 'its')
			->join('its.Player', 'p')
			->where('i.uuid = :uuid')
			->setParameter('uuid', $uuid)
			->getQuery()
			->getOneOrNullResult();
	}

	/**
	 * @return Invitee[]|iterable
	 */
	public function getInvitationsByInviter(Player $inviter): iterable
	{
		return $this->createQueryBuilder('i')
			->select('i, its, p')
			->join('i.Inviter', 'ir')
			->join('ir.Player', 'pi', Join::WITH, 'pi = :inviter')
			->join('i.Invitees', 'its')
			->join('its.Player', 'p')
			->setParameter('inviter', $inviter)
			->orderBy('i.created_at', 'desc')
			->addOrderBy('p.name')
			->getQuery()
			->getResult();
	}

	/**
	 * @return Invitee[]|iterable
	 */
	public function getInvitationsByInvitee(Player $invitee): iterable
	{
		return $this->createQueryBuilder('i')
			->select('i, iv, ip')
			->leftJoin('i.Inviter', 'ir')
			->leftJoin('ir.Player', 'pr', Join::WITH, 'pr = :invitee')
			->join('i.Invitees', 'it', Join::WITH, 'pr.id IS NULL')
			->join('it.Player', 'pt', Join::WITH, 'pt = :invitee')
			->join('i.Invitees', 'iv')
			->join('iv.Player', 'ip'/*, Join::WITH, 'ip.id != pt.id'*/) // Excludes the invitee from result
			->orderBy('i.created_at', 'desc')
			->addOrderBy('ip.name')
			->setParameter('invitee', $invitee)
			->getQuery()
			->getResult();
	}
}