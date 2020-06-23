<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

define('PHPFOX_SKIP_POST_PROTECTION', true);

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Component
 * @version 		$Id: index.class.php 1558 2010-05-04 12:51:22Z Raymond_Benc $
 */
class Coupon_Component_Controller_Invoice_Index extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{

    }
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_controller_invoice_index_clean')) ? eval($sPlugin) : false);
	}
}

?>