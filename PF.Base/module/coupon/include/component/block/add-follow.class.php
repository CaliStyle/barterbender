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
class Coupon_Component_Block_Add_Follow extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		Phpfox::isUser(TRUE);
		
		$sLink = '';
		
		$iId = (int) $this->getParam('iId');
		$iFollowId = phpfox::getLib('database')->select('follow_id')->from(phpfox::getT('coupon_follow'))->where("coupon_id = {$iId} and user_id =".phpfox::getUserId())->execute('getSlaveField');
        if (!$iFollowId)
        {
            Phpfox::getService('coupon.process')->addFollow($iId);
			$sLink = phpfox::getLib('url')->makeUrl('coupon.view_following');
		}
		
		$this->template()->assign(array(
			'sLink'	=>	$sLink
		));	
	}
}

?>