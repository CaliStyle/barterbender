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


class Socialad_Component_Controller_Admincp_Credit_Pending extends Phpfox_Component 
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

		$yncm_user_id = $this->getParam('yncm_user_id');

		if((int)$yncm_user_id <= 0){
			$yncm_user_id = Phpfox::getUserId();
		}

		if($aVals = $this->request()->get('val')) {
			if(isset($aVals['creditmoneyrequest_status_id']) && $aVals['creditmoneyrequest_status_id']) {
				$aConds[] = 'cmr.creditmoneyrequest_status = ' . $aVals['creditmoneyrequest_status_id'];
			}
		}		

		$aExtra['limit'] = $iItemPerPage;
		$aExtra['page'] = ($iPage - 1) * $iItemPerPage; // without count, page is offset
		list($iCnt, $aCreditMoneyRequest) = Phpfox::getService('socialad.ad')->getCreditMoneyRequestByStatus(
			Phpfox::getService('socialad.helper')->getConst('creditmoneyrequest.status.pending')
			, $aExtra
		);		

		$this->setParam('aPagingParams', array(
			'total_all_result' => $iCnt,
			'total_result' => count($aCreditMoneyRequest),
			'page' => $iPage,
			'limit' => $iItemPerPage
		));

		$this->template()->assign(array( 
			"aCreditMoneyRequest" => $aCreditMoneyRequest,
		));

		$this->template()
			->setTitle(_p('pending_credit_request'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("module_socialad"), $this->url()->makeUrl('admincp.app').'?id=__module_socialad')
			->setBreadcrumb(_p('pending_credit_request'));

		Phpfox::getService('socialad.helper')->loadAdminSocialAdJsCss();
	}

	public function clean()
	{
	}

}

