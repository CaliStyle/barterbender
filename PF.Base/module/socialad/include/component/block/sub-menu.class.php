<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		MinhTA
 * @package  		Module_socialad
 */

// Add and edit request both go here 
class Socialad_Component_Block_Sub_Menu extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aFilterMenu = array(
			_p('my_campaigns') 	=> 'socialad.campaign',
			_p('my_ads') 	 	=> 'socialad.ad',
			_p('payments') 		=> 'socialad.payment',
			_p('report') 	 	=> 'socialad.report',
			_p('manage_credit') => 'socialad.creditmoney',
			_p('faqs') 	 		=> 'socialad.faq',
		);
		
		$this -> template() -> buildSectionMenu('socialad', $aFilterMenu);


	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}

