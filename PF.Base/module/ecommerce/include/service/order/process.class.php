<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Service_Order_Process extends Phpfox_Service {

    public function updateStatus($iOrderId, $sStatus)
    {
        if (!in_array($sStatus, array('new', 'shipped', 'cancel')))
        {
            return false;
        }
        
        return $this->database()->update(Phpfox::getT('ecommerce_order'), array('order_status' => $sStatus), 'order_id = ' . (int) $iOrderId);
    }

    public function updateOrder($iOrderId, $aUpdateInfo)
    {
        return $this->database()->update(Phpfox::getT('ecommerce_order'), $aUpdateInfo, 'order_id = ' . (int) $iOrderId);
    }

    public function updateTotalOrderProduct($iProductId){
        $iTotalOrder =  $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('ecommerce_order_product'),'eop')
                ->join(Phpfox::getT('ecommerce_order'),'ep','ep.order_id = eop.orderproduct_order_id')
                ->where('eop.orderproduct_product_id = '.(int)$iProductId.' AND ep.order_payment_status = \'completed\'')
                ->execute('getSlaveField');
        return $this->database()->update(Phpfox::getT('ecommerce_product'), array('total_orders' => $iTotalOrder), 'product_id = ' . (int) $iProductId);
    }
	public function saveOrder($aOrders, $aShipping, $bUsingAdaptive = false, $sCurrency, $sModule = 'auction')
    {
		$oFilter = Phpfox::getLib('parse.input');		
        $iOrderId = 0;
        $aIdOrder = array();
        $aParamOrder = array();
        $aParamOrder['amount'] = 0;
        $aParamOrder['item_name'] = array(); 
        if($bUsingAdaptive) {

            $iTotalCommissionAdmin = 0;
            $aSellerCommission = array();
        }
       
    	if(count($aOrders)){
    		foreach ($aOrders as $keyOrder => $aOrder) {
    			
                $iCommissionAdminPerOrder = 0;
                $aParamOrder['amount'] += $aOrder['total_price'];

                /*update order*/
				$aInsertOrder = array(
                 'user_id'          => Phpfox::getUserId(),
                 'seller_id'        => $aOrder['seller_id'],
                 'module_id' => $sModule,
                 'order_code' => $this->randomCode(),
                 'order_item_count' => count($aOrder['product']),
                 'order_creation_datetime' => PHPFOX_TIME,
                 'order_total_price' => $aOrder['total_price'],
                 'order_currency' => $sCurrency,
                 'order_commission_rate' => 0,
                 'order_commission_value' => 0,
                 'order_commission_type' => 'normal',
                 'order_status' => 'new',
                 'order_payment_status' => 'initialized',
                 'order_note' => $oFilter->clean($aOrder['checkout_message']),
                 'order_note_parsed' => $oFilter->prepare($aOrder['checkout_message']),
                 'order_delivery_name' => isset($aShipping['address_user_name'])?$aShipping['address_user_name']:'',
                 'order_delivery_location_address' => isset($aShipping['address_customer_street'])?$aShipping['address_customer_street']:'',
                 'order_delivery_location_address_2' => isset($aShipping['address_customer_street_2'])?$aShipping['address_customer_street_2']:'',
                 'order_delivery_country_iso' => isset($aShipping['address_customer_country_iso'])?$aShipping['address_customer_country_iso']:'',
                 'order_delivery_country_child_id' => isset($aShipping['address_customer_country_child_id'])?$aShipping['address_customer_country_child_id']:0,
                 'order_delivery_city' => isset($aShipping['address_customer_city'])?$aShipping['address_customer_city']:'',
                 'order_delivery_postal_code' => isset($aShipping['address_customer_postal_code'])?$aShipping['address_customer_postal_code']:'',
                 'order_delivery_phone_number' => isset($aShipping['address_customer_phone_number'])?($aShipping['address_customer_country_code'].'-'.$aShipping['address_customer_city_code'].'-'.$aShipping['address_customer_phone_number']):'',
                 'order_delivery_mobile_number' => isset($aShipping['address_customer_mobile_number'])?$aShipping['address_customer_mobile_number']:'',
                 'order_buyfrom_id' => isset($aOrder['order_buyfrom_id']) ? $aOrder['order_buyfrom_id'] : 0,
                 'order_buyfrom_type' => isset($aOrder['order_buyfrom_type']) ? $aOrder['order_buyfrom_type'] : 'detail',
             	);

                $iCommissionRate = 0;

           		$iOrderId = $this->database()->insert(Phpfox::getT('ecommerce_order'), $aInsertOrder);
    			$aIdOrder[] = $iOrderId;

                /*update order product*/
                $aInsertOrderProduct = array();
                if(!empty($aOrder['product'])){
                    foreach ($aOrder['product'] as $aProduct) {
                        $aQuickProduct = Phpfox::getService('ecommerce')->getQuickProductById($aProduct['product_id']);
                       
                       
                        $aParamOrder['item_name'][] = $aQuickProduct['name'];

                        $aInsertOrderProduct = array(
                            'orderproduct_order_id'         => $iOrderId, 
                            'orderproduct_product_id'       => $aProduct['product_id'],
                            'orderproduct_parent_id'        => isset($aProduct['item_id']) ? isset($aProduct['item_id']) : 0, //This is code support for social store.
                            'orderproduct_product_name'     => $aQuickProduct['name'], 
                            'orderproduct_product_price'    => $aProduct['product_price'], 
                            'orderproduct_product_quantity' => $aProduct['product_quantity'], 
                            'orderproduct_final_price'      => $aProduct['product_price'],
                            'orderproduct_attribute_id'     => isset($aProduct['attribute_id']) ? $aProduct['attribute_id'] : 0,
                            'orderproduct_module'           => isset($aProduct['module']) ? $aProduct['module'] : 'auction',
                        );

                        if('auction' == $aQuickProduct['product_creating_type']){
                            $iCommissionAdminPerOrder += $aProduct['product_price']*$aProduct['product_quantity'] * Phpfox::getService('ecommerce.helper')->getUserParam('auction.commission_for_admin_when_user_buy_auction',$aOrder['seller_id']) / 100;
                            $iCommissionRate = Phpfox::getService('ecommerce.helper')->getUserParam('auction.commission_for_admin_when_user_buy_auction',$aOrder['seller_id']);
                        }
                        if('ynsocialstore_product' == $aQuickProduct['product_creating_type']){
                            $iCommissionAdminPerOrder += $aProduct['product_price']*$aProduct['product_quantity'] * Phpfox::getService('ecommerce.helper')->getUserParam('ynsocialstore.product_commit_fee',$aOrder['seller_id']) / 100;
                            $iCommissionRate = Phpfox::getService('ecommerce.helper')->getUserParam('ynsocialstore.product_commit_fee',$aOrder['seller_id']);
                        }

                       $this->database()->insert(Phpfox::getT('ecommerce_order_product'), $aInsertOrderProduct,$iOrderId);
                    }
                }  

                /*using for adaptive payment*/
                if($bUsingAdaptive) {
                   
                    $iCommissionSellerPerOrder = $aOrder['total_price'] - $iCommissionAdminPerOrder;
                    $aGatewayValues = Phpfox::getService('api.gateway')->getUserGateways($aOrder['seller_id']);
                    if(array_key_exists($aGatewayValues['paypal'] ['gateway']['paypal_email'],$aSellerCommission)){
                        
                        $aSellerCommission[$aGatewayValues['paypal'] ['gateway']['paypal_email']]['amount'] +=$iCommissionSellerPerOrder; 
                        $aSellerCommission[$aGatewayValues['paypal'] ['gateway']['paypal_email']]['invoice_id'][] = '#'.$iOrderId;   
                    }
                    else{

                        $aSellerCommission[$aGatewayValues['paypal'] ['gateway']['paypal_email']]['email'] = $aGatewayValues['paypal'] ['gateway']['paypal_email'];
                        $aSellerCommission[$aGatewayValues['paypal'] ['gateway']['paypal_email']]['amount'] = $iCommissionSellerPerOrder;
                        $aSellerCommission[$aGatewayValues['paypal'] ['gateway']['paypal_email']]['invoice_id'][] = '#'.$iOrderId;   
                    }

                    $aUpdateOrder['order_commission_type'] = 'adaptive';  
                    $iTotalCommissionAdmin += $iCommissionAdminPerOrder;
                }

                    $aUpdateOrder['order_commission_value'] = $iCommissionAdminPerOrder;
                    $aUpdateOrder['order_commission_rate'] = $iCommissionRate;
                    $this->database()->update(Phpfox::getT('ecommerce_order'), $aUpdateOrder,'order_id ='.(int)$iOrderId);

    		}
    	}
        
        $aParamOrder['item_name'] = implode("|", $aParamOrder['item_name']);

         if($bUsingAdaptive) {
                $aGatewayPaypalSetting = Phpfox::getService('ecommerce.helper')->getGatewaySetting('paypal');
                $aParamOrder['admin']['email']  =  $aGatewayPaypalSetting['paypal_email'];
                $aParamOrder['admin']['amount'] = $iTotalCommissionAdmin;
                $aParamOrder['admin']['invoice_id'][] = '#admin';

                if(array_key_exists($aParamOrder['admin']['email'] ,$aSellerCommission)){
                    $aSellerCommission[$aParamOrder['admin']['email']]['amount'] += $iTotalCommissionAdmin;
                    $aSellerCommission[$aParamOrder['admin']['email']]['invoice_id'][] = '#admin';
                    unset($aParamOrder['admin']);
                }

                $aParamOrder['seller']          = $aSellerCommission;

         }
        return array($aIdOrder,$aParamOrder);
    }

    public function updateStatusManageOrders($order_id,$status){
       
        $aUpdate = array(
            'order_status' => $status,
        );

        $aOrder = Phpfox::getService('ecommerce.order')->getOrderById($order_id);

        if($aOrder['order_status'] == $status){
            return false;
        }

        $this->database()->update(Phpfox::getT('ecommerce_order'), $aUpdate, 'order_id = ' . $order_id);


        $sLink = Phpfox::permalink('ecommerce.my-orders', null, null);
        $iReceiveId = $aOrder['user_id'];
        $aUser = Phpfox::getService('user')->getUser($iReceiveId);
        $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
        $email = $aUser['email'];
        $iProductId = 1;
        $aExtraData = array();
        $aExtraData['order_id'] = $order_id;
        $aExtraData['url'] = $sLink;
        $aEmail = Phpfox::getService('ecommerce.mail')->getEmailMessageFromTemplate($aOrder['module_id'],'order_updated' , $language_id ,  $iReceiveId, $iProductId, $aExtraData);
        Phpfox::getService('ecommerce.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);
        Phpfox::getService('notification.process')->add('ecommerce_statusorder',$aOrder['order_id'], $aOrder['user_id'], $aOrder['user_id']);

        return true;
    
    }

    function randomCode() {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $code = array(); //remember to declare $code as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $code[] = $alphabet[$n];
        }
        return implode($code); //turn the array into a string
    }
}

?>