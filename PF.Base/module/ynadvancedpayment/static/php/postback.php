<?php
require_once "cli.php";
$params = $_REQUEST;
$gatewayType = 'iTransact';
if( !empty($gatewayType) && 'index' !== $gatewayType )
{
  $params['gatewayType'] = $gatewayType;
} 
else 
{
  $gatewayType = null;
}
// Log silent response
// check gateway supported
if($gatewayType != 'iTransact' || !Phpfox::isModule('ynadvancedpayment'))
{
	// Gateway was not supported
	Phpfox::getService('api.gateway.process')->addLog('itransact', Phpfox::endLog());
      echo 'ERR';
      exit(1);
}
// Get payment_status
if($params['x_response_code'] == 1)
{
	$paymentStatus = 'okay';
}
else 
{
	// Gateway was not supported
	Phpfox::getService('api.gateway.process')->addLog('itransact', Phpfox::endLog());
      echo 'ERR';
      exit(1);
}
// Get gateway
$activeGateway = Phpfox::getService('ynadvancedpayment.paymentgateway')->getGatewayById('itransact', false);
if(!isset($activeGateway['gateway_id'])){
  // Gateway detection failed
	Phpfox::getService('api.gateway.process')->addLog('itransact', Phpfox::endLog());
  echo 'ERR';
  exit(1);
}
// Process
try 
{
    // Fetch by x_subscription_id
    if( !empty($params['x_subscription_id']) && isset($params['x_subscription_paynum']) ) 
    {
    	$ynsubscription = Phpfox::getService('ynadvancedpayment.paymentgateway')->getYNSubscriptionByGatewayIdAndGetawaySubscriptionId(
    		$activeGateway['gateway_id']
    		, $params['x_subscription_id']
		); 
		if(!isset($ynsubscription['subscription_id'])){
			Phpfox::getService('api.gateway.process')->addLog('itransact', Phpfox::endLog());
		      echo 'ERR';
		      exit(1);
		}
    }
	else 
	{
		Phpfox::getService('api.gateway.process')->addLog('itransact', Phpfox::endLog());
	      echo 'ERR';
	      exit(1);
	}
	// Process generic Silent Post Responce data ------------------------------------------------
 	$module = 'subscribe';
	
	if (Phpfox::isModule($module))
	{
		if (Phpfox::hasCallback($module, 'paymentApiCallback'))
		{
			$sStatus = 'completed';		
			if ($sStatus !== null)
			{
				// insert subscribe_purchase table 
				$oldPurchase = Phpfox::getService('subscribe.purchase')->getPurchase((int)$ynsubscription['purchase_id']);
				if(isset($oldPurchase['purchase_id'])){
					$iPurchaseId = Phpfox::getService('ynadvancedpayment.process')->addSubscribePurchase(array(
			            'package_id' => $ynsubscription['package_id'],
			            'user_id' => $ynsubscription['user_id'],
			            'currency_id' => $oldPurchase['currency_id'] ,
			            'price' => (float)$params['x_amount'],
			            'status' => $sStatus,
					));

					if($iPurchaseId)
					{
						Phpfox::callback($module . '.paymentApiCallback', array(
								'gateway' => 'itransact',
								'status' => $sStatus,
								'item_number' => $iPurchaseId,
								'total_paid' => (float)$params['x_amount']
							)
						);
					    // Exit
					    echo 'OK';
					    Phpfox::getService('api.gateway.process')->addLog('itransact', Phpfox::endLog());
					    exit(0);						
					}
				}
			}
			else 
			{
			}
		}						
		else 
		{
		}
	}
	else 
	{
	} 	


} catch( Exception $e ) {
  // Silent post validation failed
  echo 'ERR';
  Phpfox::getService('api.gateway.process')->addLog('itransact', Phpfox::endLog());
  exit(1);
}


