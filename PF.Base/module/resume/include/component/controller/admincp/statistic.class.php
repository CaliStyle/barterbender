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
class Resume_Component_Controller_Admincp_Statistic extends Phpfox_Component
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
		$iPage 		= $this->request()->getInt('page');
		$iPageSize 	= 20;
		$aVals 		= $this->request()->get('search');
		$aConds 	= array();
		
		$oSearch 	= Phpfox::getLib('search')->set(array(
						'type' => 'request',
						'search' => 'search',
					  ));

		$sType      = $oSearch->get('type');
		$oSearch->setCondition(' 1=1 ');
		
		$resume =Phpfox::getService('resume.basic')->getItemCount($oSearch->getConditions());
		
		$tempvalue = $oSearch->getConditions();

		$tempvalue[] = " and rbi.is_published=1 and rbi.status = 'approved' and rbi.is_completed = 1";
		
		$published =Phpfox::getService('resume.basic')->getItemCount($tempvalue);
		
		$tempvalue = $oSearch->getConditions();

		$tempvalue[] = " and (rbi.is_employer = 1)";
		
		$view =Phpfox::getService('resume.account')->getItemCount($tempvalue);
		
		
		$tempvalue = $oSearch->getConditions();

		$tempvalue[] = " and (rbi.is_employee = 1 and rbi.is_employer = 0)";
		
		$whoview =Phpfox::getService('resume.account')->getItemCount($tempvalue);
		
		// Set page header
		$this -> template() -> setHeader(array(
			'manage_request.js' => 'module_resume', 
			'resume_backend.css' => 'module_resume',
		));
		
		// Set breadcrumb
		$this->template()
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_resume'), $this->url()->makeUrl('admincp.app').'?id=__module_resume')
            ->setBreadCrumb(_p('resume.statistics'), '');
		
		// Assign variable for layout
		
		$aForms = array(
			'resume' => $resume,
			'published' => $published,
			'view' => $view,
			'whoview' => $whoview,
		);
		
		
		$this -> template() -> assign(array(
			'aForms'		=> $aForms,
		
		));
	 }
}
	