<?php

declare(strict_types=1);

namespace App\Doctrine;

use App\Uuid\Uuid;
use App\Uuid\UuidableInterface;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Exception;

class GenerateUuidListener
{
	/**
	 * @throws Exception
	 */
	public function prePersist(LifecycleEventArgs $event): void
	{
		if ($this->isSetUuidRequired($object = $event->getObject()))
		{
			/** @var UuidableInterface $object */
			$this->setObjectUuid($object);
		}
	}

	private function isSetUuidRequired($object): bool
	{
		return $object instanceof UuidableInterface && !$object->hasUuid();
	}

	/**
	 * @throws Exception
	 */
	private function setObjectUuid(UuidableInterface $entity): void
	{
		$rand = uniqid((string) rand(), true);

		$entity->setUuid((string) Uuid::generate(5, $rand, md5($rand)));
	}
}
