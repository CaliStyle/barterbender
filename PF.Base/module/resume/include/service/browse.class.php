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
class Resume_Service_Browse extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('resume_basicinfo');	
	}
	
	
	public function query()
	{
		if(Phpfox::getLib("module")->getControllerName()=="whoviewedme")
		{
			Phpfox::getService("resume.viewme.browse")->query();
			return;
		}
	}
	
	public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
	{
		if(Phpfox::getLib("module")->getControllerName()=="whoviewedme")
		{
			Phpfox::getService("resume.viewme.browse")->getQueryJoins($bIsCount,$bNoQueryFriend);
			return;
		}
		$bIsAdvSearch = false;
		$bIsSelfView = $this->request()->get('bIsSelfView');
		$iViewerId = Phpfox::getUserId();
		$resume_id = Phpfox::getService("resume.basic")->getResumeIdIsPublished(Phpfox::getUserId());
		$bViewResumeRegistration = Phpfox::getService('resume.account')->checkViewResumeRegistration($iViewerId);
		
		if($this->search()->get('form_flag'))
		{
			$bIsAdvSearch = true;
		};
		// we use this when paging page 2 for advanced search form
		$bA = Phpfox_Request::instance()->get("bIsAdvSearch");
		if($bIsAdvSearch)
		{
			$aVals = Phpfox::getService('resume')->getAdvSearchFields();
			$this->_getAdvQueryJoins($aVals, $bIsCount);
		}
		else
		if ($bA=="1")
		{

			if ($this->search()->getPage()>1)
			{
				$aVals =$_SESSION["searchAdv"];
				$this->_getAdvQueryJoins($aVals, $bIsCount);
			}

		}else
		{
			// My Noted Resumes Filter
			if($this->request()->get('view') && $this->request()->get('view') == 'noted')
			{
				$this->database()->join(Phpfox::getT('resume_viewme'), 'rv',"rv.user_id = {$iViewerId} AND rv.resume_id = rbi.resume_id");
			}
			
			// My Favorite Resumes Filter
			if ($this->request()->get('view') && $this->request()->get('view') == 'favorite')
			{
				$this->database()->join(Phpfox::getT('resume_favorite'), 'rf',"rf.user_id = {$iViewerId} AND rf.resume_id = rbi.resume_id");
			}
			
			// Category Filter
			if ($this->request()->get((defined('PHPFOX_IS_USER_PROFILE') ? 'req3' : 'req2')) == 'category')
			{
				$this->database()
					->innerJoin(Phpfox::getT('resume_category_data'), 'rcd', 'rcd.resume_id = rbi.resume_id')
					->innerJoin(Phpfox::getT('resume_category'), 'rc', 'rc.category_id = rcd.category_id');			
			} 
		}
		$this->database()->innerJoin(Phpfox::getT('user'), 'userDelete', 'userDelete.user_id = rbi.user_id');
	}
	
	private function _getAdvQueryJoins($aVals, $bIsCount = 0)
	{
		//Filter company
		if(!empty($aVals['company']))
		{
			$this->database() -> innerJoin(Phpfox::getT('resume_experience'),'rex','rex.resume_id = rbi.resume_id');
		}
		//Filter school and degree
		if(!empty($aVals['school'])|| !empty($aVals['degree']))
		{
			$this->database() -> innerJoin(Phpfox::getT('resume_education'),'red','red.resume_id = rbi.resume_id');
		}
		//Filter category
		if(!empty($aVals['category']))
		{
			$cats = $aVals['category'];
			
			if ($this->request()->get((defined('PHPFOX_IS_USER_PROFILE') ? 'req3' : 'req2')) == 'category')
			{
				$iCat = $this->request()->get((!empty($bIsProfile) && $bIsProfile === true ? 'req4' : 'req3'));
				if(!in_array($iCat, $cats))
				{
					$cats[] = $iCat;
				}
			}
			
			
			$cats[]= -1;
			
			$aIdString = implode(',', $cats);
			
			$this -> database() -> innerJoin(Phpfox::getT('resume_category_data'),'rcd',' rcd.category_id IN (' . $aIdString . ') AND rcd.resume_id = rbi.resume_id');

            $this -> database() -> group('rbi.resume_id');
		}
		else
		{
			if ($this->request()->get((defined('PHPFOX_IS_USER_PROFILE') ? 'req3' : 'req2')) == 'category')
			{
				$iCat =  (int) $this->request()->get((!empty($bIsProfile) && $bIsProfile === true ? 'req4' : 'req3'));
			
				$this -> database() -> innerJoin(Phpfox::getT('resume_category_data'),'rcd',' rcd.category_id = ' . $iCat . ' AND rcd.resume_id = rbi.resume_id');
                $this -> database() -> group('rbi.resume_id');
			}
		}
	} 

	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('resume.service_browse__call'))
		{
			eval($sPlugin);
			return;
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}

?>