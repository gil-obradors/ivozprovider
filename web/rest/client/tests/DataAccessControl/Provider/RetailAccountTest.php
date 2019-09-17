<?php

namespace Tests\DataAccessControl\Provider;

use Ivoz\Api\Core\Security\DataAccessControlParser;
use Ivoz\Provider\Domain\Model\RetailAccount\RetailAccount;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RetailAccountTest extends KernelTestCase
{
    use \Ivoz\Tests\AccessControlTestHelperTrait;

    protected function getResourceClass()
    {
        return RetailAccount::class;
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
                    'company',
                    'eq',
                    'user.getCompany().getId()'
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
                    'and' => [
                        [
                            'company',
                            'eq',
                            'user.getCompany().getId()'
                        ]
                    ]
                ],
                [
                    'or' => [
                        [
                            'outgoingDdi',
                            'in',
                            'DdiRepository([["company","eq","user.getCompany().getId()"]])'
                        ],
                        [
                            'outgoingDdi',
                            'isNull',
                            null
                        ]
                    ]
                ],
                [
                    'or' => [
                        [
                            'transformationRuleSet',
                            'in',
                            'TransformationRuleSetRepository({"or":[["brand","eq","user.getCompany().getBrand().getId()"],["brand","eq",null]]})'
                        ],
                        [
                            'transformationRuleSet',
                            'isNull',
                            null
                        ]
                    ]
                ]
            ]
        );
    }
}
