<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		YouNetCo Company
 * @author  		MinhNTK
 * @package 		FoxFavorite_Module
 */
class FoxFavorite_Component_Block_Add extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		Phpfox::isUser(true);
		if (Phpfox::getService('foxfavorite.process')->add($this->request()->get('type'), $this->request()->get('id')))
		{
			Phpfox::getLib('ajax')->call('<script type="text/javascript">$(\'#js_footer_bar_favorite_content\').html(\'<!-- EMPTY_FOOTER_BAR -->\');</script>');
		}		
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('foxfavorite.component_block_add_clean')) ? eval($sPlugin) : false);
	}
}

?>