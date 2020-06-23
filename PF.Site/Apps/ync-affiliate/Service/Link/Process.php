<?php

/**
 * Created by PhpStorm.
 * User: dai
 * Date: 20/01/2017
 * Time: 10:44
 */

namespace Apps\YNC_Affiliate\Service\Link;

use Phpfox;

Class Process extends \Phpfox_Service
{
    public function __construct()
    {
        $this->_activeModule = implode('\',\'',Phpfox::getLib('module')->getModules());
        $this->_sTable = Phpfox::getT('yncaffiliate_suggests');
        $this->_sTracking = Phpfox::getT('yncaffiliate_links');
    }

    public function addTracking($sLink,$sCurrentUrl,$iUserId,$bIsDynamic = false)
    {
        $aInsert = [
            'link_title' => _p('united_title'),
            'user_id'    => $iUserId,
            'is_dynamic' => ($bIsDynamic) ? 1 : 0,
            'target_url' => $sLink,
            'affiliate_url' => $sCurrentUrl,
            'total_click' => 1,
            'total_success' => 0,
            'last_user_id' => 0,
            'last_registered' => 0,
            'last_click' => PHPFOX_TIME,
            'time_stamp' => PHPFOX_TIME,
        ];
        $iId = db()->insert($this->_sTracking,$aInsert);
        return $iId;
    }
    public function updateClickLink($iTrackingId)
    {
        db()->updateCounter('yncaffiliate_links', 'total_click', 'link_id', $iTrackingId);
        db()->update($this->_sTracking,['last_click' => PHPFOX_TIME],'link_id ='.$iTrackingId);
        return true;
    }
}