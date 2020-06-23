<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/21/16
 * Time: 7:34 PM
 */
class Ynsocialstore_Component_Controller_Statistic extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
        Phpfox::getService('ynsocialstore.helper')->buildMenu();
        $this->setParam('is_seller', true);
        $this->template()->clearBreadCrumb();
        $this->template()->setBreadcrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'))
            ->setBreadcrumb(_p('seller_section'),$this->url()->permalink('ynsocialstore.statistic',null))
            ->setBreadcrumb(_p('statistics'), '');

        $this->template()
            ->setTitle(_p('statistic'));

        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId());
        $iTotalStores = (int)Phpfox::getService('ynsocialstore')->countStoreOfUserId(Phpfox::getUserId());
        $iTotalProducts = (int)Phpfox::getService('ynsocialstore.product')->getCountProduct('ep.product_status NOT IN  ("deleted") AND st.status != "deleted" AND ep.user_id ='.(int)Phpfox::getUserId());
        $iTotalOrders = (int)Phpfox::getService('ecommerce.order')->getTotalManageOrders('ynsocialstore');
        $fTotalSales = (float)Phpfox::getService('ecommerce.order')->getTotalSaleOfMyItem('ynsocialstore_product');
        $iTotalItemSold = (int)Phpfox::getService('ecommerce.order')->getTotalSoldOfMyItem('ynsocialstore_product');
        $aForms = array();
        $sDefaultCurrency = Phpfox::getService('core.currency')->getDefault();
        $this->template()
            ->setPhrase(array(
                            'ecommerce.publish_fee',
                            'ecommerce.commission_fee',
                            'ecommerce.featured_fee',
                            'ecommerce.number_of_products_sold'
                        ))
            ->setHeader('cache', array(
                'jquery.flot.js' => 'module_ecommerce',
                'jquery.flot.time.js' => 'module_ecommerce',
                'jquery.flot.stack.js' => 'module_ecommerce'
            ))
            ->assign(array(
                         'iTotalStores' => $iTotalStores,
                         'iTotalProducts' => $iTotalProducts,
                         'iTotalOrders' => $iTotalOrders,
                         'fTotalSales' => $fTotalSales,
                         'iTotalItemSold' => $iTotalItemSold,
                         'sDefaultCurrency' => $sDefaultCurrency,
                         'aForms' => $aForms
                     ));

    }
}