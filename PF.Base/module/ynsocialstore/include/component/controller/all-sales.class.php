<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 25/11/2016
 * Time: 13:37
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Controller_All_Sales extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
        Phpfox::getService('ynsocialstore.helper')->buildMenu();
        $this->template()->setBreadcrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'))
            ->setBreadcrumb(_p('seller_section'), $this->url()->permalink('ynsocialstore.statistic',null));

        $this->template()
            ->setTitle(_p('all_sales'))
            ->setBreadcrumb(_p('all_sales'), $this->url()->makeUrl('ynsocialstore.all-sales'));

        $this->setParam('aTypeManage', array(
                'sType' => 'all-sales',
            )
        );

        $this->setParam('is_seller', true);
        return Phpfox::getLib('module')->setController('ecommerce.manage-orders');
    }
}