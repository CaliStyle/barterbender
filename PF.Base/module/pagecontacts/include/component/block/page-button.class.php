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
class PageContacts_Component_Block_Page_Button extends Phpfox_Component
{
	/**
	 * Class process method which is used to execute this component.
	 */
	public function process()
	{
		if(!Phpfox::getUserParam('pagecontacts.can_create_page_contact'))
		{
			return false;
		}
		$sLink = '';
		$aPage = $this->getParam('aPage');
		$bIsSetting = false;
		$bIsShowContactButton = phpfox::getService('pagecontacts')->isShowContactButton($aPage['page_id'], $bIsSetting);

		if($bIsShowContactButton == false)
		{
			return false;
		}
		if($bIsSetting)
		{
			$sLink = phpfox::getLib('url')->makeUrl('pages.add.id_'.$aPage['page_id'].'.tab_contact');
		}
		$this->template()->assign(array(
							'iPageId'=>$aPage['page_id'],
							'bIsSetting'=>$bIsSetting,
							'sLink'=>isset($sLink)?$sLink:''
							));
		
		return 'block';
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