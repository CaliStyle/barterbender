<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 11/3/16
 * Time: 3:41 PM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Product_Add_Attribute_Element extends Phpfox_Component
{
    public function process()
    {
        $iElementId = $this->getParam('iElementId', 0);
        $iProductId = $this->getParam('iProductId', 0);

        $aElement = array();
        if($iElementId){
            $aElement = Phpfox::getService('ynsocialstore.product')->getElementAttribute($iElementId);
        }

        /*
         *  If this product available in stock is 500. We will be limit total amount
         *  of all attribute below 500.
         */
        $aProduct = Phpfox::getService('ynsocialstore.product')->getProductById($iProductId);
        if (!$aProduct['enable_inventory'] || !$aProduct['product_quantity_main'])
        {
            $iAvailable = 0;
        }
        else
        {
            $iTotal = (int)Phpfox::getService('ynsocialstore.product')->getSumOfTotalAmountQuantityAttributes($iProductId, $iElementId);
            $iAvailable = ($aProduct['product_quantity_main'] - $iTotal) > 0 ? ($aProduct['product_quantity_main'] - $iTotal) : -1;
        }

        $this->template()->assign(array(
                'aElement' => $aElement,
                'iElementId' => $iElementId,
                'iProductId' => $iProductId,
                'sCorePath' => Phpfox::getParam('core.path'),
                'iAvailable' => $iAvailable,
            )
        );
        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
        return 'block';
    }
}