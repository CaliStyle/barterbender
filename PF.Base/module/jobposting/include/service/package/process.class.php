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

class Jobposting_Service_Package_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('jobposting_package');
        $this->_sTableData = Phpfox::getT('jobposting_package_data');
	}
	
    /**
     * @param array $aVals
     * @return int
     */
	public function add($aVals)
	{
		$oParseInput = Phpfox::getLib('parse.input');
		
		$iId = $this->database()->insert($this->_sTable, array(
				'expire_number' => (int)$aVals['expire_number']?(int)$aVals['expire_number']:0,
				'post_number' => (int)$aVals['post_number']?(int)$aVals['post_number']:0,
				'name' => $oParseInput->clean($aVals['name'], 255),
				'expire_type' => $aVals['expire_type'],
				'fee' => (float)$aVals['fee']?(float)$aVals['fee']:0
			)
		);
		
		return $iId;
	}
	
    /**
     * @param int $iId
     * @param array $aVals
     */
	public function update($iId, $aVals)
	{
		$oParseInput = Phpfox::getLib('parse.input');
		
		$this->database()->update($this->_sTable, array(
			'name' => Phpfox::getLib('parse.input')->clean($aVals['name'], 255),
            'expire_number' => (int)$aVals['expire_number']?(int)$aVals['expire_number']:0,
            'post_number' => (int)$aVals['post_number']?(int)$aVals['post_number']:0,
			'expire_type' => $aVals['expire_type'],
            'fee' => (float)$aVals['fee']?(float)$aVals['fee']:0,
		), 'package_id = ' . (int) $iId);
		
		return true;
	}
	
	public function updateRemainingPost($iDataId)
	{
        $aPackage = Phpfox::getService('jobposting.package')->getByDataId($iDataId);
        if ($aPackage['post_number'] > 0 && $aPackage['remaining_post'] > 0)
        {
            $remaining_post = $aPackage['remaining_post'] - 1;
            $this->database()->update($this->_sTableData, array('remaining_post' => $remaining_post), 'data_id = '.(int)$iDataId);
        }
        
        return true;
	}
	
    /**
     * @param int $iId
     */
	public function delete($iId)
	{
		return $this->database()->delete($this->_sTable,'package_id = '.$iId);
	}
    
    /**
     * Pay packages
     * @param array $aId
     * @param int $iCompanyId
     * @param string $sReturnUrl
	 * @param bool $bReturn: return check out url or redirect
     */
    public function pay($aId, $iCompanyId, $sReturnUrl, $bReturn = false, $publishJob = 0, $featureJob = 0)
    {
        $sGateway = 'paypal';
        $sCurrency = PHpfox::getService('jobposting.helper')->getDefaultCurrency();
        $iFee = 0;
        $aInvoice = array('package_data' => array());
        $payment_type = 2; //package
        
		$sIds = implode(',', $aId);
        $aPackages = $this->database()->select('*')->from($this->_sTable)->where('package_id IN ('.$sIds.')')->execute('getRows');
        if(!count($aPackages))
        {
            return Phpfox_Error::set('Unable to find one of your selected packages. Please try again.');
        }
        
        foreach ($aPackages as $k => $aPackage)
        {
            #Fee
            $iFee += $aPackage['fee'];
            
            #Package data
            $aInsert = array(
                'company_id' => $iCompanyId,
                'package_id' => $aPackage['package_id'],
                'remaining_post' => $aPackage['post_number'],
                'valid_time' => !empty($aPackage['post_number'])?$aPackage['post_number']:0,
                'status' => 1
            );
            $iDataId = $this->database()->insert($this->_sTableData, $aInsert);
            
            #Invoice
            $aInvoice['package_data'][] = $iDataId;
        }
        
        if($publishJob)
		{
			$aInvoice['publish'] = $publishJob;
            $payment_type = 3; //package + publish
		}
        
		if($featureJob)
		{
			$iFee += PHpfox::getParam("jobposting.fee_feature_job");
			$aInvoice['feature'] = $featureJob;
            $payment_type = 4; //package + publish + feature
		}
        
        if($iFee <= 0)
        {
            $this->updatePayStatus($aInvoice, 'completed');
            if ($publishJob)
            {
                Phpfox::getService('jobposting.package.process')->updateRemainingPost($aInvoice['package_data'][0]);
                Phpfox::getService('jobposting.job.process')->publish($publishJob);
            }
            return true;
        }
        
        $aTransaction = array(
            'invoice' => serialize($aInvoice),
            'user_id' => Phpfox::getUserId(),
            'item_id' => $iCompanyId,
            'time_stamp' => PHPFOX_TIME,
            'amount' => $iFee,
            'currency' => $sCurrency,
            'status' => Phpfox::getService('jobposting.transaction')->getStatusIdByName('initialized'),
            'payment_type' => $payment_type
        );
		
        $iTransactionId = Phpfox::getService('jobposting.transaction.process')->add($aTransaction);
        /*
        $sPaypalEmail = Phpfox::getParam('jobposting.jobposting_admin_paypal_email');
        if(!$sPaypalEmail)
        {

        }
      
        $aParam = array(
            'paypal_email' => $sPaypalEmail,
            'amount' => $iFee,
            'currency_code' => $sCurrency,
            'custom' => 'jobposting|' . $iTransactionId,
            'return' => Phpfox::getParam('core.url_module') . 'jobposting/static/php/paymentcb.php?location='.$sReturnUrl,
            'recurring' => 0
        );

        if(Phpfox::isModule('younetpaymentgateways'))
        {        	
            if ($oPayment = Phpfox::getService('younetpaymentgateways')->load($sGateway, $aParam))
            {
            	$sCheckoutUrl = $oPayment->getCheckoutUrl();
				if($bReturn)
				{   
					return $sCheckoutUrl;
				}
				else
				{
					
					Phpfox::getLib('url')->forward($sCheckoutUrl);
				}
            }
        }
      	*/
        /*$aParam = Phpfox::getService('jobposting.ynpaypal')->initParam($iFee,$sCurrency,$iTransactionId,$sGateway,$sReturnUrl);*/
        /*$sCheckoutUrl = Phpfox::getService('jobposting.ynpaypal')->getCheckOutUrl($aParam);*/
        $sCheckoutUrl = Phpfox::getLib('url')->makeUrl('jobposting.payment',array('transactionid'=>$iTransactionId,'sUrl'=>base64_encode($sReturnUrl)));
        
        if($bReturn)
        {
            return $sCheckoutUrl;
        }
        else
        {

            Phpfox::getLib('url')->forward($sCheckoutUrl);
        }
        return Phpfox_Error::set(_p('can_not_load_payment_gateways_please_try_again_later'));
    }
    
    /**
     * Update bought packages after pay
     * @param array $aInvoice
     * @param string $sStatus
     */
    public function updatePayStatus($aInvoice, $sStatus)
    {
        if(isset($aInvoice['package_data']) && count($aInvoice['package_data']))
        {
            $iStatus = Phpfox::getService('jobposting.transaction')->getStatusIdByName($sStatus);
            foreach($aInvoice['package_data'] as $iDataId)
            {
                $this->updateBoughtPackageStatus($iDataId, $iStatus);
            }
        }
    }
    
    /**
     * Update bought package status same with payment transaction status
     * @param int $iDataId
     * @param int $iStatus
     */
    public function updateBoughtPackageStatus($iDataId, $iStatus)
    {
        $aUpdate = array('status' => $iStatus);
        
        if($iStatus == 3) //complete
        {
            $aUpdate['valid_time'] = PHPFOX_TIME;
            
            $aPackage = Phpfox::getService('jobposting.package')->getPackageByDataId($iDataId);
            switch($aPackage['expire_type'])
            {
                case 0: //never expire
                    $aUpdate['expire_time'] = 0;
                    break;
                case 1: //day
                    $aUpdate['expire_time'] = $aUpdate['valid_time'] + $aPackage['expire_number']*86400; //24*3600
                    break;
                case 2: //week
                    $aUpdate['expire_time'] = $aUpdate['valid_time'] + $aPackage['expire_number']*604800; //7*24*3600
                    break;
                case 3: //month
                    $aUpdate['expire_time'] = $aUpdate['valid_time'] + $aPackage['expire_number']*2592000; //30*7*24*3600
                    break;
                default:
                    #do nothing
            }
        }
        
        $this->database()->update($this->_sTableData, $aUpdate, 'data_id = '.$iDataId);
    }
	
	public function activepackage($id, $active){
		return $this->database()->update($this->_sTable,array(
			'active' => $active,
		),'package_id = '.$id);
	}
}

?>