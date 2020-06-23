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

class Socialad_Service_Ad_Placement extends Phpfox_Service
{
	private $_aBlocks ;
    public function __construct() {
		$this->_aBlocks = array(
			1, 2, 3, 4, 5, 6, 7, 8, 9 ,10, 11, 12
		);

		$this->_sModuleTable = Phpfox::getT('socialad_ad_placement_module');

    }

	public function getModulesOfAd($iAdId) {

		$aRows = $this->database()->select("module_id")
			->from($this->_sModuleTable)
			->where(" ad_id = " . $iAdId)
			->execute("getRows");
		$aResult = array();
		if($aRows) {
			foreach($aRows as $aRow) {
				$aResult[] = $aRow["module_id"];
			}
		}

		return $aResult;
	}

	public function addModule($iAdId, $aModules) {
		$this->database()->delete($this->_sModuleTable, 'ad_id = ' . $iAdId);
		foreach($aModules as $sModuleId) {
			$aModuleInsert = array( 
				'ad_id' => $iAdId,
				'module_id' => $sModuleId
			);

			$this->database()->insert($this->_sModuleTable, $aModuleInsert);
		}
	}

	public function getBlockCond($iBlockId) {
		// implicit assumption is alias of ad table is "ad" 
		return '( ad.placement_block_id =  ' . $iBlockId . ' )';

	}

	public function getModuleCond($sModuleId) {
		$this->database()->leftJoin(Phpfox::getT('socialad_ad_placement_module'), 'adpm', 'adpm.ad_id = ad.ad_id');
		$this->database()->leftJoin(Phpfox::getT('socialad_package_placement_module'), 'pkdpm', 'pkdpm.package_id = ad.ad_package_id');

		return "( adpm.module_id = '" . $sModuleId  . "' OR pkdpm.module_id  = '" . $sModuleId  . "' OR ( sp.package_allow_module IS NULL AND ( adpm.module_id = '" . $sModuleId  . "' OR adpm.module_id IS NULL ) ) )" ;
	}

	/**
	 * @params $iBlockId id of block
	 * @params $iModuleId id of module
	 * @return string it is a string of condition in where clause
	 */
	public function getPlacementConds($iBlockId, $sModuleId) {

		$aConds = array();
		if($iBlockId) {
			$aConds[] = $this->getBlockCond($iBlockId);
		}

		if($sModuleId) {
			$aConds[] = $this->getModuleCond($sModuleId);
		}

		return implode(' AND ', $aConds);

	}

	public function getBlocks() {
		return $this->_aBlocks;
	}

	public function getModules() {
		return Phpfox::getService('admincp.module')->getModules();

	}

	public function add($aVals) {

	}

}



