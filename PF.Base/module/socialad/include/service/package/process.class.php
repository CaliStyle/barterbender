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

class Socialad_Service_Package_Process extends Phpfox_Service
{
	
    public function __construct()
    {
        $this->_sPackageTable = Phpfox::getT('socialad_package');
        $this->_sPackageAlias = Phpfox::getT('socialad_package');
    }


	/**
	 * @params $aList = array( order -> id)
	 */
	public function updatePackageOrderByList($aList) {
		foreach($aList as $iOrder => $iPackageId) {
			$this->updateOrder($iPackageId, $iOrder);
		}
	}

	public function updateOrder($iPackageId, $iOrder) {
		$aUpdate = array(
			'package_order' => $iOrder
		);
		$this->database()->update($this->_sPackageTable, $aUpdate, 'package_id = ' . $iPackageId);
	}

	public function delete($iPackageId) {
		$aUpdate = array(
			'package_is_deleted' => 1,
			'package_is_active' => 0
			
		);
		$this->database()->update($this->_sPackageTable, $aUpdate, 'package_id = ' . $iPackageId);

	}

	public function toggle($iPackageId, $iActive) {
		$aUpdate = array(
			'package_is_active' => $iActive
		);
		$this->database()->update($this->_sPackageTable, $aUpdate, 'package_id = ' . $iPackageId);

	}

	public function handleSubmitForm($aVals) {

		$oFilter = Phpfox::getLib('parse.input');

		$aPackage = array (
			'package_name' => $oFilter->clean($aVals['package_name']),
			'package_description' => $oFilter->clean($aVals['package_description']),
			'package_price' => (isset($aVals['package_is_free']) && $aVals['package_is_free']) ? 0 : $aVals['package_price'],
			'package_currency' => $aVals['package_currency'],
			'package_last_edited_time' => PHPFOX_TIME, 
			'package_is_active' => $aVals['package_is_active']
		);

		if(isset($aVals['package_is_unlimited']) && $aVals['package_is_unlimited']) {
			$aPackage['package_benefit_type_id'] = 0;
			$aPackage['package_benefit_number'] = 0;
		} else {
			$aPackage['package_benefit_type_id'] = $aVals['package_benefit_type_id'];
			$aPackage['package_benefit_number'] = $aVals['package_benefit_number'];
		}


		if(isset($aVals['package_allow_item_type']) ) {
			$aPackage['package_allow_item_type'] = serialize($aVals['package_allow_item_type']);
		}
		else {
			$aPackage['package_allow_item_type'] = null;
		}

			
		if(isset($aVals['package_allow_block']) ) {
			$aPackage['package_allow_block'] = serialize($aVals['package_allow_block']);
		}
		else {
			$aPackage['package_allow_block'] = null;
		}

		$package_allow_module = null;
		if(isset($aVals['package_allow_module']) ) {
			$package_allow_module = $aVals['package_allow_module'];
			$aPackage['package_allow_module'] = serialize($aVals['package_allow_module']);
		}
		else {
			$aPackage['package_allow_module'] = null;
		}


		if(isset($aVals['package_allow_ad_type'])) {
			$aPackage['package_allow_ad_type'] = serialize($aVals['package_allow_ad_type']);
		}
		else {
			$aPackage['package_allow_ad_type'] = null;
		}
		if(isset($aVals['package_user_id'])){
			$aPackage['package_user_id'] = $aVals['package_user_id'];
		}
		else{
			$aPackage['package_user_id'] = 0;
		}
		if(isset($aVals['package_id']) && $aVals['package_id']) { // edit package 
			$aUpdate = $aPackage;
			$iPackageId = $aVals['package_id'];
			$this->database()->update($this->_sPackageTable, $aUpdate, 'package_id = ' . $iPackageId);
			
		} else { // add new package 

			$aInsert = $aPackage;
			$iPackageId = $this->database()->insert($this->_sPackageTable, $aInsert);
		}
		
		// update others
		if($package_allow_module !== null){
			$this->database()->delete(Phpfox::getT('socialad_package_placement_module'), 'package_id = ' . (int) $iPackageId);
			foreach($package_allow_module as $module){
				$iId = $this->database()->insert(Phpfox::getT('socialad_package_placement_module'), array(
						'package_id' => (int) $iPackageId,
						'module_id' => $module,
					)
				);
			}
		} 		

		return $iPackageId;
	}

	public function __call($sMethod, $aArguments)
	{
		if ($sPlugin = Phpfox_Plugin::get('socialad.Socialad_Service_Package_Process__call'))
		{
			return eval($sPlugin);
		}
		
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}

}



