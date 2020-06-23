<?php

namespace Apps\YNC_Member\Service\Place;

use Phpfox;
use Phpfox_Service;

defined('PHPFOX') or exit('NO DICE!');

class Process extends Phpfox_Service
{
    protected $_sTable;

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynmember_place');
    }

    public function add($aVals)
    {
        $aInsert = [
            'user_id' => Phpfox::getUserId(),
            'type' => $aVals['type'],
            'current' => $aVals['current'],
            'location_title' => $aVals['location_title'],
            'location_address' => $aVals['location_address'],
            'location_latitude' => $aVals['location_latitude'],
            'location_longitude' => $aVals['location_longitude'],
            'time_stamp' => PHPFOX_TIME,
        ];

        $iId = $this->database()->insert($this->_sTable, $aInsert);
        return $iId;
    }

    public function update($aVals)
    {
        $aUpdate = [
            'current' => $aVals['current'],
            'location_title' => $aVals['location_title'],
            'location_address' => $aVals['location_address'],
            'location_latitude' => $aVals['location_latitude'],
            'location_longitude' => $aVals['location_longitude'],
        ];

        $this->database()->update($this->_sTable, $aUpdate, 'place_id = ' . (int) $aVals['place_id']);
        return $aVals['place_id'];
    }

    public function delete($placeId)
    {
        $this->database()->delete($this->_sTable, 'place_id = '.$placeId);
    }
}