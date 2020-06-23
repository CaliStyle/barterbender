<?php

namespace Apps\Core_Subscriptions\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;
use Phpfox_Error;

defined('PHPFOX') or exit('NO DICE!');

class RenewMethodController extends Phpfox_Component
{
    public function process()
    {
        $sCacheUserId = Phpfox::getLib('session')->get('cache_user_id');
        if (!($aPurchase = Phpfox::getService('subscribe.purchase')->getInvoice($this->request()->getInt('id'), true, $sCacheUserId))) {
            return Phpfox_Error::display(_p('unable_to_find_this_invoice'));
        }
        $this->template()->assign([
            'iPurchaseId' => $aPurchase['purchase_id'],
            'sPaymentGatewayUrl' => $this->url()->makeUrl('subscribe.register')
        ]);
    }
}