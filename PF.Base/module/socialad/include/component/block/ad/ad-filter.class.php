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
class Socialad_Component_Block_Ad_Ad_Filter extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iCampaignId  = $this->getParam('iFilterCampaignId');
        $iDefaultStatusId = $this->getParam('status', Phpfox::getService('socialad.helper')->getConst('ad.status.running'));
        $bHideForm = $this->getParam('hide_form',false);
		// check param if existing
		$aQueryParam = $this->getParam('aQueryParam');
		if(is_array($aQueryParam)){
			$aQueryParam = array_merge($aQueryParam, array('ad_status' => $iDefaultStatusId));
		} else {
			$aQueryParam = array('ad_status' => $iDefaultStatusId);
		}
		$this->setParam('aQueryParam', $aQueryParam);
		
		$this->template()->assign(array(
			'aAdStatuses' => Phpfox::getService('socialad.ad')->getAllStatuses(),
			'aAdTypes' => Phpfox::getService('socialad.ad')->getAllAdTypes(),
			'iFilterCampaignId' => $iCampaignId,
			'iFilterDefaultStatusId' => $iDefaultStatusId,
            'bHideForm' => $bHideForm,
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
