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
class FoxFavorite_Component_Block_Top_Favorite_Member extends Phpfox_Component
{
	public function process()
	{
		$aUsers = Phpfox::getService('foxfavorite')->getTopFavoriteMembers();
		
		if (!count($aUsers))
		{
			return false;
		}
		
		$this->template()->assign(array(
				'sHeader' => _p('foxfavorite.top_favorite_members'),
				'aLoggedInUsers' => $aUsers,
				//'sDeleteBlock' => 'dashboard',
			/*
				'aEditBar' => array(
					'ajax_call' => 'log.getUserLoginEditBar',
					'params' => '&amp;type_id=dashboard'
				)							 
			 */
			)
		);
		
		return 'block';
	}
}
?>