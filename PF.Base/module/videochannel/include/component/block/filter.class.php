<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Block_Filter extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if (defined('PHPFOX_IS_USER_PROFILE'))
		{
			return false;
		}
		
		$this->template()->assign(array(
				'sHeader' => _p('videochannel.browse_filter')
			)
		);
		
		return 'block';		
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('videochannel.component_block_filter_clean')) ? eval($sPlugin) : false);
	}
}

?>