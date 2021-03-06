<?php

namespace Ivoz\Provider\Domain\Model\MatchList;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\Common\Collections\Selectable;

interface MatchListRepository extends ObjectRepository, Selectable
{
    public function getIdsByCompanyId(int $companyId): array;
}
