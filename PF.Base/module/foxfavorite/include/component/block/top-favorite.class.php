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
class FoxFavorite_Component_Block_Top_Favorite extends Phpfox_Component
{
	public function process()
	{
		
		$aMostFavorites = array();
		$bFlag = true;
		$iOffset = 0;
		$iPage = 0;
		$iTotalFeed = 5;
		$iCountFeed = 5;
		$aUser = $this->getParam('aUser');
		$aTotalFoxFavorite = phpfox::getLib('database')->select('*')
							->from(phpfox::getT('foxfavorite'))
							->group('item_id, type_id')
							->execute('getRows');		
		$iTotalFoxFavorite = count($aTotalFoxFavorite);
		while($bFlag)
		{
			list($iOwnerUserId, $aFavorites) = Phpfox::getService('foxfavorite')->getMostFavorites($iOffset, $iCountFeed);
			if(count($aMostFavorites) >= $iTotalFeed || $iOffset >= $iTotalFoxFavorite)
			{
				$bFlag = false;
			}
			else
			{
				if(!empty($aFavorites))
				{
					$aMostFavorites = array_merge($aMostFavorites, $aFavorites);
				}
				$iCountFeed = $iTotalFeed - count($aMostFavorites);
				$iPage++;
				$iOffset = $iPage * $iTotalFeed - 1;
				if($iCountFeed <= 0)
				{
					break;
				}
				
			}
		}

		$this->template()->assign(array(
								'aFavorites'=>$aMostFavorites,
								'sHeader'=>'Top Favorites'
								));
		return 'block';
		
	}
}
?>