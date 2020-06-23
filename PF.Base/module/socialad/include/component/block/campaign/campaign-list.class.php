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
class Socialad_Component_Block_Campaign_Campaign_List extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$aCore = $this->request()->get('core');
		$iItemPerPage = 10;
		$iPage = 1;
		$aConds = array();

		if($aVals = $this->getParam('aQueryParam')) {
			if(isset($aVals['campaign_status']) && $aVals['campaign_status']) {
				$aConds[] = 'sac.campaign_status = ' . $aVals['campaign_status'];
			}

			if(isset($aVals['page']) && $aVals['page']) {
				$iPage = $aVals['page'];
			}
		}

		$aExtra['limit'] = $iItemPerPage;
		$aExtra['page'] = $iPage ; // without count, page is offset

		if(!isset($aCore['is_admincp'])){
			if(!Phpfox::isAdminPanel()) {		
				$aConds[] = 'sac.campaign_user_id = ' . Phpfox::getUserId();
			}
		} else if($aCore['is_admincp'] !=  1){
			// check for ajax request 
			$aConds[] = 'sac.campaign_user_id = ' . Phpfox::getUserId();
		}
        $isAdminPanel = false;
		if((isset($aCore['is_admincp']) && $aCore['is_admincp'] ==  1)
			|| Phpfox::isAdminPanel() == true
		){
            $isAdminPanel = true;
		}

        $this->template()->assign(array(
            'isAdminPanel' => $isAdminPanel
        ));
		$aCampaigns = Phpfox::getService('socialad.campaign')->getCampaign($aConds, $aExtra);
		foreach($aCampaigns as &$aCampaign) {
			$aCampaign = Phpfox::getService('socialad.campaign')->retrievePermission($aCampaign);
		}

		$this->setParam('aPagingParams', array(
			'total_all_result' => Phpfox::getService('socialad.campaign')->count($aConds),
			'total_result' => count($aCampaigns),
			'page' => $iPage,
			'limit' => $iItemPerPage
		));

		$this->template()->assign(array(
			'aSaCampaigns' => $aCampaigns
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

