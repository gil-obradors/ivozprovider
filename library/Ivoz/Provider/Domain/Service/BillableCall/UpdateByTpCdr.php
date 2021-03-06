<?php

namespace Ivoz\Provider\Domain\Service\BillableCall;

use Ivoz\Cgr\Domain\Model\TpCdr\TpCdrInterface;
use Ivoz\Cgr\Domain\Model\TpCdr\TpCdrRepository;
use Ivoz\Cgr\Domain\Model\TpDestination\TpDestinationInterface;
use Ivoz\Cgr\Domain\Model\TpDestination\TpDestinationRepository;
use Ivoz\Cgr\Domain\Model\TpRatingPlan\TpRatingPlanInterface;
use Ivoz\Cgr\Domain\Model\TpRatingPlan\TpRatingPlanRepository;
use Ivoz\Core\Application\Service\EntityTools;
use Ivoz\Core\Infrastructure\Domain\Service\Cgrates\ProcessExternalCdr;
use Ivoz\Kam\Domain\Model\TrunksCdr\Event\TrunksCdrWasMigratedSubscriberInterface;
use Ivoz\Kam\Domain\Model\TrunksCdr\TrunksCdrInterface;
use Ivoz\Provider\Domain\Model\BillableCall\BillableCallDto;
use Ivoz\Provider\Domain\Model\BillableCall\BillableCallInterface;
use Ivoz\Provider\Domain\Model\Carrier\CarrierInterface;
use Ivoz\Provider\Domain\Model\Destination\DestinationInterface;
use Ivoz\Kam\Domain\Model\TrunksCdr\Event\TrunksCdrWasMigrated;
use Ivoz\Core\Domain\Event\DomainEventInterface;
use Psr\Log\LoggerInterface;

class UpdateByTpCdr implements TrunksCdrWasMigratedSubscriberInterface
{
    protected $tpCdrRepository;
    protected $updateDtoByDefaultRunTpCdr;
    protected $processExternalCdr;
    protected $entityTools;
    protected $logger;

    public function __construct(
        TpCdrRepository $tpCdrRepository,
        UpdateDtoByDefaultRunTpCdr $updateDtoByDefaultRunTpCdr,
        ProcessExternalCdr $processExternalCdr,
        EntityTools $entityTools,
        LoggerInterface $logger
    ) {
        $this->tpCdrRepository = $tpCdrRepository;
        $this->updateDtoByDefaultRunTpCdr = $updateDtoByDefaultRunTpCdr;
        $this->processExternalCdr = $processExternalCdr;
        $this->entityTools = $entityTools;
        $this->logger = $logger;
    }

    /**
     * @param DomainEventInterface $domainEvent
     * @return boolean
     */
    public function isSubscribedTo(DomainEventInterface $domainEvent)
    {
        if ($domainEvent instanceof TrunksCdrWasMigrated) {
            return true;
        }

        return false;
    }

    /**
     * @param TrunksCdrWasMigrated $domainEvent
     * @throws \Exception
     * @return void
     */
    public function handle(DomainEventInterface $domainEvent)
    {
        if (!($domainEvent instanceof TrunksCdrWasMigrated)) {
            throw new \Exception('TrunksCdrWasMigrated was expected');
        }

        $trunksCdr = $domainEvent->getTrunksCdr();
        $billableCall = $domainEvent->getBillableCall();

        $isNotOutbound = !$billableCall->isOutboundCall();
        if ($isNotOutbound) {
            $msg = sprintf(
                'Skipping %s call #%d',
                $billableCall->getDirection(),
                $billableCall->getId()
            );
            $this->logger->info($msg);

            return;
        }

        $infoMsg = sprintf(
            'About to update billable call by TpCdr. TrunksCdr#%s',
            $trunksCdr->getId()
        ) ;
        $this->logger->info($infoMsg);

        $this->execute(
            $trunksCdr,
            $billableCall
        );
    }

    /**
     * @param BillableCallInterface $billableCall
     * @return void
     */
    protected function execute(
        TrunksCdrInterface $trunksCdr,
        BillableCallInterface $billableCall
    ) {
        $cgrid = $trunksCdr->getCgrid();
        if (!$cgrid) {
            try {
                $processed = $this->processExternalCdr->execute($trunksCdr);
                if ($processed) {
                    $cgrid = $trunksCdr->getCgrid();
                }
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        if (!$cgrid) {
            $this->logger->info('Cgrid was not found. Skipping');
            return;
        }

        /** @var CarrierInterface $carrier */
        $carrier = $billableCall->getCarrier();
        if ($carrier && $carrier->getExternallyRated()) {
            $infoMsg = sprintf(
                'Carrier#%s has external rater. Skipping',
                $carrier->getId()
            );
            $this->logger->info($infoMsg);
            return;
        }

        /**
         * @var TpCdrInterface $defaultRunTpCdr
         */
        $defaultRunTpCdr = $this->tpCdrRepository->getDefaultRunByCgrid(
            $cgrid
        );

        if (!$defaultRunTpCdr) {
            $errorMsg = sprintf(
                'No default run TpCdr found for cgrid %s. Skipping',
                $cgrid
            );
            $this->logger->error($errorMsg);
            return;
        }

        /** @var BillableCallDto $billableCallDto */
        $billableCallDto = $this->entityTools->entityToDto($billableCall);
        $billableCallDto = $this->updateDtoByDefaultRunTpCdr->execute(
            $billableCallDto,
            $trunksCdr,
            $defaultRunTpCdr
        );

        /**
         * @var TpCdrInterface $carrierRunTpCdr
         */
        $carrierRunTpCdr = $this->tpCdrRepository->getCarrierRunByCgrid(
            $cgrid
        );

        if ($carrierRunTpCdr) {
            $billableCallDto->setCost(
                $carrierRunTpCdr->getCost()
            );
        }

        $this->entityTools->persistDto(
            $billableCallDto,
            $billableCall,
            false
        );
    }
}
