<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/19/16
 * Time: 15:40
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_My_Cart extends Phpfox_Component
{
    public function process()
    {
        if(!Phpfox::isUser())
        {
            return false;
        }
        $sController = Phpfox::getLib('module')->getFullControllerName();
        $boxSize = Phpfox::getCookie('ynsocialstore_my_cart_box');
        if($this->getParam('boxSize',''))
        {
            $boxSize = $this->getParam('boxSize','');
        }
        if($sController == 'ynsocialstore.my-cart' || $sController == 'ynsocialstore.checkout')
        {
            return false;
        }
        $iCartId = Phpfox::getService('ecommerce')->getMyCartId();
        list($iCount,$aCartData) = Phpfox::getService('ynsocialstore.product')->getMyCartData();
        $this->template()->assign([
                            'iCartId' => $iCartId,
                            'iCount' => $iCount,
                            'aCartData' => $aCartData,
                            'boxSize' => (!empty($boxSize)) ? $boxSize : 'min',
                            'sCorePath' => Phpfox::getParam('core.path_file'),
                                  ]);
        return 'block';
    }

    function __call($name, $arguments)
    {

    }

}