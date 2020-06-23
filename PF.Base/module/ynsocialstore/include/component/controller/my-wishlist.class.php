<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/21/16
 * Time: 7:34 PM
 */
class Ynsocialstore_Component_Controller_My_Wishlist extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        Phpfox::getService('ynsocialstore.helper')->buildMenu();

        $iPage = $this->request()->getInt('page');
        $iSize = 12;
        $iCount = 0;

        $aProducts = Phpfox::getService('ynsocialstore.product')->getMyWishListProduct($iPage, $iSize, $iCount);

        // Set page id
        PhpFox::getLib('pager')->set(array(
            'page' => $iPage,
            'size' => $iSize,
            'count' => $iCount,
        ));

        $this->template()->setBreadCrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'))
            ->setBreadCrumb(_p('buyer_section'), $this->url()->makeUrl('ynsocialstore.my-cart'))
            ->setBreadCrumb(_p('my_wishlist'), $this->url()->makeUrl('ynsocialstore.my-wishlist'));

        $this->template()->assign(array(
                'aMyWishList' => $aProducts,
                'bIsWishList' => true,
            )
        );

        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
    }
}