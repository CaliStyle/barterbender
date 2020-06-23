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
class Socialad_Component_Block_Payment_Transaction_Filter extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$this->template()->assign(array( 
			'aTransactionMethod' => Phpfox::getService('socialad.payment')->getAllTransactionMethods(),
			'aTransactionStatus' => Phpfox::getService('socialad.payment')->getAllTransactionStatus(),
            'bIsAdminManage' => $this->getParam('bIsAdminManage',false)
		));

	}


	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}

