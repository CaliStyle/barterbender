<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 11/18/16
 * Time: 10:33 AM
 */
class Ynsocialstore_Component_Controller_My_Requests extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('ynsocialstore.helper')->buildMenu();
        $this->template()->setBreadcrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'))
            ->setBreadcrumb(_p('seller_section'), $this->url()->permalink('ynsocialstore.statistic',null));

        $this->template()
            ->setTitle(_p('my-requests'))
            ->setBreadcrumb(_p('ecommerce.my_requests'), $this->url()->makeUrl('ynsocialstore.my-requests'));
        $this->setParam('is_seller', true);
        return Phpfox::getLib('module')->setController('ecommerce.my-requests');
    }
}