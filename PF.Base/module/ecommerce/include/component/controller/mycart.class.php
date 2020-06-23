<?php

defined('PHPFOX') or exit('NO DICE!');

class ecommerce_Component_Controller_mycart extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        $sModule = $this->request()->get('req1');

        // We will not show my-cart when in module ecommerce
        if ($sModule == 'ecommerce')
            return Phpfox::getLib('module')->setController('error.404');

        $iCartId = Phpfox::getService('ecommerce')->getMyCartId();

        if ($iRemoveCartId = $this->request()->get('remove')) {
            Phpfox::getService('ecommerce.process')->removeCart($iRemoveCartId);
            $this->url()->send($sModule . '.mycart');
        }


        $aSessionMyCart = Phpfox::getLib('session')->get('ynecommerce_mycart');
        $aMyCart = Phpfox::getService('ecommerce')->getMyCartData();

        if (is_array($aSessionMyCart) && count($aSessionMyCart) && serialize($aSessionMyCart) == serialize($aMyCart)) {
            $aMyCart = $aSessionMyCart;
        } else {
            Phpfox::getLib('session')->set('ynecommerce_mycart', $aMyCart);
        }


        $this->template()
            ->setHeader(
                'cache', array(
                    'ynecommerce.js' => 'module_ecommerce'
                )
            )
            ->assign(array(
                'iCartId' => $iCartId,
                'aMyCart' => $aMyCart,
                'sModule' => $sModule,
                'sDefaultImage' => Phpfox::getParam('core.path') . 'module/auction/static/image/default_ava.png'
            ));

        $this->template()
            ->setTitle(_p('my_cart'))
            ->setBreadcrumb(_p('' . $sModule), $this->url()->makeUrl($sModule));

        if ($sModule == 'ecommerce') {
            $this->template()->setBreadcrumb(_p('my_cart'), $this->url()->makeUrl('ecommerce.mycart'));
            Phpfox::getService('ecommerce.helper')->buildMenu();
        }


    }
}

?>