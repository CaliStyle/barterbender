<?php

/**
 * [PHPFOX_HEADER]
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		PhuongNV
 * @package  		yn_member
 */

namespace Apps\YNC_Member\Controller;

use Phpfox;
use Phpfox_Component;

class ProfilePlacesController extends Phpfox_Component
{

    public function process()
    {
        list($aStudyPlaces, $aWorkPlaces, $aLivingPlaces, $aLivedPlaces) = Phpfox::getService('ynmember.place.browse')->getPlacesOfUser(Phpfox::getUserId());
        $onePageProfileThemes = array(
            'material',
            'yncfbclone'
        );
        $isHideForm = !in_array(flavor()->active->id, $onePageProfileThemes); // profile form is 1 page or not?
        $this->template()->assign([
            'aStudyPlaces' => $aStudyPlaces,
            'aWorkPlaces' => $aWorkPlaces,
            'aLivingPlaces' => $aLivingPlaces,
            'aLivedPlaces' => $aLivedPlaces,
            'isHideForm' => $isHideForm,
        ]);
    }
}