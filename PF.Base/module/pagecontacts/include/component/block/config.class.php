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
class PageContacts_Component_Block_Config extends Phpfox_Component
{
	/**
	 * Class process method which is used to execute this component.
	 */
	public function process()
	{
		$iPageId = (int)$this->request()->get('iPageId',0);
		if(isset($iPageId) && $iPageId)
		{
			
			$aContact = phpfox::getService('pagecontacts')->getContactOfPage($iPageId);
			$sPath = phpfox::getParam('core.path');
			if(empty($aContact))
			{
				$bIsActive = true;
			}
			else
			{
				$bIsActive = $aContact['is_active'];
			}
			
			$this->template()->assign(array(
									'iPageId'=>$iPageId,
									'bIsActive'=>$bIsActive,
									'aForms'=>$aContact,
									'sPath'=>$sPath
									));
				

			$this->template()->setEditor();
		}
		else
		{
			return false;
		}
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('pagecontacts.component_block_config_clean')) ? eval($sPlugin) : false);
	}
}

?>