<?php

namespace Ivoz\Provider\Application\Service\RetailAccount;

use Ivoz\Api\Entity\Serializer\Normalizer\DateTimeNormalizerInterface;
use Ivoz\Core\Application\DataTransferObjectInterface;
use Ivoz\Core\Domain\Model\EntityInterface;
use Ivoz\Core\Application\Service\Assembler\CustomDtoAssemblerInterface;
use Ivoz\Core\Infrastructure\Symfony\HttpFoundation\RequestDateTimeResolver;
use Ivoz\Kam\Domain\Model\UsersLocation\UsersLocationRepository;
use Ivoz\Kam\Domain\Model\UsersLocation\RegistrationStatus;
use Ivoz\Provider\Domain\Model\RetailAccount\RetailAccountDto;
use Ivoz\Provider\Domain\Model\RetailAccount\RetailAccountInterface;
use Assert\Assertion;

class RetailAccountDtoAssembler implements CustomDtoAssemblerInterface
{
    protected $usersLocationRepository;
    protected $requestDateTimeResolver;

    public function __construct(
        UsersLocationRepository $usersLocationRepository,
        RequestDateTimeResolver $requestDateTimeResolver
    ) {
        $this->usersLocationRepository = $usersLocationRepository;
        $this->requestDateTimeResolver = $requestDateTimeResolver;
    }

    /**
     * @param RetailAccountInterface $retailAccount
     * @throws \Exception
     */
    public function toDto(EntityInterface $retailAccount, int $depth = 0, string $context = null): DataTransferObjectInterface
    {
        Assertion::isInstanceOf($retailAccount, RetailAccountInterface::class);

        /** @var RetailAccountDto $dto */
        $dto = $retailAccount->toDto($depth);

        if (RetailAccountDto::CONTEXT_STATUS !== $context) {
            return $dto;
        }

        $domain = $retailAccount->getDomain();
        if (!$domain) {
            return $dto;
        }

        $dto->setDomainName(
            $domain->getDomain()
        );

        $userLocations = $this
            ->usersLocationRepository
            ->findByUsernameAndDomain(
                $retailAccount->getName(),
                $domain->getDomain()
            );

        foreach ($userLocations as $userLocation) {
            $dto->addStatus(
                new RegistrationStatus(
                    $userLocation,
                    $this->requestDateTimeResolver->getTimezone()
                )
            );
        }

        return $dto;
    }
}
