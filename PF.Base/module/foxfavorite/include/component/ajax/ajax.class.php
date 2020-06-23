<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		YouNetCo Company
 * @author  		MinhNTK
 * @package 		FoxFavorite_Module
 */
class FoxFavorite_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function add()
	{
		Phpfox::getBlock('foxfavorite.add');
	}
	
	public function getFooterBar()
	{
		Phpfox::isUser(true);
		Phpfox::getBlock('favorite.footer');
		$this->html('#js_footer_bar_favorite_content', $this->getContent(false));
	}
	
	public function delete()
	{
		if (Phpfox::getService('foxfavorite.process')->delete($this->get('favorite_id')))
		{
			
		}
	}
    
    public function addFavorite()
    {
        if(Phpfox::getService('foxfavorite.process')->add($this->get('type'), $this->get('id')))
        {
            
        }
		$sModule = $this->get('type');
		$iItemId = $this->get('id');
		$this->updateWhoFavoriteThisBlock($sModule, $iItemId);
		
    }
	public function updateWhoFavoriteThisBlock($sModule, $iItemId)
	{
		$sHtml = "";
		$aUsers = phpfox::getService('foxfavorite')->getFavoriteMembers($sModule, $iItemId);
			if(empty($aUsers))
			{				
			}else
			{						 
				$sHtml = "<ul>";
				{
					for($i=0;$i<count($aUsers);$i++)
					{
						$aUser = $aUsers[$i];
						$sHtml.= "<li>".Phpfox_Image_Helper::instance()->display(array(
						'user'=>$aUser,
						'suffix'=>'_50_square',
						'max_width'=>'32',
						'max_height'=>'32'
						))
						 ."</li>";
				 	}	
				}
				$sHtml .= "</ul><div class=\"clear\"></div>";
			}
			if (count($aUsers) > 24)
			{
				
				$sHtml .="<style>
				.ffav_whofavthis
				{
					max-height:200px;
					overflow-y:scroll;
				}</style>";
			}
		$this->html('#foxfavorite_member', $sHtml);
		$this->call("ynfoxfavorite_loadlazyimage();");
	}
	public function updateFavoriteMemberBlock()
	{
		Phpfox::getBlock('favorite.footer');
		$this->html('#js_footer_bar_favorite_content', $this->getContent(false));
	}
	
	public function deleteFavorite()
	{
		$iItem = $this->get('id');
		$iItemId = $this->get('id');
		$sModule = $this->get('type');
		if($sModule == 'profile')
		{
			$iItem = phpfox::getService('foxfavorite')->getUserIdFromUserName($iItem);
		}
        $sModule = $sModule == 'v' ? 'video' : $sModule;
		$iFavoriteId = phpfox::getLib('database')->select('favorite_id')
					->from(phpfox::getT('foxfavorite'))
					->where('type_id = "'.$sModule.'" and item_id = '.$iItem.' and user_id ='.phpfox::getUserId())
					->execute('getSlaveField');

		if($iFavoriteId)
		{
			Phpfox::getService('foxfavorite.process')->delete($iFavoriteId);
		} 
		$this->updateWhoFavoriteThisBlock($sModule, $iItemId);
	}
	
	public function updateModuleActivity()
	{
		$sModule = $this->get('id');
		$iActive = $this->get('active');
		if(isset($sModule) && isset($iActive))
		{
			phpfox::getService('foxfavorite.process')->updateSetting($sModule, $iActive);
		}
	}
}

?>