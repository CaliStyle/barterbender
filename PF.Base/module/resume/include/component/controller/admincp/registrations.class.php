<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 * 
 */
class Resume_Component_Controller_Admincp_Registrations extends Phpfox_Component
{
	/*
	 * Process method which is used to process this component
	 */
	 
	private function _convertToTimeStamp($sDate, $bToDate = false)
	{
		// Generate date information array 
		$aDate = explode('/', $sDate);
		if(count($aDate)!=3 || strlen(trim($sDate))>10)
		{
			return '';
		}	
		// Convert to timestamp
		if(!$bToDate)
		{
			$sTimeStamp = phpfox::getLib('date')->mktime(0,0,0,$aDate[0], $aDate[1], $aDate[2]);
		}
		else 
		{
			$sTimeStamp = phpfox::getLib('date')->mktime(23,59,59,$aDate[0], $aDate[1], $aDate[2]);	
		}
		
		// Return timestamp result
		if(isset($sTimeStamp) && $sTimeStamp != '')
		{
			return $sTimeStamp;
		}
		return '';
	}
	
	 public function process()
	 {
	 	// Get search condition
	 
	 	$oSetting = Phpfox::getService("resume.setting");
			
		// Get global setting
	
		$iPage 		= $this->request()->getInt('page');
		$iPageSize 	= 20;
		
		
		$aConds 	= array();
		
		$oSearch 	= Phpfox::getLib('search')->set(array(
						'type' => 'request',
						'search' => 'search',
					  ));
		$sFromDate 	= $oSearch->get('fromdate');
		$sToDate 	= $oSearch->get('todate');
		$sType      = $oSearch->get('type');

		$oSearch->setCondition(' 1=1 ');
		if($sFromDate)
		{
			$iFromDateTimeStamp = $this->_convertToTimeStamp($sFromDate);
			if(!$iFromDateTimeStamp)
			{
				$oSearch->setCondition(' and 1!=1 ');
			}	
			else {
				if($sType==2 || $sType==3)
				{
					$oSearch->setCondition(' and (rbi.start_time=0 or rbi.start_time >= '.$iFromDateTimeStamp.') ');
					$oSearch->setCondition(' and (rbi.start_employer_time=0 or rbi.start_employer_time >= '.$iFromDateTimeStamp.') ');
				}
				else if($sType==4)
				{
					$oSearch->setCondition(' and (rbi.start_time=0 or rbi.start_time >= '.$iFromDateTimeStamp.') ');
				}
				else if($sType==1)
				{
					$oSearch->setCondition(' and (rbi.start_employer_time=0 or rbi.start_employer_time >='.$iFromDateTimeStamp.') ');
				}	
			}
		}
		
		if($sToDate)
		{
			$iToDateTimeStamp = $this->_convertToTimeStamp($sToDate,true);
			if(!$iToDateTimeStamp)
			{
				$oSearch->setCondition(' and 1!=1 ');
			}
			else	
			{
				if($sType==2 || $sType==3)
				{
					$oSearch->setCondition(' and (rbi.start_time=0 or rbi.start_time <= '.$iToDateTimeStamp.') ');
					$oSearch->setCondition(' and (rbi.start_employer_time=0 or rbi.start_employer_time <= '.$iToDateTimeStamp.') ');
				}
				else if($sType==4)
				{
					$oSearch->setCondition(' and (rbi.start_time=0 or rbi.start_time <= '.$iToDateTimeStamp.') ');
				}
				else if($sType==1)
				{
					$oSearch->setCondition(' and (rbi.start_employer_time=0 or rbi.start_employer_time <= '.$iToDateTimeStamp.') ');
				}
			}
		}
		
		if($sType)
		{
            if ($sType == 4) {
                $oSearch->setCondition(' and rbi.is_employee = 1 AND start_time > 0');
            } elseif ($sType == 1) {
					//$oSearch->setCondition(' and rbi.view_resume = '. (int)$sType);
                $oSearch->setCondition(' and rbi.is_employer = 1 AND start_employer_time > 0');
            } elseif ($sType != 3) {
					$oSearch->setCondition(' and rbi.view_resume > 0');	
			}
		}
		//Delete selected resume 
		if($aTask = $this->request()->getArray('task'))
		{
			
			if($aTask[0] == 'do_delete_selected')
			{
				foreach($this->request()->getArray('resume_row') as $iId){
					Phpfox::getService('resume.account.process')->deleteAccount($iId);
				}
				Phpfox::getLib('url')->send('admincp.resume.registrations');
			}
		}
		
		//Set pager
		$iCount = Phpfox::getService('resume.account')->getItemCount($oSearch->getConditions());
		
		phpFox::getLib('pager')->set(array(
				'page'  => $iPage, 
				'size'  => $iPageSize, 
				'count' => $iCount
		));

		// Get resume list
	
		$aResumes = Phpfox::getService('resume.account')->getResumes($oSearch->getConditions(), 'rbi.account_id desc', $iPage, $iPageSize, $iCount);
		
		// Set page header
		$this -> template() -> setHeader(array(
			'manage_request.js' => 'module_resume', 
		));
		
		// Set breadcrumb
		$this->template()
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_resume'), $this->url()->makeUrl('admincp.app').'?id=__module_resume')
            ->setBreadCrumb(_p('resume.manage_view_service_registration'), $this->url()->makeUrl('admincp.resume.registrations'));

		// Assign variable for layout
		$this -> template() -> assign(array(
			'aResumes'   => $aResumes,
			
			'sFromDate'	=> $sFromDate,
			'sToDate'   => $sToDate,
			'sType'  => $sType,			
		));
	 	 
	 }
}
	