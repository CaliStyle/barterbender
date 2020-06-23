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
class Socialad_Component_Block_Payment_Transaction_List extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aCore = $this->request()->get('core');
		$iItemPerPage = 30;
		$iPage = 1;
		$aConds = array();

		if($aVals = $this->request()->get('val')) {
			if(isset($aVals['transaction_method_id']) && $aVals['transaction_method_id']) {
				$aConds[] = 'sat.transaction_method_id = ' . $aVals['transaction_method_id'];
			}

			if(isset($aVals['transaction_status_id']) && $aVals['transaction_status_id']) {
				$aConds[] = 'sat.transaction_status_id = ' . $aVals['transaction_status_id'];
			}

			if(isset($aVals['page']) && $aVals['page']) {
				$iPage = $aVals['page'];
			}
		}
        $isAdmin = true;
		if(!isset($aCore['is_admincp'])){
			if(!Phpfox::isAdminPanel()) {
                $isAdmin = false;
				$aConds[] = 'sat.transaction_user_id = ' . Phpfox::getUserId();
			}
		} else if($aCore['is_admincp'] !=  1){
            $isAdmin = false;
			// check for ajax request 
			$aConds[] = 'sat.transaction_user_id = ' . Phpfox::getUserId();
		}

		$aExtra['limit'] = $iItemPerPage;
		$aExtra['page'] = $iPage; // without count, page is offset
		$aTransactions = Phpfox::getService("socialad.payment")->getWithPermission($aConds, $aExtra);

		$this->setParam('aPagingParams', array(
			'total_all_result' => Phpfox::getService('socialad.payment')->count($aConds),
			'total_result' => count($aTransactions),
			'page' => $iPage,
			'limit' => $iItemPerPage
		));

		$this->template()->assign(array( 
			'aTransactions' => $aTransactions,
            'isAdmin' => $isAdmin
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

