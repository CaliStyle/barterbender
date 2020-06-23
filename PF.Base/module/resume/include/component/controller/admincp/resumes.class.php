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
class Resume_Component_Controller_Admincp_Resumes extends Phpfox_Component
{
	/*
	 * Process method which is used to process this component
	 */
	 public function process()
	 {
	 	// Get search conditions
	 	$iPage 		= $this->request()->getInt('page');
		$iPageSize 	= 20;
		$aConds 	= array();
		
		$oSearch 	= Phpfox::getLib('search')->set(array(
						'type' => 'request',
						'search' => 'search',
					  ));
					  
		$aVals['headline'] 		= $oSearch->get('headline');	  
		$aVals['full_name'] 	= $oSearch->get('full_name');
		$aVals['status'] 		= $oSearch->get('status');
		$aVals['submit'] 		= $oSearch->get('submit');
		$aVals['reset'] 		= $oSearch->get('reset');
		
		if(!empty($aVals['reset']))
		{
			$this->url()->send('admincp.resume.resumes');
		}
		
		if(!empty($aVals['headline']))
		{
			$aConds[] = 'AND rbi.headline like '. "'%".$aVals['headline']."%'";
		}
		
		if(!empty($aVals['full_name']))
		{
			$aConds[] = 'AND u.full_name like '. "'%".$aVals['full_name']."%'";
		}
		
		if(!empty($aVals['status']))
		{
			switch($aVals['status'])
			{
				case 'incomplete':;
					$aConds[] = 'AND rbi.is_completed = 0';
					break;
				case 'completed':
					$aConds[] = 'AND rbi.is_completed = 1';
					break;
				case 'approving':
					$aConds[] = "AND rbi.status = 'approving'";
					break;
				case 'approved':
					$aConds[] = "AND rbi.is_completed = 1 AND rbi.status ='approved' AND rbi.is_published = 1";
					break;
				case 'denied':
					$aConds[] = "AND rbi.status = 'denied'";
					break;
				case 'private':
					$aConds[] = "AND rbi.is_completed = 1 AND rbi.status ='approved' AND rbi.is_published = 0";
					break;	
			}
		}
		
	 	// Delete selected resumes
	 	if($aTask = $this->request()->getArray('task'))
		{
			if($aTask[0] == 'do_delete_selected')
			{
				foreach($this->request()->getArray('resume_row') as $iId){
					Phpfox::getService('resume.process')->delete($iId);
				}
				Phpfox::getLib('url')->send('admincp.resume.resumes');
			}	
		}
		
	 	// Set pager
	 	$iCount = Phpfox::getService('resume')->getItemCount($aConds);
		
		phpFox::getLib('pager')->set(array(
				'page'  => $iPage, 
				'size'  => $iPageSize, 
				'count' => $iCount
		));
		
	 	// Get resume list
	 	$aResumes = Phpfox::getService('resume')->getResumes($aConds, 'rbi.resume_id DESC', $iPage, $iPageSize, $iCount);
		
	 	// Set page header
		$this -> template() -> setHeader(array(
			'manage_resume.js' => 'module_resume'
		));
		
	 	// Set breadcrumb
	 	$this -> template()
	 		  -> setTitle(_p('resume.admin_menu_manage_resumes'))
              ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
              ->setBreadCrumb(_p('module_resume'), $this->url()->makeUrl('admincp.app').'?id=__module_resume')
	 		  -> setBreadCrumb(_p('resume.admin_menu_manage_resumes'), $this->url()->makeUrl('admincp.resume.resumes'));
		
	 	// Assign variable for layout
		$this -> template() -> assign(array(
			'aResumes'   => $aResumes,
			'aForms'	 => $aVals			
		));
	 	
	 	// Set page phrase for jscript call
		$this->template()->setPhrase(array(
			'resume.are_you_sure_you_want_to_delete_this_resume'			
		));
	 }
}
	