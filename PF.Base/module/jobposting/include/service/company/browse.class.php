<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, AnNT
 * @package        Module_Jobposting
 * @version        3.01
 * 
 */
class JobPosting_Service_Company_Browse extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{
	   
	}
	
	public function query()
	{
	   
	}
	
	public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
	{
		$iViewerId = Phpfox::getUserId();
		if ($this->request()->get('view') && $this->request()->get('view') == 'followingcompany')
		{
			$this->database()->join(Phpfox::getT('jobposting_follow'), 'fl',"fl.user_id = {$iViewerId} AND fl.item_id = ca.company_id and fl.item_type = 'company'");
		}
		
		if ($this->request()->get('view') && $this->request()->get('view') == 'favoritecompany')
		{
			$this->database()->join(Phpfox::getT('jobposting_favorite'), 'fr',"fr.user_id = {$iViewerId} AND fr.item_id = ca.company_id and fr.item_type = 'company'");
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
		if ($sPlugin = Phpfox_Plugin::get('jobposting.company.service_browse__call'))
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