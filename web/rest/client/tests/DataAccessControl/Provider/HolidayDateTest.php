<?php

namespace Tests\DataAccessControl\Provider;

use Ivoz\Api\Core\Security\DataAccessControlParser;
use Ivoz\Provider\Domain\Model\HolidayDate\HolidayDate;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HolidayDateTest extends KernelTestCase
{
    use \Ivoz\Tests\AccessControlTestHelperTrait;

    protected function getResourceClass()
    {
        return HolidayDate::class;
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
                    'calendar',
                    'in',
                    'CalendarRepository([["company","eq","user.getCompany().getId()"]])'
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
                            'calendar',
                            'in',
                            'CalendarRepository([["company","eq","user.getCompany().getId()"]])'
                        ]
                    ]
                ],

                [
                    'or' => [
                        [
                            'extension',
                            'in',
                            'ExtensionRepository([["company","eq","user.getCompany().getId()"]])'
                        ],
                        [
                            'extension',
                            'isNull',
                            null
                        ]
                    ]
                ],
                [
                    'or' => [
                        [
                            'voiceMailUser',
                            'in',
                            'UserRepository([["company","eq","user.getCompany().getId()"]])'
                        ],
                        [
                            'voiceMailUser',
                            'isNull',
                            null
                        ]
                    ]
                ],
                [
                    'or' => [
                        [
                            'locution',
                            'in',
                            'LocutionRepository([["company","eq","user.getCompany().getId()"]])'
                        ],
                        [
                            'locution',
                            'isNull',
                            null
                        ]
                    ]
                ]
            ]
        );
    }
}
