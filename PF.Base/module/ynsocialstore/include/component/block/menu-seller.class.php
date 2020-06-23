<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/19/16
 * Time: 8:38 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Menu_Seller extends Phpfox_Component
{
    public function process()
    {

        if ($this->request()->get('req1') != 'ynsocialstore' || !$this->getParam('is_seller'))
            return false;

        $sFullControllerName = Phpfox::getLib('module')->getFullControllerName();
        $position = Phpfox::getUserParam('ynsocialstore.what_did_friend_buy') ? 4 : 5;

        $ecommerce_settings = Phpfox::getService('ecommerce')->getGlobalSetting();

        $bDisableVitualMoney = $ecommerce_settings['actual_setting']['payment_settings'];

        $this->template()->assign(array(
            'sFullControllerName' => $sFullControllerName,
            'position' => $position,
            'sHeader' => _p('menu_seller'),
            'bDisableVitualMoney' => $bDisableVitualMoney,
            'is_seller' => $this->getParam('is_seller'),
        ));

        return 'block';
    }
}