<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/21/16
 * Time: 7:34 PM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Controller_My_Cart extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        Phpfox::getService('ynsocialstore.helper')->buildMenu();
        $this->template()->clearBreadCrumb();
        $this->template()->setBreadCrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'))
                        ->setBreadCrumb(_p('buyer_section'), $this->url()->makeUrl('ynsocialstore.my-cart'))
                        ->setBreadCrumb(_p('my_cart'));
        $iCartId = Phpfox::getService('ecommerce')->getMyCartId();
        list($iCount,$aMaxOfElement,$aMyCart,$bIsOnlyDigital) = Phpfox::getService('ecommerce')->getMyCartData('ynsocialstore');
        $this->template()
            ->setHeader(
                'cache', array(
                           'ynecommerce.js' => 'module_ecommerce',
                           'jquery.dd.js' => 'module_ynsocialstore',
                           'dd.css' => 'module_ynsocialstore'
                       )
            )
            ->assign(array(
                         'iCartId'   => $iCartId,
                         'iCount'    => $iCount,
                         'aMyCart'   => $aMyCart,
                         'aMaxElement' => json_encode($aMaxOfElement),
                         'sModule'   => 'ynsocialstore',
                         'sCorePath' => Phpfox::getParam('core.path_file'),
                     ));
//die(d($aMyCart));
        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
    }
}