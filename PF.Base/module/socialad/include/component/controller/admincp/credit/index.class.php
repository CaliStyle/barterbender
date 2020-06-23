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


class Socialad_Component_Controller_Admincp_Credit_Index extends Phpfox_Component 
{
	/**
	 * Class process method which is used to execute this component.
	 */
	public function process()
	{
		$search_user = $this->request()->get('search_user');
		$iItemPerPage = 30;
		$iPage = 1;
		$aConds = array();

		if(strlen(trim($search_user)) > 0){
			$this->template()->assign(array( 
				"search_user" => $search_user,
			));
			$aConds[] = 'u.full_name LIKE \'%' . trim($search_user) . '%\' ';
		}

		$aExtra['limit'] = $iItemPerPage;
		$aExtra['page'] = ($iPage - 1) * $iItemPerPage; // without count, page is offset
		list($iCnt, $aCreditMoney) = Phpfox::getService('socialad.ad')->getCreditMoney($aConds, $aExtra);		

		$aCurrentCurrencies = Phpfox::getService('socialad.helper')->getCurrentCurrencies($sGateway = 'paypal');

		$this->setParam('aPagingParams', array(
			'total_all_result' => $iCnt,
			'total_result' => count($aCreditMoney),
			'page' => $iPage,
			'limit' => $iItemPerPage
		));

		$this->template()->assign(array( 
			"aCreditMoney" => $aCreditMoney,
			"aCurrentCurrency" => $aCurrentCurrencies[0],
		));

		$this->template()
			->setTitle(_p('manage_credit'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("module_socialad"), $this->url()->makeUrl('admincp.app').'?id=__module_socialad')
			->setBreadcrumb(_p('manage_credit'));

		Phpfox::getService('socialad.helper')->loadAdminSocialAdJsCss();
	}

	public function clean()
	{
	}

}

