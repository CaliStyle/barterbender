<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FoxFeedsPro
 * @version          3.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<?php
class FoxFeedsPro_Component_Block_Favorite_Link extends Phpfox_Component {
	public function process() {
		if (!phpFox::isModule('favorite'))
			return false;
		$sView = phpfox::getLib('request')->get('view');
		$bIsMyFavoritePage = 0;
		if(isset($sView) && $sView == 'favourite')
		{
			$bIsMyFavoritePage = 1;
		}

		$this -> template() -> assign(array(
			'item_id' => $this -> getParam('item_id'),
			'favorite_id' => $this -> getParam('favorite_id'),
			'is_favorite' => true,
			'bIsMyFavoritePage'=>$bIsMyFavoritePage
		));
	}

}
?>