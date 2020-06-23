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
class FoxFavorite_Component_Block_Favorite_Profile extends Phpfox_Component
{
	public function process()
	{
		$sModule = 'profile';
		$iItemId = (defined('PHPFOX_IS_USER_PROFILE'))?phpfox::getLib('request')->get('req1'):phpfox::getLib('request')->getInt('req2');
		$iUserId = phpfox::getService('foxfavorite')->getUserIdFromUserName($iItemId);
		if(!Phpfox::getService('foxfavorite')->isAvailModule($sModule) || $iUserId == phpfox::getUserId() || empty($iUserId) || (phpfox::getUserBy('view_id')!=0))
		{
			return false;
		}
		
		$bIsAlreadyFavorite = phpfox::getService('foxfavorite')->isAlreadyFavorite($sModule, $iItemId);
		$favor_img = phpfox::getParam('core.path').'module/foxfavorite/static/image/favorite.png';
		$unfavor_img = phpfox::getParam('core.path').'module/foxfavorite/static/image/unfavorite.png';
		$this->template()->assign(array(
			'favor_img'=>$favor_img,
			'unfavor_img'=>$unfavor_img,
			'bIsAlreadyFavorite'=>$bIsAlreadyFavorite,
			'sModule'=>$sModule,
			'iItemId'=>$iItemId
		));
		
		return 'block';
	}
}
?>