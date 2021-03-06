<?php

namespace Ivoz\Provider\Infrastructure\Persistence\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Ivoz\Provider\Domain\Model\ConditionalRoutesConditionsRelSchedule\ConditionalRoutesConditionsRelScheduleRepository;
use Ivoz\Provider\Domain\Model\ConditionalRoutesConditionsRelSchedule\ConditionalRoutesConditionsRelSchedule;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * ConditionalRoutesConditionsRelCalendarDoctrineRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ConditionalRoutesConditionsRelScheduleDoctrineRepository extends ServiceEntityRepository implements ConditionalRoutesConditionsRelScheduleRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ConditionalRoutesConditionsRelSchedule::class);
    }
}
