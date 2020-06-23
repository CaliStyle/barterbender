<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Donation
 * @version 		$Id: sample.class.php 1 2012-02-15 10:33:17Z YOUNETCO $
 */
class Donation_Component_Controller_Index extends Phpfox_Component
{
	/**
	 * Class process method which is used to execute this component.
	 */
	public function process()
	{
	    $this->url()->send('');
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('donation.component_controller_index_clean')) ? eval($sPlugin) : false);
	}

}

?>