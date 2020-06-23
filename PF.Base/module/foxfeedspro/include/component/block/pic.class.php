<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
class FoxFeedsPro_Component_Block_Pic extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{				
		if (!defined('PHPFOX_IS_USER_PROFILE') && !defined('PAGE_TIME_LINE'))
		{
			return false;
		}
		
		(($sPlugin = Phpfox_Plugin::get('profile.component_block_pic_start')) ? eval($sPlugin) : false);
		
		if (isset($bHideThisBlock))
		{
			return false;
		}		
		
		$aUser = $this->getParam('aUser');
		if ($aUser === null)
		{
			$aUser = $this->getParam('aPage');
			$aUser['user_image'] = $aUser['image_path'];
			foreach ($aUser as $sKey => $sValue)
			{
				if (strpos($sKey, 'owner_') !== false && $sKey != 'owner_user_image')
				{
					$aUser[str_replace('owner_', '', $sKey)] = $sValue;
				}
			}
		}

		$aUserInfo = array(
			'title' => $aUser['full_name'],
			'path' => 'core.url_user',
			'file' => $aUser['user_image'],
			'suffix' => '_120_square',
			'max_width' => 175,
			'max_height' => 300,
			'no_default' => (Phpfox::getUserId() == $aUser['user_id'] ? false : true),
			'thickbox' => true,
			'class' => 'profile_user_image',
			'no_link' => true
		);	
		
		if (Phpfox::getService('profile')->timeline())
		{
			$aUserInfo['suffix'] = '_120_square';
			unset($aUserInfo['max_width']);
			unset($aUserInfo['max_height']);
		}

		(($sPlugin = Phpfox_Plugin::get('profile.component_block_pic_process')) ? eval($sPlugin) : false);
		
		$sImage = Phpfox::getLib('image.helper')->display(array_merge(array('user' => Phpfox::getService('user')->getUserFields(true, $aUser)), $aUserInfo));	

		$this->template()->assign(array(
				'sProfileImage' => $sImage, 
			)
		);
		
		if (defined('PHPFOX_IN_DESIGN_MODE') && !Phpfox::getService('profile')->timeline())
		{
			return 'block';
		}
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('profile.component_block_pic_clean')) ? eval($sPlugin) : false);
	}
}

?>