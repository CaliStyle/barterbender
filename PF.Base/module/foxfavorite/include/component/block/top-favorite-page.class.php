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
class FoxFavorite_Component_Block_Top_Favorite_Page extends Phpfox_Component
{
	public function process()
	{
		if(!phpfox::isModule('pages'))
		{
			return false;
		}
		$aTopFavPages = Phpfox::getService('foxfavorite')->getTopFavoritePages();
		
		if (!count($aTopFavPages))
		{
			return false;
		}
		
		$this->template()->assign(array(
				'sHeader' => _p('foxfavorite.top_favorite_pages'),
				'aTopFavPages' => $aTopFavPages,
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