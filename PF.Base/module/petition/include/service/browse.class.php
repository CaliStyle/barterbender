<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Service_Browse extends Phpfox_Service 
{	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('petition');	
	}
	
	public function query()
	{            
		$this->database()->select('petition_text.short_description_parsed AS description, petition_category1.category_id AS category_id, petition_category1.name AS category_name, ');

            $this->database()->join(Phpfox::getT('petition_text'), 'petition_text', 'petition_text.petition_id = petition.petition_id')                
                    ->leftjoin(Phpfox::getT('petition_category_data'), 'petition_category_data1','petition_category_data1.petition_id = petition.petition_id')
                    ->leftjoin(Phpfox::getT('petition_category'), 'petition_category1', 'petition_category1.category_id = petition_category_data1.category_id');
            
		if (Phpfox::isUser() && Phpfox::isModule('like'))
		{
			$this->database()->select('lik.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'lik', 'lik.type_id = \'petition\' AND lik.item_id = petition.petition_id AND lik.user_id = ' . Phpfox::getUserId());			
		}
	}
	
	public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
	{		
		if (Phpfox::isModule('friend') && Phpfox::getService('friend')->queryJoin($bNoQueryFriend))
		{
			$this->database()->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = petition.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());	
		}
		
		if (Phpfox::getParam('core.section_privacy_item_browsing'))
		{
			if ($this->search()->isSearch())
			{
				$this->database()->join(Phpfox::getT('petition_text'), 'petition_text', 'petition_text.petition_id = petition.petition_id');
			}			
		}
		else
		{
			if ($bIsCount && $this->search()->isSearch())
			{
				$this->database()->join(Phpfox::getT('petition_text'), 'petition_text', 'petition_text.petition_id = petition.petition_id');
			}
		}
		
		if ($this->request()->get('req2') == 'tag')
		{
			$this->database()->innerJoin(Phpfox::getT('tag'), 'tag', 'tag.item_id = petition.petition_id AND tag.category_id = \'petition\'');	
		}
		
		if ($this->request()->get((defined('PHPFOX_IS_USER_PROFILE') ? 'req3' : 'req2')) == 'category')
		{		
			$this->database()
				->innerJoin(Phpfox::getT('petition_category_data'), 'petition_category_data', 'petition_category_data.petition_id = petition.petition_id')
				->innerJoin(Phpfox::getT('petition_category'), 'petition_category', 'petition_category.category_id = petition_category_data.category_id');			
		}

		$this->database()->join(Phpfox::getT('user'), 'ynp_user', 'ynp_user.user_id = petition.user_id');	        
		
            
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
		if ($sPlugin = Phpfox_Plugin::get('petition.service_browse__call'))
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