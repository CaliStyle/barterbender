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
class FoxFavorite_Component_Block_Recent_Favorite extends Phpfox_Component
{
	public function process()
	{
		$aRecentFavorites = array();
		$bFlag = true;
		$iOffset = 0;
		$iPage = 0;
		$iTotalFeed = 5;
		$iCountFeed = 5;
		if(!phpfox::isUser())
		{
			return false;
		}
		$sUserName = phpfox::getUserBy('user_name');
		$sViewAllLink = phpfox::getLib('url')->makeUrl($sUserName.'.foxfavorite');
		$iTotalFavorite = phpfox::getLib('database')->select('count(*)')
						->from(phpfox::getT('foxfavorite'))
						->where('user_id = '.phpfox::getUserId())
						->execute('getField');
		while($bFlag)
		{
			list($iOwnerUserId, $aFavorites) = Phpfox::getService('foxfavorite')->getRecentFavorites(phpfox::getUserId(), '', 'f.time_stamp DESC', $iOffset, $iCountFeed);
			if(count($aRecentFavorites) >= $iTotalFeed || $iOffset > $iTotalFavorite)
			{
				$bFlag = false;
				break;
			}
			$iPage++;

			if($iOffset != 0)
			{		
				$iOffset += $iCountFeed;
				
			}
			else
			{
				$iOffset += $iPage * $iTotalFeed + 1;
				
			}
			$aRecentFavorites = array_merge($aRecentFavorites, $aFavorites);
			$iCountFeed = $iTotalFeed - count($aRecentFavorites);
			if($iCountFeed <= 0)
			{
				break;
			}
			
		}

		if(empty($aRecentFavorites))
		{
			return false;
		}
        
		$this->template()->assign(array(
								'aFavorites'=>$aRecentFavorites,
								'sHeader'=>_p('foxfavorite.recent_favorites'),
								'sViewAllLink'=>$sViewAllLink,
                                'aFooter' => array(
                                    _p('view_all') => $sViewAllLink
                                )));
		return 'block';
		
	}
}
?>