<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL, TrucPTM
 * @package        Module_Resume
 * @version        3.01
 * 
 */
class Resume_Component_Block_Add_Favorite extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		// Check User login requirement	
		Phpfox::isUser(true);
		
		$iResumeid = $this->getParam('iId');
		
		
		$aResume = Phpfox::getService('resume.basic')->getQuick($iResumeid);
		if ($aResume)
		{
			// Add resume to favorite list
			Phpfox::getService('resume.process')->addFavorite($iResumeid);
			
			// Add notification
			Phpfox::getService('notification.process')->add('resume_favorite', $aResume['resume_id'], $aResume['user_id']);
			
			// Add link view favorite list
			$sLink = phpfox::getLib('url')->makeUrl('resume.view_favorite');
			$this->template()->assign(array('sLink'=>$sLink));
		}
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('karaoke.component_block_add_favorite_clean')) ? eval($sPlugin) : false);
	}
}

?>