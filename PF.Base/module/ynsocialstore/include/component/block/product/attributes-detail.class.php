<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 11/9/16
 * Time: 09:23
 */
class Ynsocialstore_Component_Block_Product_Attributes_Detail extends Phpfox_Component
{
    public function process()
    {
        $iProductId = $this->getParam('iProductId');
        if((int)$iProductId < 0){
            return false;
        }
        $aProduct = $this->getParam('aProduct');

        $isNoAttribute = $isAlreadyBuy =  false;
        $aPackage = Phpfox::getService('ynsocialstore.package')->getPackageByStoreId($aProduct['item_id']);
        if(!$aPackage ||  $aPackage['enable_attribute'] != 1 || $aProduct['product_type'] == 'digital')
        {
            $isNoAttribute = true;
        }
        $sDefaultSymbol = $this->getParam('sDefaultSymbol');
        if(empty($aProduct))
        {
            return false;
        }
        $aAttributeInfo = ['type' => $aProduct['attribute_style'],'name' => $aProduct['attribute_name']];
        $aElements = Phpfox::getService('ynsocialstore.product')->getAllElements($iProductId);
        $aProduct['quantity_in_cart_order']  = Phpfox::getService('ynsocialstore.product')->countOrderAndCartOfProductByUser($iProductId,(int)Phpfox::getUserId());
        $iTotalInCart = Phpfox::getService('ynsocialstore.product')->countTotalOnCartByProduct($iProductId,(int)Phpfox::getUserId());
        if($aProduct['enable_inventory'] && $aProduct['product_type'] == 'physical')
        {
            if($aProduct['product_quantity_main'] > 0)
            {
                if($aProduct['product_quantity'] > $aProduct['max_order'] && $aProduct['max_order'] > 0)
                {
                    if($aProduct['max_order'] > $aProduct['quantity_in_cart_order'])
                    {
                        $aProduct['max_quantity_can_add'] = abs($aProduct['max_order'] - $aProduct['quantity_in_cart_order']);
                    }
                    else{
                        $aProduct['max_quantity_can_add'] = 0;
                    }
                }
                else{
                    $aProduct['max_quantity_can_add'] = $aProduct['product_quantity'] - $iTotalInCart;
                }
            }
            elseif($aProduct['max_order'] > 0)
            {
                if($aProduct['max_order'] > $aProduct['quantity_in_cart_order'])
                {
                    $aProduct['max_quantity_can_add'] = abs($aProduct['max_order'] - $aProduct['quantity_in_cart_order']);
                }
                else{
                    $aProduct['max_quantity_can_add'] = 0;
                }
            }
            else{
                $aProduct['max_quantity_can_add'] = 'unlimited';
            }
        }
        else{
            $aProduct['max_quantity_can_add'] = 'unlimited';
        }
        if($aProduct['max_quantity_can_add'] < 0) $aProduct['max_quantity_can_add'] = 0;
        if(empty($aElements) || $aProduct['product_type'] == 'digital'){
            if(Phpfox::getService('ynsocialstore.product')->checkIsBuyThisProduct($iProductId))
            {
                $isAlreadyBuy = true;
            }
            $isNoAttribute = true;
        }
        else {
            if($aProduct['product_type'] == 'physical') {
                $aElementsOnUserCart = Phpfox::getService('ynsocialstore.product')->countOnCartProductByUser($iProductId, (int)Phpfox::getUserId());
                $aElementsOnOrder = Phpfox::getService('ynsocialstore.product')->countOrderOfProduct($iProductId);
                foreach ($aElements as $key => $aElement) {
                    $iInUsed = 0;
                    if (isset($aElementsOnUserCart[$aElement['attribute_id']])) {
                        $iInUsed = $aElementsOnUserCart[$aElement['attribute_id']];
                    }
                    if (isset($aElementsOnOrder[$aElement['attribute_id']])) {
                        $iInUsed = $iInUsed + $aElementsOnOrder[$aElement['attribute_id']];
                    }
                    $aElements[$key]['quantity_sold'] = $iInUsed;
                    $aElements[$key]['remain_of_attribute'] =($aElement['quantity'] > 0) ? ($aElement['quantity'] - $iInUsed) : 0;
                }
            }

        }

        $this->template()->assign([
                'iProductId' => $iProductId,
                'sDefaultSymbol' => $sDefaultSymbol,
                'aAttributeInfo' => $aAttributeInfo,
                'aElements' => $aElements,
                'isNoAttribute' => $isNoAttribute,
                'aProduct' => $aProduct,
                'isAlreadyBuy' => $isAlreadyBuy,
                'sCorePath' => Phpfox::getParam('core.path_file')
                                  ]);

    }
}