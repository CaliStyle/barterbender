<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Controller_Profile extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{		
		$this->setParam('bIsProfile', true);
		
		$aUser = $this->getParam('aUser');		
		
		if (!Phpfox::getService('user.privacy')->hasAccess($aUser['user_id'], 'videochannel.display_on_profile'))
		{
			return Phpfox_Error::display(_p('videochannel.videos_for_this_profile_is_set_to_private'));
		}			
		
		$bCanUploadVideo = Phpfox::getUserParam('videochannel.can_upload_videos');		
		$bCanAddChannel = Phpfox::getUserParam('videochannel.can_add_channels');
		
        $this->template()->assign(array('bCanUploadVideo' => $bCanUploadVideo, 'bCanAddChannel' => $bCanAddChannel));
        
		Phpfox::getComponent('videochannel.index', array('bNoTemplate' => true), 'controller');
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('videochannel.component_controller_profile_clean')) ? eval($sPlugin) : false);
	}
}

?>