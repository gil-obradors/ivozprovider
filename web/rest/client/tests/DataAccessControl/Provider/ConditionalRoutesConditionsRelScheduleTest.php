<?php

namespace Tests\DataAccessControl\Provider;

use Ivoz\Api\Core\Security\DataAccessControlParser;
use Ivoz\Provider\Domain\Model\ConditionalRoutesConditionsRelSchedule\ConditionalRoutesConditionsRelSchedule;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ConditionalRoutesConditionsRelScheduleTest extends KernelTestCase
{
    use \Ivoz\Tests\AccessControlTestHelperTrait;

    protected function getResourceClass()
    {
        return ConditionalRoutesConditionsRelSchedule::class;
    }

    protected function getAdminCriteria(): array
    {
        return ['username' => 'test_company_admin'];
    }

    /**
     * @test
     */
    function it_has_read_access_control()
    {
        $accessControl = $this
            ->dataAccessControlParser
            ->get(
                DataAccessControlParser::READ_ACCESS_CONTROL_ATTRIBUTE
            );

        $this->assertEquals(
            $accessControl,
            [
                [
                    'schedule',
                    'in',
                    'ScheduleRepository([["company","eq","user.getCompany().getId()"]])'
                ]
            ]
        );
    }

    /**
     * @test
     */
    function it_has_write_access_control()
    {
        $accessControl = $this
            ->dataAccessControlParser
            ->get(
                DataAccessControlParser::WRITE_ACCESS_CONTROL_ATTRIBUTE
            );

        $this->assertEquals(
            $accessControl,
            [
                [
                    'condition',
                    'in',
                    'ConditionalRoutesConditionRepository([["conditionalRoute","IN",["ConditionalRouteRepository([[\"company\",\"eq\",\"user.getCompany().getId()\"]])"]]])'
                ],
                [
                    'schedule',
                    'in',
                    'ScheduleRepository([["company","eq","user.getCompany().getId()"]])'
                ]
            ]
        );
    }
}
