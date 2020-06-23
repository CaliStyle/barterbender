<?php

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_Coupon
 * @version        3.01
 * 
 */
class Coupon_Component_Controller_Admincp_Transaction extends Phpfox_Component
{

        /**
         * Class process method wnich is used to execute this component.
         */
        public function process()
        {
        	// Service Coupon
			$oCouponTransaction = Phpfox::getService("coupon.transaction");
			
        	// Page Number & Limit Per Page
            $iPage = $this->request()->getInt('page');
			$iPageSize = 10;
			
			// Variables
			$aVals = array();
			$aConds = array();
			
        	// Search Filter
            $oSearch = Phpfox::getLib('search')->set(array(
					'type' 	 => 'request',
					'search' => 'search',
			));
            
			$aVals['title'] 		= $oSearch->get('title');	  
			$aVals['username'] 		= $oSearch->get('username');
			$aVals['fromdate'] 		= $oSearch->get('fromdate');
			$aVals['todate'] 		= $oSearch->get('todate');
			$aVals['payment_type']  = $oSearch->get('payment_type');
			$aVals['payment_status']= $oSearch->get('payment_status');
			$aVals['submit'] 		= $oSearch->get('submit');
			$aVals['reset'] 		= $oSearch->get('reset');
			    
			 if($aVals['reset'])
			{
				$this->url()->send('admincp.coupon.transaction');
			}
			if($aVals['title'])
			{
				$aConds[] = "AND c.title like '%{$aVals['title']}%'";
			}
			if($aVals['username'])
			{
				$aConds[] = "AND u.full_name like '%{$aVals['username']}%'";
			}
			if($aVals['fromdate'])
			{
				$iFromTime = strtotime($aVals['fromdate']);
				$aConds[] = "AND ct.time_stamp >= {$iFromTime}";
			}
			if($aVals['todate'])
			{
				$iToTime = strtotime($aVals['todate'])+23*60*60+59*60+59;
				$aConds[] = "AND ct.time_stamp <= {$iToTime}";
			}
			if($aVals['payment_type'])
			{
				$aConds[] = "AND ct.payment_type = {$aVals['payment_type']}";
			}
			if($aVals['payment_status'])
			{
				$aConds[] = "AND ct.status = {$aVals['payment_status']}";
			}
			// Set pager
			$iCount = $oCouponTransaction->getItemCountForManage($aConds);
			
			phpFox::getLib('pager')->set(array(
						'page'  => $iPage, 
						'size'  => $iPageSize, 
						'count' => $iCount
			));
			
			// Get Coupons list
			$aTransactions = $oCouponTransaction->getTransactionsForManage($aConds, 'ct.time_stamp DESC', $iPage, $iPageSize, $iCount);
            
            $this -> template() -> setTitle(_p('manage_transactions'));
                        
			$this -> template()
                ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
                ->setBreadCrumb(_p('module_coupon'), $this->url()->makeUrl('admincp.app').'?id=__module_coupon')
                -> setBreadcrumb(_p('manage_transactions'), $this->url()->makeUrl('admincp.coupon.transaction'));
			$this -> template() -> assign(array(
					'aTransactions' => $aTransactions,
					'aForms'		=> $aVals
				));
        }

        /**
         * Garbage collector. Is executed after this class has completed
         * its job and the template has also been displayed.
         */
        public function clean()
        {
                (($sPlugin = Phpfox_Plugin::get('coupon.component_controller_admincp_transaction_clean')) ? eval($sPlugin) : false);
        }

}

?>