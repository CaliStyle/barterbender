<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		VuDP, AnNT
 * @package  		Module_jobposting
 */

class JobPosting_Service_YnPayPal extends Phpfox_service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        
    }
    
	public function getCheckOutUrl($aParam)
	{
		$aTempParam = array();
		$sUrl = $aParam['url'];

		foreach ($aParam['param'] as $sKey => $sValue) {
			if ($sValue) {
				$aTempParam[] = $sKey . "=" . $sValue;
			}
		}
		$aTempParam = implode('&', $aTempParam);
		$sUrl .= '?' . $aTempParam;

		return $sUrl;
	}
	public function initParam($iFee,$sCurrency,$iTransactionId,$sGateway,$sReturnUrl)
	{
		$sPaypalEmail = "";
		$api_gateway = new Api_Service_Gateway_Gateway();
		$activePaypal =$api_gateway->getActive();

		if (!isset($activePaypal[0]["custom"]["paypal_email"]))
		{
			$sError =  _p('administrator_does_not_have_paypal_email_please_contact_him_her_to_update_it');
			Phpfox_Error::display($sError);
		}
		$is_test = true;
		for($i=0;$i<count($activePaypal);$i++)
		{
			$item = $activePaypal[$i];
			if ($item["gateway_id"]=="paypal")
			{
				if (!isset($item["custom"]["paypal_email"]))
				{
					$sError =  _p('administrator_does_not_have_paypal_email_please_contact_him_her_to_update_it');
					Phpfox_Error::display($sError);
				}
				$sPaypalEmail = $item["custom"]["paypal_email"]["value"];
				$is_test = $item["is_test"];
			}
		}

		$aParam = array(
			'paypal_email' => $sPaypalEmail,
			'amount' => $iFee,
			'currency_code' => $sCurrency,
			'custom' => 'jobposting|' . $iTransactionId,
			'return' => Phpfox::getParam('core.path_file').'module/jobposting/static/php/paymentcb.php?location='.$sReturnUrl,
			'recurring' => 0
		);
		$aParam["setting"]  = serialize($aParam);
		$aParam["is_test"]  = $is_test;
		$aParam["item_name"]  = 'jobposting|' . $iTransactionId;
		$aParam['item_number']  = 1;
		$gateway = new Phpfox_Gateway();
		$oPayment_fox = $gateway->load($sGateway, $aParam);
		$aParam = $oPayment_fox->getForm();
		return $aParam;

	}

}

?>