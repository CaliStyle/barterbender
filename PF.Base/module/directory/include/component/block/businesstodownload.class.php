<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_businesstodownload extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {
		$iBusinessId  = $this->getParam('iBusinessId');

		$aBusiness = Phpfox::getService('directory')->getBusinessById($iBusinessId);
		if(isset($aBusiness['business_id'])){
			$category_id_main = 0;
        	$aParentCategory = Phpfox::getService('directory.category')->getParentCategoryByBusinessId($iBusinessId);
        	foreach ($aParentCategory as $aParentCategoryKey => $aParentCategoryItem) {
        		$aChildCategory = Phpfox::getService('directory.category')->getChildCategoryByParentAndBusiness($aParentCategoryItem['category_id'], $iBusinessId);
        		foreach ($aChildCategory as $aChildCategoryKey => $aChildCategoryItem) {
        			$aChildCategory[$aChildCategoryKey]['title'] = _p($aChildCategory[$aChildCategoryKey]['title']);
        		}
        		$aParentCategory[$aParentCategoryKey]['list_child'] = $aChildCategory;

        		$aParentCategory[$aParentCategoryKey]['title'] = _p($aParentCategory[$aParentCategoryKey]['title']);

        		if($aParentCategoryItem['is_main']){
        			$category_id_main = $aParentCategoryItem['category_id'];
        		}
        	}
        	$aBusiness['list_category'] = ($aParentCategory);
        	$aBusiness['category_id_main'] = ($category_id_main);

        	$aPhones = Phpfox::getService('directory')->getBusinessPhone($iBusinessId);
        	$aWebsites = Phpfox::getService('directory')->getBusinessWebsite($iBusinessId);
        	$aBusiness['total_phone'] = count($aPhones);
        	$aBusiness['list_phone'] = ($aPhones);
            if(count($aPhones) > 1){
            	$aPhonesDivide = Phpfox::getService('directory.helper')->array_divide($aPhones, 2);
	            $aBusiness['list_phone_first'] = $aPhonesDivide[0];
	            $aBusiness['list_phone_second'] = $aPhonesDivide[1];
            } else {
	            $aBusiness['list_phone_first'] = $aPhones;
	            $aBusiness['list_phone_second'] = array();
            }

        	$aFaxes = Phpfox::getService('directory')->getBusinessFax($iBusinessId);
        	$aBusiness['total_fax'] = count($aFaxes);
        	$aBusiness['list_fax'] = ($aFaxes);
            if(count($aFaxes) > 1){
            	$aFaxesDivide = Phpfox::getService('directory.helper')->array_divide($aFaxes, 2);
	            $aBusiness['list_fax_first'] = $aFaxesDivide[0];
	            $aBusiness['list_fax_second'] = $aFaxesDivide[1];
            } else {
	            $aBusiness['list_fax_first'] = $aFaxes;
	            $aBusiness['list_fax_second'] = array();
            }

        	$aBusiness['total_website'] = count($aWebsites);
        	$aBusiness['list_website'] = ($aWebsites);

            $aVistingHourInfo = Phpfox::getService('directory')->getBusinessVistingHour($iBusinessId);
            foreach ($aVistingHourInfo as $key2 => $value2) {
            	$aVistingHourInfo[$key2]['phrase'] = Phpfox::getService('directory.helper')->getPhraseById('date.dayofweek', $value2['vistinghour_dayofweek']);
            }
            $aBusiness['total_visitinghour'] = count($aVistingHourInfo);
            $aBusiness['list_visitinghour'] = ($aVistingHourInfo);
            if(count($aVistingHourInfo) > 1){
            	$aVistingHourInfoDivide = Phpfox::getService('directory.helper')->array_divide($aVistingHourInfo, 2);
	            $aBusiness['list_visitinghour_first'] = $aVistingHourInfoDivide[0];
	            $aBusiness['list_visitinghour_second'] = $aVistingHourInfoDivide[1];
            } else {
	            $aBusiness['list_visitinghour_first'] = $aVistingHourInfo;
	            $aBusiness['list_visitinghour_second'] = array();
            }

            $aLocations = Phpfox::getService('directory')->getBusinessLocation($iBusinessId);
            $aBusiness['total_location'] = count($aLocations);
            $aBusiness['list_location'] = ($aLocations);
            if(count($aLocations) > 1){
            	$aLocationsDivide = Phpfox::getService('directory.helper')->array_divide($aLocations, 2);
	            $aBusiness['list_location_first'] = $aLocationsDivide[0];
	            $aBusiness['list_location_second'] = $aLocationsDivide[1];
            } else {
	            $aBusiness['list_location_first'] = $aLocations;
	            $aBusiness['list_location_second'] = array();
            }

            $aAdditionInfos = Phpfox::getService('directory')->getBusinessAdditionInfo($iBusinessId);
            $aBusiness['total_addinfo'] = count($aAdditionInfos);
            $aBusiness['list_addinfo'] = ($aAdditionInfos);

			$this->template()->assign(array(
					'aBusiness' => $aBusiness, 
				)
			);
		}

		$this->template()->assign(array(
				'iBusinessId' => $iBusinessId, 
				'sCorePath' => Phpfox::getParam('core.path'),
                'sCustomClassName' => 'ync-block'
			)
		);
	}

}

?>
