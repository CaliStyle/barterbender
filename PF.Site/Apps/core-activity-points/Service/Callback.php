<?php
namespace Apps\Core_Activity_Points\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Callback
 * @package Apps\Core_Activity_Points\Service
 */
class Callback extends Phpfox_Service
{
    /**
     * Handling payment gateway
     * @param $aParams
     * @return bool|null
     */
    public function paymentApiCallback($aParams)
    {
        Phpfox::log('App ActivityPoint callback recieved: ' . var_export($aParams, true));
        if(!$aPurchase = Phpfox::getService('activitypoint.package')->getPurchase($aParams['item_number']))
        {
            Phpfox::log('Purchase not found');
            return false;
        }
        Phpfox::log('Purchase is valid');

        if($aParams['status'] != "completed")
        {
            Phpfox::log('Status is invalid');
            return false;
        }

        Phpfox::getService('activitypoint.package.process')->processPurchase($aParams, $aPurchase);
        Phpfox::log('Purchase activitypoint package is completed.');
        return null;
    }
}