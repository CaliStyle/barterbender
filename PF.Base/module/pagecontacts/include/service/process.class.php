<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Donationpages
 * @version 		$Id: process.class.php 1 2012-02-15 10:33:17Z YOUNETCO $
 */
class PageContacts_Service_Process extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
		$this->_sTable = Phpfox::getT('pagecontacts');                
    }
    
	public function add($aVals)
	{	
		$oFilter = phpfox::getLib('parse.input');
		$aVals['contact_description'] = isset($aVals['contact_description'])?$oFilter->clean($aVals['contact_description']):'';
		$aSql = array(
					'page_id'=>$aVals['page_id'],
					'description'=>$aVals['contact_description'],
					'user_id'=>phpfox::getUserId(),
					'is_active'=>isset($aVals['is_active'])?$aVals['is_active']:0
				);
		
		$iId = phpfox::getLib('database')->insert(phpfox::getT('pagecontacts'), $aSql);
		if(!empty($aVals['q']))
		{
			$this->updateTopic($aVals['page_id'], $aVals['q']);
		}
		
	}
	
	public function update($aVals)
	{
		$oFilter = phpfox::getLib('parse.input');
		$aVals['contact_description'] = isset($aVals['contact_description'])?$oFilter->clean($aVals['contact_description']):'';
		$aSql = array(
					'description'=>$aVals['contact_description'],
					'is_active'=>$aVals['is_active']
				);
		phpfox::getLib('database')->update(phpfox::getT('pagecontacts'), $aSql, 'page_id = '.$aVals['page_id']);
		if(!empty($aVals['q']))
		{
			$this->updateTopic($aVals['page_id'], $aVals['q']);
		}
		return true;
		
		
	}
	public function updateTopic($iPageId, $aVals, $bIsAdd = true)
	{
		if($iPageId)
		{
			phpfox::getLib('database')->delete(phpfox::getT('pagecontacts_topic'), 'page_id = '.$iPageId);
			foreach($aVals as $iKey => $aItem)
			{
				if(!empty($aItem['question']) || !empty($aItem['email']))
				{
					$aInsert = array('page_id'=>$iPageId, 'topic'=>$aItem['question'], 'email'=>$aItem['email']);
					phpfox::getLib('database')->insert(phpfox::getT('pagecontacts_topic'), $aInsert);
				}
			}
		}
		
	}
	
	public function sendMail($aVals)
	{
		if(empty($aVals))
		{
			return false;
		}
		
		$sToEmail = phpfox::getLib('database')->select('pt.email, pt.topic, pt.page_id')
					->from(phpfox::getT('pagecontacts_topic'), 'pt')
					->where('pt.topic_id ='.$aVals['topic'])
					->execute('getRow');
		if(!empty($sToEmail))
		{
			$aNewPage = Phpfox::getService('pages')->getForView($sToEmail['page_id']);
	
			$sLink = Phpfox::getService('pages')->getUrl($aNewPage['page_id'], $aNewPage['title'], $aNewPage['vanity_url']);
		}

		$sPhrase = _p('pagecontacts.email_contact_message',
					array('topic'=>$sToEmail['topic'], 'message'=>$aVals['message'],'full_name'=>$aVals['full_name'],'email'=>$aVals['email'], 'link'=>$sLink, 'title'=>$aNewPage['title'])
					);
		
		Phpfox::getLib('mail')->to($sToEmail['email'])						
			->subject($aVals['subject'])
			->message($sPhrase)
			->send();
		return true;
		
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
		if ($sPlugin = Phpfox_Plugin::get('donationpages.service_donationpages__call'))
		{
			return eval($sPlugin);
		}

		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }	  
    
}

?>