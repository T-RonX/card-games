<?php

declare(strict_types=1);

namespace App\Chat\Repository;

use App\Chat\Entity\ChatRoom;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

final class ChatRoomRepository extends ServiceEntityRepository
{
	/**
	 * @inheritDoc
	 */
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, ChatRoom::class);
	}

	public function findByReference(string $game_id, DateInterval $last_activity_threshold = null): ?ChatRoom
	{
		$qb = $this->createQueryBuilder('cr')
			->select('cr, cm, ps, cps, p')
			->leftJoin('cr.ChatMessages', 'cm')
			->leftJoin('cm.Player', 'p');

		if ($last_activity_threshold)
		{
			$qb->leftJoin('cr.ChatRoomPlayers', 'cps', Join::WITH, 'cps.last_activity_at > :threshold')
				->setParameter('threshold', (new DateTime())->sub($last_activity_threshold));
		}
		else
		{
			$qb->leftJoin('cr.ChatRoomPlayers', 'cps');
		}

		$qb->leftJoin('cps.Player', 'ps')
			->where('cr.reference = :reference')
			->setParameter('reference', $game_id);

		$result = $qb->getQuery()->getResult();

		return 1 === count($result) ? $result[0] : null;
	}
}