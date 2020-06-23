<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 19/01/2017
 * Time: 09:30
 */

namespace Apps\YNC_Affiliate\Controller;

defined('PHPFOX') or exit('NO DICE!');
use Phpfox;
use Phpfox_Plugin;
use Phpfox_Error;

class RegisterController extends \Phpfox_Component
{
    public function process()
    {
        $bIsPending = false;
        $bIsDenied = false;
        $bIsCanSignup = user('ynaf_can_register_affiliate');
        Phpfox::getService('yncaffiliate.helper')->buildMenu();
        $iIsAffiliate =  Phpfox::getService('yncaffiliate.affiliate.affiliate')->checkIsAffiliate(Phpfox::getUserId());
        if($iIsAffiliate == 'pending')
        {
            $bIsPending = true;
            $this->template()->assign([
                'bIsPending' => $bIsPending,
            ]);
        }
        elseif($iIsAffiliate == 'denied'){
            $bIsDenied = true;
            $this->template()->assign([
                'bIsDenied' => $bIsDenied,
            ]);
        }
        $aUser = Phpfox::getService('user')->getUser(Phpfox::getUserId());
        if($aVals = $this->request()->getArray('val'))
        {
            if($iId = Phpfox::getService('yncaffiliate.affiliate.process')->addAffiliate($aVals))
            {
                $this->url()->send('affiliate',_p('sign_up_affiliate_successfully'));
            }
        }
        $this->template()->setTitle(_p('join_affiliate_program'))
                        ->setBreadCrumb(_p('Affiliate'))
                        ->setBreadCrumb(($bIsPending) ? _p('waiting_for_approval') : (($bIsDenied) ? _p('denied') : _p('sign_up')),$this->url()->makeUrl('affiliate'))
                        ->setPhrase([
                            'terms_of_service',
                            'this_field_is_required',
                            'you_have_to_agree_with_our_terms_of_service'
                        ])
                        ->assign([
                            'bIsPending' => $bIsPending,
                            'bIsDenied' => $bIsDenied,
                            'bIsCanSignup' => $bIsCanSignup,
                            'aUser' => $aUser,
                            'corePath' => Phpfox::getParam('core.path_actual').'PF.Site/Apps/ync-affiliate'
                        ]);
    }
}