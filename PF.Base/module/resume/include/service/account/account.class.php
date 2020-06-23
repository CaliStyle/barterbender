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
class Resume_Service_Account_Account extends Phpfox_Service
{
	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('resume_account');
	}
	
	public function getAccount()
	{
		// Generate query object	
		$user_id = Phpfox::getUserId();
		
		$oQuery = $this -> database()
					 	-> select('*')
				   		-> from($this->_sTable)
						-> where('user_id = '.$user_id);
		$Info = $oQuery-> execute('getRow');
		
		return $Info;	
	}
	
	public function getAccountByUserId($user_id)
	{
		// Generate query object	
		$oQuery = $this -> database()
					 	-> select('*')
				   		-> from($this->_sTable)
						-> where('user_id = '.$user_id);
		$Info = $oQuery-> execute('getRow');
		
		return $Info;	
	}
	
	public function getAccountById($account_id)
	{
		// Generate query object	
		
		$oQuery = $this -> database()
					 	-> select('*')
				   		-> from($this->_sTable)
						-> where('account_id = '.$account_id);
		$Info = $oQuery-> execute('getRow');
		
		return $Info;	
	}
	
	public function getItemCount($aConds = array())
	{
		$oQuery = $this -> database()
				-> select('count(*) as count')
				-> from($this->_sTable,'rbi');
		
		if($aConds)
		{
			$oQuery->where($aConds);
		}
		
		$iCnt = (int)$oQuery-> execute('getSlaveField');
		return $iCnt;
	}
	
	public function getResumes($aConds, $sOrder, $iPage = 0, $iLimit = 0, $iCount = 0)
	{
		// Generate query object	
						
		$oSelect = $this -> database() 
						 -> select('*')
						 -> from($this->_sTable, 'rbi')
						 -> join(Phpfox::getT('user'),'u','u.user_id=rbi.user_id');
		
		// Filter select condition
		if($aConds)
		{
			$oSelect->where($aConds);
		}
		
		// Setup select ordering		
		if($sOrder)
		{
			$oSelect->order($sOrder);
		}
		
		// Setup limit items getting
		$oSelect->limit($iPage, $iLimit, $iCount);
		$aResumes = $oSelect->execute('getRows');
	 	return $aResumes;
	}
	
	
	public function getUserGroupId($stype)
	{
		return $this->database()->select('user_group_id')
		->from(Phpfox::getT('user_group'))
		->where('resume_flag="'.$stype.'"')
		->execute('getSlaveField');
	}

	/**
	 * Get Info User Group
	 * 
	 */
	 public function getInfoGroup($user_group_id)
	 {
	 	return $this->database()->select('*')
		->from(Phpfox::getT('user_group'))
		->where('user_group_id='.$user_group_id)
		->execute('getRow');
	 }

	public function updateUserGroup($user_id,$group_id = 0)
	{
		if($group_id > 0)
		{
			$this->database()->update(Phpfox::getT('user'),array('user_group_id'=>$group_id),'user_id='.$user_id);
		}
	}
	
	public function getListAccountViewMe($resume_id,$owner_id,$iPage = 0, $iLimit = 0, $iCount=0)
	{
		$where = 're.resume_id='.$resume_id;
		$aAccount = $this->getAccountByUserId($owner_id);
		if($aAccount && $aAccount['is_employer']==1)
		{
			$where.=" and ac.is_employer=1";
		}
	
		$iCnt = $this->database()->select('count(*)')
			->from(Phpfox::getT('resume_viewme'),'re')
			->leftjoin(Phpfox::getT('resume_account'),'ac','re.user_id=ac.user_id')
			->where($where)
			->execute('getSlaveField');
		
		
		$oSelect = $this->database()->select('*')
			->from(Phpfox::getT('resume_viewme'),'re')
			->leftjoin(Phpfox::getT('resume_account'),'ac','re.user_id=ac.user_id');
		$oSelect->where($where);
		$oSelect->limit($iPage, $iLimit, $iCount);
		
		$aResumes = $oSelect->execute('getRows');
		
	 	return array($aResumes,$iCnt);
	}
	
	public function checkViewResume($iUserId)
	{
		// Get selected Account	
		$aRegistration = $this -> database()
							   -> select('*')
							   -> from(Phpfox::getT('resume_viewme'))
							   -> where('user_id = ' . $iUserId)
							   ->execute('getRow');
		
		//Return the result
		if($aRegistration)
		{
			return $aRegistration;
		}
		return false;
	}
	
		/**
	 * Check if the viewer had registried "View Resume" service or not
	 * @param <int> $iUserId is the id of the viewer
	 * @return boolean
	 */
	 
	private function checkpermission($iUserId){
		$permission = PHpfox::getService('resume.setting')->getPermissionByName("public_resume");
		if (Phpfox::getUserParam('resume.view_all_resume'))
		{
			if($permission == 3 && Phpfox::getUserParam('resume.view_all_resume'))
			{
				return TRUE;
			}
		}




		if($permission==2 && $iUserId>0)
			return true;
		if($permission==1)
			return true;
		return false;
	}
	
	public function checkViewResumeRegistration($iUserId)
	{
	
		if($this->checkpermission($iUserId))
		{
			return true;
		}
		$aRegistration = $this->database() ->select('*')
							->from($this->_sTable)
							->where("user_id = {$iUserId} AND is_employer = 1")
							->execute('getRow');
		if($aRegistration)
		{
			return TRUE;
		}
		return FALSE;
	}
	
	public function checkWhoViewRegistration($iUserId)
	{
		if($this->checkpermission($iUserId))
		{
			return true;
		}
		
		$aRegistration = $this->database()->select('*')
								->from($this->_sTable)
								->where("user_id = {$iUserId} AND is_employee = 1")
								->execute('getRow');
		if($aRegistration)
		{
			return TRUE;
		}	
		
		return FALSE;
	}
}

?>