<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 18/01/2017
 * Time: 12:02
 */

namespace Apps\YNC_Affiliate\Controller;

defined('PHPFOX') or exit('NO DICE!');
use Phpfox;

class NetworkClientsController extends \Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('yncaffiliate.helper')->buildMenu();
        $this->template()->setTitle(_p('network_clients'))
            ->setBreadCrumb(_p('Affiliate'))
            ->setBreadCrumb(_p('network_clients'),$this->url()->makeUrl('affiliate.network-clients'));
        if(!$iUserId = $this->request()->getInt('id'))
        {
            $iUserId = (int)Phpfox::getUserId();
        }
        $aUser = Phpfox::getService('user')->getUser($iUserId);
        if(!$aUser)
        {
            return $this->url()->send('affiliate',_p('user_not_found'));
        }
        $aClients = Phpfox::getService('yncaffiliate.affiliate.affiliate')->getClient($iUserId);
        $iTotalClients = Phpfox::getService('yncaffiliate.affiliate.affiliate')->countAllClient($iUserId);
        $iTotalDirect =  Phpfox::getService('yncaffiliate.affiliate.affiliate')->countDirectClient($iUserId);
        $iMaxLevel = setting('ynaf_number_commission_levels');
        $iLoadedClient = count($aClients);
        $sHtmlTree = Phpfox::getService('yncaffiliate.affiliate.affiliate')->getTree($aClients,$iMaxLevel,$iTotalDirect,0,$aUser['user_id'],0,0,$iLoadedClient);
        $this->template()->assign([
            'aClients' => $aClients,
            'iTotalClients' => $iTotalClients,
            'iTotalDirect' => $iTotalDirect,
            'aUser' => $aUser,
            'iMaxLevel' => setting('ynaf_number_commission_levels'),
            'iLastAssocId' => 0,
            'iSearchUserId' => 0,
            'iLoadedClient' => count($aClients),
            'sHtmlTree' => $sHtmlTree
        ]);
    }
}