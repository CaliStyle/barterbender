<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Service_Cart_Process extends Phpfox_Service {

    public function add($aVals)
    {
        $aInsert = array(
            'cart_user_id' => isset($aVals['user_id']) ? (int) $aVals['user_id'] : Phpfox::getUserId(),
            'cart_creation_datetime' => PHPFOX_TIME,
            'cart_modification_datetime' => 0
        );
        
        $iCartId = $this->database()->insert(Phpfox::getT('ecommerce_cart'), $aInsert);
        
        return $iCartId;
    }

    public function edit($iUserId)
    {
        $aUpdate = array(
            'cart_modification_datetime' => PHPFOX_TIME
        );
        
        return $this->database()->update(Phpfox::getT('ecommerce_cart'), $aUpdate, 'cart_user_id = ' . (int) $iUserId);
    }
    
    public function addProducts($aVals)
    {
        $iCartId = isset($aVals['cart_id']) ? (int) $aVals['cart_id'] : 0;
        $iProductId = isset($aVals['product_id']) ? (int) $aVals['product_id'] : 0;
        $iQuantity = isset($aVals['quantity']) ? (int) $aVals['quantity'] : 0;
        $fPrice = isset($aVals['price']) ? (float) $aVals['price'] : 0.0;
        $sProductData = isset($aVals['product_data']) ? json_encode($aVals['product_data']) : '';
        $sType = isset($aVals['type']) ? $aVals['type'] : '';
        $sCurrency = isset($aVals['currency']) ? $aVals['currency'] : 'USD';
        $iAttributeId = isset($aVals['attribute_id']) ? $aVals['attribute_id'] : 0;
        
        if (!in_array($sType, array('bid','buy','offer')))
        {
            return false;
        }
        
        $aInsert = array(
            'cartproduct_cart_id' => $iCartId,
            'cartproduct_product_id' => $iProductId,
            'cartproduct_quantity' => $iQuantity,
            'cartproduct_data' => $sProductData,
            'cartproduct_type' => $sType,
            'cartproduct_price' => $fPrice,
            'cartproduct_module' => isset($aVals['module']) ? $aVals['module'] : 'auction',
            'cartproduct_currency' => $sCurrency,
            'cartproduct_payment_status' => 'init',
            'cartproduct_attribute_id' => $iAttributeId
        );
        
        $iCartProductId = $this->database()->insert(Phpfox::getT('ecommerce_cart_product'), $aInsert);
        
        return $iCartProductId;
    }
}

?>