<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Service_Cart_Cart extends Phpfox_Service {

    public function get($iUserId)
    {
        $aRow = $this->database()
                ->select('cart.*')
                ->from(Phpfox::getT('ecommerce_cart'), 'cart')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = cart.cart_user_id')
                ->where('cart.cart_user_id = ' . (int) $iUserId)
                ->execute('getRow');
        
        return $aRow;
    }
    
    public function getProducts($iUserId)
    {
        $aRows = $this->database()
                ->select('cp.*')
                ->from(Phpfox::getT('ecommerce_cart_product'), 'cp')
                ->join(Phpfox::getT('ecommerce_cart'), 'cart', 'cart.cart_id = cp.cartproduct_cart_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = cart.cart_user_id')
                ->where('cart.cart_user_id = ' . (int) $iUserId.' AND cp.cartproduct_payment_status =\'init\'')
                ->execute('getRows');
        
        foreach ($aRows as $iKey => $aRow)
        {
            $aRows[$iKey]['cartproduct_data'] = (array) $aRow['cartproduct_data'];
        }
        
        return $aRows;
    }

    public function getProductsByProductId($iUserId,$iProductId,$sType)
    {

        $aRow = $this->database()
                ->select('cp.*')
                ->from(Phpfox::getT('ecommerce_cart_product'), 'cp')
                ->join(Phpfox::getT('ecommerce_cart'), 'cart', 'cart.cart_id = cp.cartproduct_cart_id')                
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = cart.cart_user_id')
                ->where('cart.cart_user_id = ' . (int) $iUserId.' AND cp.cartproduct_product_id = '.(int) $iProductId.' AND cp.cartproduct_type = \''.$sType.'\''.' AND cp.cartproduct_payment_status =\'init\'')
                ->execute('getRow');
        
        return $aRow;
    }  
    

}

?>
