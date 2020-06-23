<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		YouNet Company
 * @author  		MinhNTK
 * @package  		Module_PageContacts
 * @version 		3.01
 */
class PageContacts_Service_PageContacts extends Phpfox_Service {

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->_sTable = Phpfox::getT('pagecontacts');
	}
	
	/**
	 * Get Contacts from one page 
	 */
	public function getContactOfPage($iPageId)
	{
			$aContact = phpfox::getLib('database')->select('pc.is_active, pc.description as contact_description, pc.page_id')
						->from($this->_sTable, 'pc')
						->where('pc.page_id = '.$iPageId)
						->execute('getRow');
			if(empty($aContact))
			{
				return false;
			}
			$aTopics = phpfox::getLib('database')->select('*')
					 ->from(phpfox::getT('pagecontacts_topic'))
					 ->where('page_id = '.$aContact['page_id'])
					 ->execute('getRows');
			foreach($aTopics as $iKey => $aTopic)
			{
				$aContact['topics'][$iKey]['topic_id'] = $iKey;
				$aContact['topics'][$iKey]['topic'] = $aTopic['topic'];
				$aContact['topics'][$iKey]['email'] = $aTopic['email'];
			}
			$aContact['totalTopic'] = count($aTopics);
			return $aContact;
			
	}
	
	public function getTopicsOfPage($iPageId)
	{
		$aTopics = phpfox::getLib('database')->select('pt.topic, pt.topic_id')
					->from(phpfox::getT('pagecontacts_topic'), 'pt')
					->where('pt.page_id = '.$iPageId)
					->execute('getRows');
		if(empty($aTopics))
		{
			return false;
		}
		return $aTopics;
	}
	
	public function isShowContactButton($iPageId, &$bIsSetting)
	{
		$aContact = $this->getContactOfPage($iPageId);
	
		if(empty($aContact))
		{
			$bIsPageOwner = phpfox::getService('pagecontacts')->isOwnerPage($iPageId);
			if($bIsPageOwner)
			{
				
				$bIsSetting = true;
				return true;
			}
			return false;
		}
		else
		{
			if($aContact['is_active'] == 0)
			{
				return false;
			}
		}
		return true;
	}
	
	public function isOwnerPage($iPageId)
	{
		$aPage = phpfox::getLib('database')->select('*')
				->from(phpfox::getT('pages'))
				->where('user_id = '.phpfox::getUserId().' and page_id = '.$iPageId)
				->execute('getRow');
		if(empty($aPage))
		{
			return false;
		}
		return true;
	}
}

?>