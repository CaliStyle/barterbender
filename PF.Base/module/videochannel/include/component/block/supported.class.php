<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Block_Supported extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$this->template()->assign(array(
//				'aSites' => Phpfox::getService('videochannel.grab')->getSites(true),
				'aSites' => 'YouTube'
			)
		);	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('videochannel.component_block_supported_clean')) ? eval($sPlugin) : false);
	}
}

?>