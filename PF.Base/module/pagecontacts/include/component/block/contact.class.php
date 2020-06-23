<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		YouNet Company
 * @author  		MinhNTK
 * @package  		Module_Pagecontacts
 * @version 		3.01
 */
class PageContacts_Component_Block_Contact extends Phpfox_Component
{
	/**
	 * Class process method which is used to execute this component.
	 */
	public function process()
	{
		$iPageId = (int)$this->request()->get('iPageId',0);
		$aTopics = phpfox::getService('pagecontacts')->getTopicsOfPage($iPageId);
		$aContact = phpfox::getService('pagecontacts')->getContactOfPage($iPageId);
		if(phpfox::isUser())
		{
			$sFullName = phpfox::getUserBy('full_name');
			$sEmail = phpfox::getUserBy('email');
		}
		$this->template()->assign(array(
								'aTopics'=>$aTopics,
								'sFullName'=>isset($sFullName)?$sFullName:'',
								'sEmail'=>isset($sEmail)?$sEmail:'',
								'aContact'=>$aContact,
								));
		return 'block';
    }
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('pagecontacts.component_block_detail_clean')) ? eval($sPlugin) : false);
	}
}

?>