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
class FoxFavorite_Component_Block_Favorite_Member extends Phpfox_Component
{
	public function process()
	{
		$sModule = (defined('PHPFOX_IS_USER_PROFILE')) ? 'profile' : Phpfox::getLib('module')->getModuleName();
        
        if(!Phpfox::getService('foxfavorite')->isAvailModule($sModule))
        {
        	
            return false;
        }
        
        if($sModule == 'advancedmarketplace' || $sModule == 'karaoke' || $sModule == 'coupon' || $sModule == 'directory' || $sModule == 'auction')
        {
            $iItemId = Phpfox::getLib('request')->getInt('req3');
        }
        else
		{
            $iItemId = (defined('PHPFOX_IS_USER_PROFILE')) ? Phpfox::getLib('request')->get('req1') : Phpfox::getLib('request')->getInt('req2');
		}
		
        if($sModule == 'pages')
		{
			$aPage = phpfox::getService('pages')->getForView($iItemId);
			$iItemId = $aPage['page_id'];
		}
		
        if(isset($sModule) && isset($iItemId))
		{
			
			$aUsers = phpfox::getService('foxfavorite')->getFavoriteMembers($sModule, $iItemId);
			if(empty($aUsers))
			{
				return false;
			}
			$this->template()->assign(array(
				'aUsers'=>$aUsers,
				'sHeader'=>(defined('PHPFOX_IS_USER_PROFILE')) ?_p('foxfavorite.who_favorited_this_person'):_p('foxfavorite.who_favorited_this_item'),
			));
			return 'block';	
		}
		else
		{	
			return false;
		}
	}
}
?>