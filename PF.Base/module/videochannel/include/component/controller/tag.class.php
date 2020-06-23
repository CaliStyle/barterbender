<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Controller_Tag extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		return Phpfox::getLib('module')->setController('videochannel.index');	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('videochannel.component_controller_tag_clean')) ? eval($sPlugin) : false);
	}
}

?>