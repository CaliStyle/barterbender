<?php
/**
 * Created by PhpStorm.
 * User: phuong
 * Date: 2/19/17
 * Time: 10:58 AM
 */

namespace Apps\YNC_Member\Service\Place;

use Phpfox;
use Phpfox_Service;

class Place extends Phpfox_Service
{

    public function getForEdit($iPlaceId)
    {
        $aRow = $this->database()->select('*')
            ->from(Phpfox::getT('ynmember_place'))
            ->where('place_id = '. (int) $iPlaceId)
            ->executeRow();

        if (!isset($aRow['place_id'])) {
            return false;
        } else {
            return $aRow;
        }
    }
}