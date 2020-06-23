<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 09:26
 */

namespace Apps\YNC_Affiliate\Controller;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
class InviteLinkController extends \Phpfox_Component
{
    public function process()
    {
        $sCurrentUrl = $this->url()->getFullUrl();
        if($this->request()->get('req1') == 'yaf')
        {
            $bIsDynamic = false;
            $iUserId = $this->request()->get('req2');
            if((int)$iUserId)
            {
                $sHref = $this->request()->get('req3');
                if($sHref){
                    $sDesHref = base64_decode($sHref);
                }

            }
            elseif($iUserId == 'd'){
                $bIsDynamic = true;
                $iUserId = $this->request()->get('req3');
                $sHref = $this->request()->get('req4');
                if($sHref){
                    $sDesHref = base64_decode($sHref);
                }
            }
            if($iTrackingId = Phpfox::getService('yncaffiliate.link')->checkIsTracking($sDesHref,$iUserId))
            {
                Phpfox::getService('yncaffiliate.link.process')->updateClickLink($iTrackingId);
            }
            else{
                $iTrackingId = Phpfox::getService('yncaffiliate.link.process')->addTracking($sDesHref,$sCurrentUrl,$iUserId,$bIsDynamic);
            }
            Phpfox::removeCookie('ynaf_invite_user');
            Phpfox::removeCookie('ynaf_invite_link_id');
            Phpfox::removeCookie('ynaf_invite_time');

            Phpfox::setCookie('ynaf_invite_user',$iUserId);
            Phpfox::setCookie('ynaf_invite_link_id',$iTrackingId);
            Phpfox::setCookie('ynaf_invite_time',PHPFOX_TIME);
            return $this->url()->send($sDesHref);
        }
        return $this->url()->send('affiliate',_p('cannot_detect_link'));
    }
}