<?php

use Ivoz\Provider\Domain\Model\Calendar\Calendar;
use Ivoz\Provider\Domain\Model\Calendar\CalendarDto;
use Ivoz\Provider\Domain\Model\CalendarPeriod\CalendarPeriod;
use Ivoz\Provider\Domain\Model\CalendarPeriod\CalendarPeriodDto;

/**
 * Class IvozProvider_Klear_Filter_CalendarPeriodSelectLocution
 *
 * Filter Locution Listbox to only display Company's Locution
 * We can not use Company filter here because the model hasn't companyId field
 *
 */
class IvozProvider_Klear_Filter_CalendarPeriodSelectLocution implements KlearMatrix_Model_Field_Select_Filter_Interface
{
    protected $_condition = array();

    public function setRouteDispatcher(KlearMatrix_Model_RouteDispatcher $routeDispatcher)
    {
        //Get ModelName and your Controller
        $currentItemName = $routeDispatcher->getCurrentItemName();

        $pk = $routeDispatcher->getParam("pk", false);
        $parentId = $routeDispatcher->getParam("parentId", false);

        $dataGateway = \Zend_Registry::get('data_gateway');
        switch ($currentItemName) {
            case "calendarPeriodsNew_screen":
                if ($parentId) {

                    /** @var CalendarDto $calendarDto */
                    $calendarDto = $dataGateway->find(
                        Calendar::class,
                        $parentId
                    );
                    $this->_condition[] = "self::company = '" . $calendarDto->getCompanyId() . "'";
                }
                break;
            case "calendarPeriodsEdit_screen":
                if ($pk) {
                    /** @var CalendarPeriodDto $holidayDateDto */
                    $holidayDateDto = $dataGateway->find(
                        CalendarPeriod::class,
                        $pk
                    );

                    /** @var CalendarDto $calendarDto */
                    $calendarDto = $dataGateway->find(
                        Calendar::class,
                        $holidayDateDto->getCalendarId()
                    );

                    $companyId = $calendarDto->getCompanyId();
                    $this->_condition[] = "self::company = '".$companyId."'";
                }
                break;
        }

        return true;
    }

    public function getCondition()
    {
        if (count($this->_condition) > 0) {
            return ['(' . implode(" AND ", $this->_condition) . ')'];
        }
        return;
    }
}
