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

class JobPosting_Component_Controller_Applyfee extends Phpfox_Component 
{
    public function process(){
        PHpfox::isUser(true);
        $p = PHPFOX_DIR_FILE . PHPFOX_DS . 'pic' . PHPFOX_DS . 'jobposting' . PHPFOX_DS;
        if (!is_dir($p)) {
            if (!@mkdir($p, 0777, 1)) {
            }
        }

        $jobID = $this->request()->get('jobID');
        $package = $this->request()->get('popup_packages');
        $paypal = $this->request()->get('popup_paypal');

        $aJob = Phpfox::getService('jobposting.job')->getJobByJobId((int)$jobID);
        $title = '';
        if(isset($aJob['job_id'])){
            $title = $aJob['title'];
        }
        $sUrl = Phpfox::getLib('url')->permalink('jobposting', $jobID, $title);

        // group cannot apply job 
        if(!isset($aJob['job_id'])
        ){
            $this->url()->send($sUrl, null, _p('unable_to_find_the_job_you_want_to_apply'));
            return false;
        }

        if (!$package)
        {
            $this->url()->send($sUrl, null, _p('please_select_a_package_to_apply_this_job'));
            return false;
        }

        $bSuccess = false;
        if ($paypal == 0) //select existing packages
        {
            $aPackage = Phpfox::getService('jobposting.applyjobpackage')->getByDataId($package, true);
            if (!$aPackage)
            {
                $this->url()->send($sUrl, null, _p('invalid_package'));
                return false;
            }

            $ret = Phpfox::getService('jobposting.applyjobpackage.process')->updateRemainingApply($package);
            if(isset($ret['data_id'])){
                $bSuccess = true;
                // foward apply job page
                Phpfox::getComponent('jobposting.applyjob'
                    , array('bNoTemplate' => true, 'fromComponent' => 'applyfee', 'jobID' => $jobID)
                    , 'controller'
                );
                return true;
            } else {
                $bSuccess = false;
                $this->url()->send($sUrl, null, _p('invalid_package'));
                return false;
            }            
        }
        elseif ($paypal == 1) //buy new
        {
            $aPackage = Phpfox::getService('jobposting.applyjobpackage')->getById($package);
            if (!$aPackage)
            {
                $this->url()->send($sUrl, null, _p('invalid_package'));
                return false;
            }

            #Package data
            $aVals = array(
                'user_id' => Phpfox::getUserId(),
                'package_id' => $aPackage['package_id'],
                'remaining_apply' => $aPackage['apply_number'],
                'status' => 1   //  1 : not paid, 3 : paid
            );
            $iDataId = Phpfox::getService('jobposting.applyjobpackage.process')->addPackageData($aVals);
            if((int)$iDataId <= 0){
                $this->url()->send($sUrl, null, _p('invalid_package'));
                return false;
            }            

            $jobApplicationFee = (int)$aPackage['fee'];
            $sGateway = 'paypal';
            $sCurrency = PHpfox::getService('jobposting.helper')->getDefaultCurrency();
            $aInvoice = array('jobID' => $jobID
                , 'package_data' => $iDataId
                , 'jobApplicationFee' => $jobApplicationFee
                , 'sCurrency' => $sCurrency
            );
            $payment_type = 7; // pay fee for apply job 

            $aTransaction = array(
                'invoice' => serialize($aInvoice),
                'user_id' => Phpfox::getUserId(),
                'item_id' => $jobID,
                'time_stamp' => PHPFOX_TIME,
                'amount' => $jobApplicationFee,
                'currency' => $sCurrency,
                'status' => Phpfox::getService('jobposting.transaction')->getStatusIdByName('initialized'),
                'payment_type' => $payment_type
            );
            if($jobApplicationFee <= 0)
            {
                $aTransaction['status'] = Phpfox::getService('jobposting.transaction')->getStatusIdByName('completed');
            }

            $iTransactionId = Phpfox::getService('jobposting.transaction.process')->add($aTransaction);
            if($jobApplicationFee > 0){
                // foward paypal 
                /*$sPaypalEmail = Phpfox::getParam('jobposting.jobposting_admin_paypal_email');
                if(!$sPaypalEmail)
                {
                    Phpfox_Error::set(_p('administrator_does_not_have_paypal_email_please_contact_him_her_to_update_it'));
                    $this->url()->send($sUrl, null, null);
                }

                $aParam = array(
                    'paypal_email' => $sPaypalEmail,
                    'amount' => $jobApplicationFee,
                    'currency_code' => $sCurrency,
                    'custom' => 'jobposting|' . $iTransactionId,
                    'return' => Phpfox::getParam('core.url_module') . 'jobposting/static/php/paymentcb.php?location='.$sReturnUrl,
                    'recurring' => 0
                );*/
                $sReturnUrl = Phpfox::getLib('url')->permalink('jobposting.applyjob', $iTransactionId);
                /*
                if(Phpfox::isModule('younetpaymentgateways'))
                {
                    if ($oPayment = Phpfox::getService('younetpaymentgateways')->load($sGateway, $aParam))
                    {
                        $sCheckoutUrl = $oPayment->getCheckoutUrl();
                        Phpfox::getLib('url')->forward($sCheckoutUrl);
                    }
                }
                */
                /*
                $aParam = Phpfox::getService('jobposting.ynpaypal')->initParam($jobApplicationFee,$sCurrency,$iTransactionId,$sGateway,$sReturnUrl);
                $sCheckoutUrl = Phpfox::getService('jobposting.ynpaypal')->getCheckOutUrl($aParam);
                */
                $sCheckoutUrl = Phpfox::getLib('url')->makeUrl('jobposting.payment',array('transactionid'=>$iTransactionId,'sUrl'=>base64_encode($sReturnUrl)));
                Phpfox::getLib('url')->forward($sCheckoutUrl);

            } else {
                // not forwar paypal 
                Phpfox::getService('jobposting.applyjobpackage.process')->updatePayStatusOnePackage($aInvoice, 'completed');
                $ret = Phpfox::getService('jobposting.applyjobpackage.process')->updateRemainingApply($iDataId);
                if(isset($ret['data_id'])){
                    $bSuccess = true;
                    // foward apply job page
                    Phpfox::getComponent('jobposting.applyjob'
                        , array('bNoTemplate' => true, 'fromComponent' => 'applyfee', 'jobID' => $jobID)
                        , 'controller'
                    );
                    return true;
                } else {
                    $bSuccess = false;
                    $this->url()->send($sUrl, null, _p('invalid_package'));
                    return false;
                }            
            }

        }
    }

}

?>