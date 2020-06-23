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
class Socialad_Component_Block_Creditmoney_List extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aCore = $this->request()->get('core');
		$yncm_user_id = $this->getParam('yncm_user_id');
		$iItemPerPage = 30;
		$iPage = 1;
		$aConds = array();

		if((int)$yncm_user_id <= 0){
			$yncm_user_id = $this->request()->get('yncm_user_id');
		}
        $bIsAdmin = true;
		if(!isset($aCore['is_admincp'])){
			if(!Phpfox::isAdminPanel()) {
                $bIsAdmin = false;
				// in front end
				$yncm_user_id = Phpfox::getUserId();
			} else {

			}
		} else if($aCore['is_admincp'] !=  1){
            $bIsAdmin = false;
			// in front end
			// check for ajax request 
			$yncm_user_id = Phpfox::getUserId();
		}				

		if($aVals = $this->request()->get('val')) {
			if(isset($aVals['creditmoneyrequest_status_id']) && $aVals['creditmoneyrequest_status_id']) {
				$aConds[] = 'cmr.creditmoneyrequest_status = ' . $aVals['creditmoneyrequest_status_id'];
			}
			
			if(isset($aVals['page']) && $aVals['page']) {
				$iPage = $aVals['page'];
			}
		}		

		$aExtra['limit'] = $iItemPerPage;
		$aExtra['page'] = $iPage; // without count, page is offset
		list($iCnt, $aCreditMoneyRequest) = Phpfox::getService('socialad.ad')->getCreditMoneyRequestByUserId((int)$yncm_user_id, $aConds, $aExtra);

		$this->setParam('aPagingParams', array(
			'total_all_result' => $iCnt,
			'total_result' => count($aCreditMoneyRequest),
			'page' => $iPage,
			'limit' => $iItemPerPage
		));

		$this->template()->assign(array( 
			"aCreditMoneyRequest" => $aCreditMoneyRequest,
            'bIsAdminManage' => $this->getParam('bIsAdminManage',false) || $bIsAdmin
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

