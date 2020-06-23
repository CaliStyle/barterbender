<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * @copyright		YouNetCo Company
 * @author  		MinhNTK
 */
class FoxFavorite_Component_Block_Pic extends Phpfox_Component
{
	public function process()
	{				
		if (!defined('PHPFOX_IS_USER_PROFILE'))
		{
			return false;
		}

		$aUser = $this->getParam('aUser');
        $aProfileLinksFavorite = Phpfox::getService('profile')->getProfileMenu($aUser);

        foreach ($aProfileLinksFavorite as $iKey => $aProfileLink) {
            if ($aProfileLink['actual_url'] == 'profile_foxfavorite') {
                foreach ($aProfileLinksFavorite[$iKey]['sub_menu'] as $ikey => $value ) {
                    if ($aProfileLinksFavorite[$iKey]['sub_menu'][$ikey]['total'] == 0) {
                        unset($aProfileLinksFavorite[$iKey]['sub_menu'][$ikey]);
                    }
                }
            }
        }
		$sView = $this->request()->get('view');
		$aUserInfo = array(
			'title' => $aUser['full_name'],
			'path' => 'core.url_user',
			'file' => $aUser['user_image'],
			'suffix' => '_200',
			'max_width' => 175,
			'max_height' => 300,
			'no_default' => (Phpfox::getUserId() == $aUser['user_id'] ? false : true),
			'thickbox' => true,
        	'class' => 'profile_user_image',
			'no_link' => true
		);		

		(($sPlugin = Phpfox_Plugin::get('foxfavorite.component_block_pic_process')) ? eval($sPlugin) : false);
		
		$sImage = Phpfox::getLib('image.helper')->display(array_merge(array('user' => Phpfox::getService('user')->getUserFields(true, $aUser)), $aUserInfo));	

		$this->template()->assign(array(
				'sProfileImage' => $sImage,
				'sView'=>$sView,
                'aProfileLinksFavorite' => $aProfileLinksFavorite
			)
		);
		
		if (defined("PHPFOX_IN_DESIGN_MODE"))
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
		(($sPlugin = Phpfox_Plugin::get('foxfavorite.component_block_pic_clean')) ? eval($sPlugin) : false);
	}
}
?>