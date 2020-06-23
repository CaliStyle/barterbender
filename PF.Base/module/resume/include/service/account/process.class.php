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
class Resume_Service_Account_Process extends Phpfox_Service
{
	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('resume_account');
	}
	
	/**
	 * Add a new account
	 */
	public function add($aVals)
	{
		
		
		$aSql = array(
			'user_id' => Phpfox::getUserId(),
			'view_resume' => $aVals['view_resume'],
		);
		
		if($aVals['view_resume']==1 || $aVals['view_resume']==2)
			$aSql['start_employer_time'] = PHPFOX_TIME;  
		if($aVals['view_resume']==0 || $aVals['view_resume']==2)
			$aSql['start_time'] = PHPFOX_TIME;       
		
		if(Phpfox::isAdmin())
		{
			if($aVals['view_resume']==2)
			{
				$aSql['is_employer'] = $aSql['is_employee'] = 1;
			}
			else if($aVals['view_resume']==1)
			{
				$aSql['is_employer'] = 1;
			}
			else if($aVals['view_resume']==0)
			{
				$aSql['is_employee'] = 1;
			}
		}  
		                                                                                                                                                                                                                            
		$iId = $this->database()->insert($this->_sTable,$aSql);	
		 
		return $iId;
	}
	
	/**
	 * Update a Account.
	 */
	public function update($aVals)
	{
		
	    $aSql = array(	
			'view_resume' => $aVals['view_resume'],
		); 
		
		$aAccount = Phpfox::getService('resume.account')->getAccount();
		if($aAccount['start_time']==0 || $aAccount['start_time']==null)
		{
			if($aVals['view_resume']==2 || $aVals['view_resume']==0)
			{
				$aSql['start_time'] = PHPFOX_TIME;
			}
		}
		if($aAccount['start_employer_time']==0 || $aAccount['start_employer_time']==null)
		{
			if($aVals['view_resume']==2 || $aVals['view_resume']==1)
			{
				$aSql['start_employer_time'] = PHPFOX_TIME;
			}
		} 
		    
		if(Phpfox::isAdmin())
		{
			if($aVals['view_resume']==2)
			{
				$aSql['is_employer'] = $aSql['is_employee'] = 1;
			}
			else if($aVals['view_resume']==1)
			{
				$aSql['is_employer'] = 1;
			}
			else if($aVals['view_resume']==0)
			{
				$aSql['is_employee'] = 1;
			}
		}                                                                                                                                                                                                                              
		$iId = $this->database()->update($this->_sTable,$aSql,'account_id='.$aVals['account_id']);		
		return $iId;
	}

	/**
	 * Delete a account
	 */
	 
	 public function deleteAccount($account_id)
	 {
	 	$aAccount = Phpfox::getService('resume.account')->getAccountById($account_id);
	
		if(!isset($aAccount['user_id']))
			return false;
		if(!$this->is_checkAdmin($aAccount['user_id']))
		{
			Phpfox::getService('resume.account')->updateUserGroup($aAccount['user_id'],2);
		}
	 	$this->database()->delete($this->_sTable,'account_id='.$account_id);	
	 }
	 
	 /**
	  * 
	  */
	 public function updateApprove($account_id,$type,$value)
	 {
	 	if($type=="is_employee")
		{
			$this->database()->update(Phpfox::getT('resume_account'),array('is_employee'=>$value),'account_id='.$account_id);
		}
		else {
			$this->database()->update(Phpfox::getT('resume_account'),array('is_employer'=>$value),'account_id='.$account_id);
		}
	 }
	 
	 /**
	  * Check user is Admin
	  */
	  public function is_checkAdmin($user_id)
	  {
	  		$aUser = Phpfox::getService('user')->getUser($user_id);
			if($aUser['user_group_id']==1)
				return true;
			return false;
	  }
	  
}

?>