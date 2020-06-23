<?php

namespace Apps\YNC_Member\Block;

use \Phpfox;

class BirthdayCalendar extends \Phpfox_Component
{
    public function process()
    {
        $sToday = date('d F');
        $iLimitToday = 2;
        $iLimitUpcoming = $this->getParam('limit', 4);
        if ($iLimitUpcoming <= 0) {
            return false;
        }
        $iRemainToday = 0;
        $iRemainUpcoming = 0;
        list($iTotalTodayBirthdays, $aTodayBirthdays, $iTotalUpcomingBirthdays, $aUpcomingBirthdays) = Phpfox::getService('ynmember.browse')->getBirthdays($iLimitToday, $iLimitUpcoming);
        if ($iTotalTodayBirthdays >= $iLimitToday) {
            $iRemainToday = $iTotalTodayBirthdays - $iLimitToday;
        }
        if ($iTotalTodayBirthdays + $iTotalUpcomingBirthdays == 0) {
            return false;
        }
        if ($iTotalUpcomingBirthdays >= $iLimitUpcoming) {
            $iRemainUpcoming = $iTotalUpcomingBirthdays - $iLimitUpcoming;
        }
        $this->template()->assign([
            'sHeader' => _p('Birthday Calendar'),
            'aTodayBirthdays' => $aTodayBirthdays,
            'aUpcomingBirthdays' => $aUpcomingBirthdays,
            'iRemainToday' => $iRemainToday,
            'iRemainUpcoming' => $iRemainUpcoming,
            'sToday' => $sToday,
        ]);
        return 'block';
    }

    public function getSettings()
    {
        return [
            [
                'info' => _p('Upcoming Birthday List Limit'),
                'description' => _p('Maximum number of items on upcoming birthday list'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            
        ];
    }
    
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => _p('"Upcoming Birthday List Limit" must be greater than or equal to 0')
            ],
        ];
    }
}