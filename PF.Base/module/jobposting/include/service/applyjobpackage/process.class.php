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

class Jobposting_Service_Applyjobpackage_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('jobposting_applyjobpackage');
        $this->_sTableData = Phpfox::getT('jobposting_applyjobpackage_data');
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
				'apply_number' => (int)$aVals['apply_number']?(int)$aVals['apply_number']:0,
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
            'apply_number' => (int)$aVals['apply_number']?(int)$aVals['apply_number']:0,
			'expire_type' => $aVals['expire_type'],
            'fee' => (float)$aVals['fee']?(float)$aVals['fee']:0,
		), 'package_id = ' . (int) $iId);
		
		return true;
	}
	
	public function updateRemainingApply($iDataId)
	{
        $aPackage = Phpfox::getService('jobposting.applyjobpackage')->getByDataId($iDataId);

        if($aPackage['apply_number'] == 0){
            return array('data_id' => $iDataId, 'remaining_apply' => 0);
        }
        else if ($aPackage['apply_number'] > 0 && $aPackage['remaining_apply'] > 0)
        {
            $remaining_apply = $aPackage['remaining_apply'] - 1;
            $this->database()->update($this->_sTableData, array('remaining_apply' => $remaining_apply), 'data_id = '.(int)$iDataId);
            return array('data_id' => $iDataId, 'remaining_apply' => $remaining_apply);
        }
        
        return false;
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
     * @param int $iUserId
     * @param string $sReturnUrl
	 * @param bool $bReturn: return check out url or redirect
     */
    public function pay($aId, $iUserId, $sReturnUrl, $bReturn = false, $applyJob = 0)
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
                'user_id' => $iUserId,
                'package_id' => $aPackage['package_id'],
                'remaining_apply' => $aPackage['apply_number'],
                'status' => 1
            );
            $iDataId = $this->database()->insert($this->_sTableData, $aInsert);
            
            #Invoice
            $aInvoice['package_data'][] = $iDataId;
        }
        
        if($applyJob)
		{
			$aInvoice['publish'] = $applyJob;
            $payment_type = 3; //package + publish
		}
        
        if($iFee <= 0)
        {
            $this->updatePayStatus($aInvoice, 'completed');
            if ($applyJob)
            {
                Phpfox::getService('jobposting.applyjobpackage.process')->updateRemainingApply($aInvoice['package_data'][0]);
                Phpfox::getService('jobposting.job.process')->publish($applyJob);
            }
            return true;
        }
        
        $aTransaction = array(
            'invoice' => serialize($aInvoice),
            'user_id' => Phpfox::getUserId(),
            'item_id' => $iUserId,
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
        $aParam = Phpfox::getService('jobposting.ynpaypal')->initParam($iFee,$sCurrency,$iTransactionId,$sGateway,$sReturnUrl);
        $sCheckoutUrl = Phpfox::getService('jobposting.ynpaypal')->getCheckOutUrl($aParam);
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

    public function updatePayStatusOnePackage($aInvoice, $sStatus)
    {
        if(isset($aInvoice['package_data']))
        {
            $iStatus = Phpfox::getService('jobposting.transaction')->getStatusIdByName($sStatus);
            $this->updateBoughtPackageStatus($aInvoice['package_data'], $iStatus);
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
            
            $aPackage = Phpfox::getService('jobposting.applyjobpackage')->getPackageByDataId($iDataId);
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

    public function addPackageData($aVals = array()){
        $aInsert = array(
            'user_id' => $aVals['user_id'],
            'package_id' => $aVals['package_id'],
            'remaining_apply' => $aVals['remaining_apply'],
            'valid_time' => !empty($aVals['valid_time'])?$aVals['valid_time']:0,
            'expire_time' => !empty($aVals['expire_time'])?$aVals['expire_time']:0,
            'status' => $aVals['status']
        );

        $iDataId = $this->database()->insert($this->_sTableData, $aInsert);
        return $iDataId;
    }
}

?>