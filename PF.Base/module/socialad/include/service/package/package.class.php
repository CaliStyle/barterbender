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

class Socialad_Service_Package_Package extends Phpfox_Service
{

	private $_aPackageBenefitTypes;

    public function __construct()
    {
        $this->_sPackageTable = Phpfox::getT('socialad_package');

		// sap stands for social ad package
        $this->_sPackageAlias = 'sap';

		$this->_aPackageBenefitTypes = array(
			'click' => array ( 
				'id'     => 1,
				'phrase' => _p('clicks'),
				'name'   => 'click'
			),
			'impression' => array ( 
				'id'     => 2,
				'phrase' => _p('impressions'),
				'name'   => 'impression',
			),
			'day' => array ( 
			 	'id'     => 3,
			 	'phrase' => _p('days'),
			 	'name'   => 'day'
			 ),
		);
    }

	public function getAllPackageBenefitTypes() {
		return $this->_aPackageBenefitTypes;
	}
	
	public function getBlocksOfPackage($iPackageId) {
		$aPackage = $this->getPackageById($iPackageId);
		return $aPackage['package_allow_block'] ? $aPackage['package_allow_block'] : Phpfox::getService('socialad.ad.placement')->getBlocks();
	}

	public function getModulesOfPackage($iPackageId) {
		$aPackage = $this->getPackageById($iPackageId);
		return $aPackage['package_allow_module'] ? $aPackage['package_allow_module'] : Phpfox::getService('socialad.ad.placement')->getModules();;
	}
	
	public function getItemTypesOfPackage($iPackageId) {
		$aPackage = $this->getPackageById($iPackageId);
		$aAdTypes = $aPackage['package_allow_item_type'] ? Phpfox::getService('socialad.ad.item')->getItemTypesFromIds($aPackage['package_allow_item_type']) : Phpfox::getService('socialad.ad.item')->getAllItemTypes();
		return $aAdTypes;
	}

	public function getAdTypesOfPackage($iPackageId) {
		$aPackage = $this->getPackageById($iPackageId);
		if($aPackage['package_allow_ad_type']) {
			$aAdTypes = Phpfox::getService('socialad.ad')->getAdTypesByIds($aPackage['package_allow_ad_type']);
		} else {
			$aAdTypes = Phpfox::getService('socialad.ad')->getAllAdTypes();
		}

		return $aAdTypes;
	}

	public function getTable() {
		return $this->_sPackageTable;
	}

	public function getAlias() {
		return $this->_sPackageAlias;
	}

	public function getPackageById($iPackageId) {
		$aRow = $this->database()->select('*')
			->from($this->_sPackageTable)
			->where('package_id = ' . $iPackageId)
			->execute('getRow');

		$aRow = $this->processPackageData($aRow);

		return $aRow;
	}

	public function processPackageData($aPackage) {
		if(!$aPackage) {
			return false;
		}
		if($aPackage['package_allow_item_type']) {
			$aPackage['package_allow_item_type'] = unserialize($aPackage['package_allow_item_type']);
		}

		if($aPackage['package_allow_block']) {
			$aPackage['package_allow_block'] = unserialize($aPackage['package_allow_block']);
		}

		if($aPackage['package_allow_module']) {
			$aPackage['package_allow_module'] = unserialize($aPackage['package_allow_module']);
		}

		if($aPackage['package_allow_ad_type']) {
			$aPackage['package_allow_ad_type'] = unserialize($aPackage['package_allow_ad_type']);
		}

		if($aPackage['package_benefit_type_id'] == 0) {
			$aPackage['package_benefit_type_name'] = 'unlimited';
			$aPackage['package_is_unlimited'] = 1;
		} else { 
			$aPackage['package_is_unlimited'] = 0;
			$aPackage['package_benefit_type_name'] = Phpfox::getService('socialad.helper')->getNameById('package.benefit', $aPackage['package_benefit_type_id']);
		}

		if($aPackage['package_price'] == 0) {
			$aPackage['package_is_free'] = 1;
		} else { 
			$aPackage['package_is_free'] = 0;
		}

		$aPackage['number_of_ad'] = Phpfox::getService('socialad.ad')->countAdByPackageId($aPackage['package_id']);


		$aPackage = $this->transformPackageDataToDisplayedText($aPackage);

		return $aPackage;
	}

	/**
	 * <pre>
	 * 		$aPackage = transformPackageDataToDisplayedText($aPackage);
	 * </pre>
	 * @params $aPackage array of package data
	 * @return $aPackage array of modified package data
	 */
	public function transformPackageDataToDisplayedText($aPackage) {
		$aPackage['package_price_text'] = $aPackage['package_is_free'] ? _p('free_upper') : Phpfox::getService('socialad.helper')->getMoneyText($aPackage['package_price'], $aPackage['package_currency']);

		$aPackage['package_benefit_text'] = $aPackage['package_is_unlimited'] ? _p('unlimited_upper') : ($aPackage['package_benefit_number'] . ' ' . Phpfox::getService('socialad.helper')->getPhraseById('package.benefit', $aPackage['package_benefit_type_id']));

		$aPackage['package_benefit_type_text'] = Phpfox::getService('socialad.helper')->getPhraseById('package.benefit', $aPackage['package_benefit_type_id']);

		$aPackage['package_allow_block_text'] = !$aPackage['package_allow_block'] ? _p('all_blocks') : (_p('block_sa') . ' ' . implode(', ', $aPackage['package_allow_block']));

		$aAdTypesStrings = array();
		if($aPackage['package_allow_ad_type']) {
			foreach($aPackage['package_allow_ad_type'] as $iAdTypeId) {
				$aAdTypesStrings[] = Phpfox::getService('socialad.helper')->getPhraseById('ad.type', $iAdTypeId);
			}
		}

		// get all ad types phrase under string form: banner, html, ... 
		$aAllAdTypesStrings = array();
		$aAdTypeCustomTextList= array();
		$aAllAdTypes = Phpfox::getService('socialad.ad')->getAllAdTypes();

		foreach($aAllAdTypes as $aAdTypes) {
			$isAddAdTypePhrase = true;
			if(is_array($aPackage['package_allow_ad_type'])){
				$isAddAdTypePhrase = false;
				if(in_array($aAdTypes['id'], $aPackage['package_allow_ad_type'])){
					$isAddAdTypePhrase = true;
				}
			}

			if($isAddAdTypePhrase == true){
				$aAllAdTypesStrings[] = $aAdTypes['phrase'];

				if($aAdTypes['name'] == 'html') {
					$aAdTypeCustomTextList[] = '<strong> ' . $aAdTypes['phrase'] . '</strong> ' . _p('on_block_3');
				} else if ($aAdTypes['name'] == 'banner') { 
					$aAdTypeCustomTextList[] = '<strong> ' . $aAdTypes['phrase'] . '</strong> ' . _p('on') . ' ' . $aPackage['package_allow_block_text'];
				} else if ($aAdTypes['name'] == 'feed') {
					$aAdTypeCustomTextList[] =  '<strong> ' . $aAdTypes['phrase'] . '</strong> '. _p('on_newsfeed');
				}
			}
		}
		$sAllAdTypesString = implode(', ', $aAllAdTypesStrings);

		
		$aPackage['package_allow_ad_type_text'] = !$aPackage['package_allow_ad_type'] ? $sAllAdTypesString : (implode(', ', $aAdTypesStrings));

		$aPackage['package_allow_ad_type_custom_text_list'] = $aAdTypeCustomTextList; 

		// get item types of ad under string form
		$aItemTypesStrings = array();
		if($aPackage['package_allow_item_type']) {
			foreach($aPackage['package_allow_item_type'] as $iItemTypeId) {
				$aItemTypesStrings[] = Phpfox::getService('socialad.helper')->getPhraseById('ad.itemtype', $iItemTypeId);
			}
		}

		$aAllItemTypesStrings = array();
		$aAllItemTypes = Phpfox::getService('socialad.ad.item')->getAllItemTypes();
		foreach($aAllItemTypes as $aItemTypes) {
			$aAllItemTypesStrings[] = $aItemTypes['phrase'];
		}
		$sAllItemTypesString = implode(', ', $aAllItemTypesStrings);

		$aPackage['package_allow_item_type_text'] = !$aPackage['package_allow_item_type'] ? $sAllItemTypesString : (implode(', ', $aItemTypesStrings));
		return $aPackage;
	}

	public function getAllActivePackages() {
		$sPackageAlias = $this->_sPackageAlias;
		$aCond[] = " {$sPackageAlias}.package_is_active = 1 ";
		return $this->getPackages($aCond);
	}

	public function getAllPackages() {
		return $this->getPackages();
	}

	public function getPackages($aCond = array(), $bGetDelete = false, $aExtra = array()) {
		//assume that all conds are AND	

		if(!$bGetDelete) {
			$sPackageAlias = $this->_sPackageAlias;
			$aCond[] = " {$sPackageAlias}.package_is_deleted = 0 ";
		}

		if($aExtra && isset($aExtra['order'])) {
			$this->database()->order($aExtra['order']);
		} else {
			$this->database()->order( "{$this->_sPackageAlias}.package_order ASC" );
		}

		$sCond = implode(" AND " , $aCond);
		$aRows = $this->database()->select('*')
			->from($this->_sPackageTable, $this->_sPackageAlias)
			->where($sCond)
			->execute('getRows');

		foreach($aRows as &$aRow) {
			$aRow = $this->processPackageData($aRow);
		}

		return $aRows;
	}

	public function __call($sMethod, $aArguments)
	{
		if ($sPlugin = Phpfox_Plugin::get('socialad.Socialad_Service_Package_Package__call'))
		{
			return eval($sPlugin);
		}
		
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}

}



