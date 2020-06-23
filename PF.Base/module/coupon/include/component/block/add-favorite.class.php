<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_Coupon
 * @version        3.01
 * 
 */
class Coupon_Component_Block_Add_Favorite extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		Phpfox::isUser(TRUE);
		
		$sLink = '';
		
		$iId = (int) $this->getParam('iId');
		
		if (Phpfox::getService('coupon.process')->addFavorite($iId))
		{
			$sLink = phpfox::getLib('url')->makeUrl('coupon.view_favorite');
		}
		
		$this->template()->assign(array(
			'sLink'	=>	$sLink
		));	
	}
}

?>