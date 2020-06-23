<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_User_Browse extends Phpfox_Service 
{

	public function query() {
		$sView = $this->request()->get('view');

		if($sView == 'donor')
		{
			$this->database()->select(Phpfox::getUserField() . ', donor.full_name as guest_full_name, ' );	
		}
		else if($sView == 'supporter')
		{
			$this->database()->select(Phpfox::getUserField() . ', ');	
		}
		else
		{
			//corresponding to default case in user controller
			$this->database()->select(Phpfox::getUserField() . ', donor.full_name as guest_full_name, ' );	
		}
		

	}

	public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false) {
		$sView = $this->request()->get('view');
		if($sView == 'donor')
		{
			$this->database()->innerJoin(Phpfox::getT('user'), 'u', 'u.user_id = donor.user_id');
		}
		else if($sView == 'supporter')
		{
			$this->database()->innerJoin(Phpfox::getT('user'), 'u', 'u.user_id = supporter.user_id');
		}
	}

}

?>