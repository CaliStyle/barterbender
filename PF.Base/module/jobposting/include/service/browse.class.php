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
class JobPosting_Service_Browse extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
	}
	
	
	public function query()
	{
		if(trim(Phpfox::getLib("module")->getControllerName())=="company/index" || trim(Phpfox::getLib("module")->getControllerName())=="company\index")
		{
			return Phpfox::getService("jobposting.company.browse")->query();
		}
        $this->database()->join(Phpfox::getT('jobposting_company'), 'jc', 'jc.company_id = job.company_id');
        $this->database()->select('u.*,jc.name,jc.location,jc.image_path,jc.server_id as image_server_id,');
	}
	
	public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
	{
		if(trim(Phpfox::getLib("module")->getControllerName()) == "company/index" || trim(Phpfox::getLib("module")->getControllerName())=="company\index")
		{
			return Phpfox::getService("jobposting.company.browse")->getQueryJoins($bIsCount, $bNoQueryFriend);
		}
		
		$iViewerId = Phpfox::getUserId();
    	if (Phpfox::isModule('friend') && Phpfox::getService('friend')->queryJoin($bNoQueryFriend))
        {
        	$this->database()->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = job.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());	
        }
		
		$this->database()->join(Phpfox::getT('jobposting_company'),'jc','jc.company_id = job.company_id and jc.is_deleted = 0');
		
		if ($this->request()->get('view') && $this->request()->get('view') == 'following')
		{
			$this->database()->join(Phpfox::getT('jobposting_follow'), 'fl',"fl.user_id = {$iViewerId} AND fl.item_id = job.job_id and fl.item_type = 'job'");
		}
		
		if ($this->request()->get('view') && $this->request()->get('view') == 'favorite')
		{
			$this->database()->join(Phpfox::getT('jobposting_favorite'), 'fr',"fr.user_id = {$iViewerId} AND fr.item_id = job.job_id and fr.item_type = 'job'");
		}
		
		if ($this->request()->get('view') && $this->request()->get('view') == 'appliedjob')
		{
			$this->database()->join(Phpfox::getT('jobposting_application'), 'al',"al.user_id = {$iViewerId} AND al.job_id = job.job_id")->group('job.job_id');
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
		if ($sPlugin = Phpfox_Plugin::get('jobposting.service_browse__call'))
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