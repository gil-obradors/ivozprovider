<?php

namespace Ivoz\Provider\Infrastructure\Persistence\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Ivoz\Core\Infrastructure\Persistence\Doctrine\Model\Helper\CriteriaHelper;
use Ivoz\Provider\Domain\Model\CalendarPeriod\CalendarPeriodInterface;
use Ivoz\Provider\Domain\Model\CalendarPeriod\CalendarPeriodRepository;
use Ivoz\Provider\Domain\Model\CalendarPeriod\CalendarPeriod;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * CalendarPeriodDoctrineRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CalendarPeriodDoctrineRepository extends ServiceEntityRepository implements CalendarPeriodRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CalendarPeriod::class);
    }
}
