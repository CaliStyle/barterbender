<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 11/21/16
 * Time: 11:35 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Controller_Order_Detail extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('ynsocialstore.helper')->buildMenu();

        $iOrderId = $this->request()->get('req3');
        $aOrder = Phpfox::getService('ecommerce.order')->getOrder($iOrderId);
        if (!$aOrder)
        {
            return;
        }

        if($aOrder['seller_user_id'] == Phpfox::getUserId()){
            $this->setParam('is_seller', true);
            $this->template()->setBreadCrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'))
                ->setBreadCrumb(_p('seller_section'), $this->url()->makeUrl('ynsocialstore.statistic'))
                ->setBreadCrumb(_p('all_sales'), $this->url()->makeUrl('ynsocialstore.all-sales'));
        }
        elseif($aOrder['buyer_user_id'] == Phpfox::getUserId()){
            $this->template()->setBreadCrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'))
                ->setBreadCrumb(_p('buyer_section'), $this->url()->makeUrl('ynsocialstore.my-cart'))
                ->setBreadCrumb(_p('my_orders'), $this->url()->makeUrl('ynsocialstore.my-orders'));
        } else {
            $this->setParam('menu_seller_hidden', true);
            $this->template()
                ->setBreadCrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'))
                ->setBreadCrumb(_p('ecommerce.manage_orders'), $this->url()->makeUrl('ynsocialstore.manage-orders'));
        }
        $this->setParam('isSocialStore',true);
        return Phpfox::getLib('module')->setController('ecommerce.order-detail');
    }
}