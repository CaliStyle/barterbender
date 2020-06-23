<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/21/16
 * Time: 7:34 PM
 */
class Ynsocialstore_Component_Controller_My_Orders extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
        Phpfox::getService('ynsocialstore.helper')->buildMenu();
        $this->template()->setBreadCrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'))
                        ->setBreadCrumb(_p('buyer_section'), $this->url()->makeUrl('ynsocialstore.my-cart'))
                        ->setBreadCrumb(_p('my_orders'), $this->url()->makeUrl('ynsocialstore.my-orders'));
        return Phpfox::getLib('module')->setController('ecommerce.my-orders');
    }
}