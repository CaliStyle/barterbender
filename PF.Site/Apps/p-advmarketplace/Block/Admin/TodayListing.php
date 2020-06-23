<?php

namespace Apps\P_AdvMarketplace\Block\Admin;

use Phpfox_Component;
use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class TodayListing extends Phpfox_Component
{
    public function process()
    {
        $iId = $this->getParam("iId");
        $aTListing = Phpfox::getService("advancedmarketplace")->getTodayListing($iId);

        $mostRecentMonth = 0;
        $mostRecentYear = Phpfox::getTime('Y');
        foreach($aTListing as $timeListing) {
            if($timeListing['time_stamp'] > (PHPFOX_TIME * 1000)) {
                $mostRecentTime = (int)($timeListing['time_stamp'] / 1000);
                $mostRecentMonth = (int)Phpfox::getTime('m', $mostRecentTime);
                $mostRecentYear = (int)Phpfox::getTime('Y', $mostRecentTime);
                break;
            }
        }

        $this->template()->assign(array(
            "iId" => $iId,
            "aTListing" => $aTListing,
            'sCustomClassName' => 'ync-block',
            'mostRecentMonth' => $mostRecentMonth,
            'mostRecentYear' => $mostRecentYear
        ));

        return "block";
    }
}
