<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/19/16
 * Time: 8:38 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Menu_Buyer extends Phpfox_Component
{
    public function process()
    {
        if (!Phpfox::isUser(false) || $this->request()->get('req1') != 'ynsocialstore' || $this->getParam('is_seller') || $this->getParam('menu_seller_hidden')) {
            return false;
        }

        $sFullControllerName = Phpfox::getLib('module')->getFullControllerName();

        $this->template()->assign(array(
            'sFullControllerName' => $sFullControllerName,
            'sHeader' => _p('menu_buyer'),
        ));

        return 'block';
    }
}