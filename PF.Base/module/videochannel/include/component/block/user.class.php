<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Block_User extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		list($iCnt, $aVideos) = Phpfox::getService('videochannel')->getUserVideos($this->request()->getInt('user_id'));
		
		$this->template()->assign(array(
				'iUserTotalVideos' => $iCnt,
				'aMyVideos' => $aVideos
			)
		);	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('videochannel.component_block_user_clean')) ? eval($sPlugin) : false);
	}
}

?>