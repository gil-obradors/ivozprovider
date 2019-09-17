<?php

namespace Ivoz\Provider\Application\Service\DdiProviderRegistration;

use Ivoz\Core\Application\DataTransferObjectInterface;
use Ivoz\Core\Domain\Model\EntityInterface;
use Ivoz\Core\Application\Service\Assembler\CustomDtoAssemblerInterface;
use Ivoz\Kam\Domain\Model\TrunksUacreg\DdiProviderRegistrationStatus;
use Ivoz\Kam\Domain\Service\TrunksClientInterface;
use Ivoz\Provider\Domain\Model\DdiProviderRegistration\DdiProviderRegistrationDto;
use Ivoz\Provider\Domain\Model\DdiProviderRegistration\DdiProviderRegistrationInterface;
use Assert\Assertion;

class DdiProviderRegistrationDtoAssembler implements CustomDtoAssemblerInterface
{
    protected $trunksClient;

    public function __construct(
        TrunksClientInterface $trunksClient
    ) {
        $this->trunksClient = $trunksClient;
    }

    /**
     * @param DdiProviderRegistrationInterface $ddiProviderRegistration
     * @throws \Exception
     */
    public function toDto(EntityInterface $ddiProviderRegistration, int $depth = 0, string $context = null): DataTransferObjectInterface
    {
        Assertion::isInstanceOf($ddiProviderRegistration, DdiProviderRegistrationInterface::class);

        /** @var DdiProviderRegistrationDto $dto */
        $dto = $ddiProviderRegistration->toDto($depth);

        if (DdiProviderRegistrationDto::CONTEXT_DETAILED_COLLECTION !== $context) {
            return $dto;
        }

        $trunksUacreg = $ddiProviderRegistration->getTrunksUacreg();

        $uacRegistrationInfo = $this->trunksClient->getUacRegistrationInfo(
            $trunksUacreg->getLUuid()
        );

        $statusCode = $uacRegistrationInfo['flags'] ?? -1;
        $expires = $uacRegistrationInfo['diff_expires'] ?? null;

        $dto->setStatus(
            new DdiProviderRegistrationStatus(
                $statusCode,
                $expires
            )
        );

        return $dto;
    }
}
