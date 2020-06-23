<?php
/**
 * Created by IntelliJ IDEA.
 * User: macpro
 * Date: 2/24/17
 * Time: 6:45 PM
 */

namespace Apps\YNC_Member\Service\Place;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Error;

class Browse extends \Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynmember_place');
    }

    public function query()
    {

    }

    public function getQueryJoins()
    {
        $this->database()
            ->leftJoin(Phpfox::getT('user'), 'reviewer', 're.user_id = reviewer.user_id');
        if($this->search()->get('form_flag') != 1 && !$this->request()->get('s'))
            $this->database()->group('re.item_id')
            ;
    }

    /**
     * @param $iUserId
     * @return array
     */
    public function getPlacesOfUser($iUserId)
    {
        $aRows = $this->database()
            ->select('*')
            ->from($this->_sTable)
            ->where('user_id = ' . (int)$iUserId)
            ->order('current DESC, place_id DESC')
            ->executeRows();

        $aStudyPlaces = [];
        $aWorkPlaces = [];
        $aLivingPlaces = [];
        $aLivedPlaces = [];

        foreach ($aRows as $aRow) {
            if ($aRow['type'] == 'work') {
                $aWorkPlaces[] = $aRow;
            } elseif ($aRow['type'] == 'study') {
                $aStudyPlaces[] = $aRow;
            } elseif ($aRow['type'] == 'living') {
                $aLivingPlaces[] = $aRow;
            } elseif ($aRow['type'] == 'lived') {
                $aLivedPlaces[] = $aRow;
            }
        }

        return [$aStudyPlaces, $aWorkPlaces, $aLivingPlaces, $aLivedPlaces];
    }

}