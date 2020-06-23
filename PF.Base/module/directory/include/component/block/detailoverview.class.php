<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_detailoverview extends Phpfox_Component {

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process() {

		$aYnDirectoryDetail = $this -> getParam('aYnDirectoryDetail');
		$aBusiness = $aYnDirectoryDetail['aBusiness'];

		$aCategories = Phpfox::getService('directory.category') -> getCategoriesById($aBusiness['business_id']);

		/*Visit Hour*/

		$aVisitingHours = Phpfox::getService('directory.helper') -> getVisitingHours();
		$aVisitingHoursDetail = array();
		foreach ($aVisitingHours['dayofweek'] as $key => $visit) {
			$aVisitingHoursDetail[$visit['id']] = $visit;
		}

		$todayOfWeek = array();
		$today_dayofweek = Phpfox::getTime('N', PHPFOX_TIME);
		foreach ($aBusiness['vistinghours'] as $key_visithour => &$value_visithour) {
			$aBusiness['vistinghours'][$key_visithour]['vistinghour_dayofweek_phrase'] = $aVisitingHoursDetail[$aBusiness['vistinghours'][$key_visithour]['vistinghour_dayofweek']]['phrase'];

			if ($aBusiness['vistinghours'][$key_visithour]['vistinghour_dayofweek'] == $today_dayofweek) {
				$todayOfWeek = $aBusiness['vistinghours'][$key_visithour];
			}
		}

		/*suggested business*/
		$aSuggestedBusinesses = Phpfox::getService('directory') -> getSuggestedBusiness($aBusiness['business_id']);
		if (count($aSuggestedBusinesses)) {
			foreach ($aSuggestedBusinesses as $key_business => $aValueBusiness) {
				$aSuggestedBusinesses[$key_business]['count_member'] = Phpfox::getService('directory') -> getCountMemberOfBusiness($aValueBusiness['business_id']);
				$aSuggestedBusinesses[$key_business]['count_review'] = Phpfox::getService('directory') -> getCountReviewOfBusiness($aValueBusiness['business_id']);
                $aSuggestedBusinesses[$key_business]['default_logo_path'] = Phpfox::getService('directory')->getStaticPath() . 'module/directory/static/image/default_ava.png';
            }
		}

		$aTimeZones = Phpfox::getService('core') -> getTimeZones();
		$aBusiness['time_zone'] = $aTimeZones[$aBusiness['time_zone']];
		/*                echo '<pre>';
		 var_dump($aBusiness);
		 die;
		 */

		foreach ($aBusiness['websites'] as $keywebsites => $valuewebsites) {
			$url = $valuewebsites['website_text'];
			// to clarify, this shouldn't be === false, but rather !== 0
			if (false === strpos($url, 'http://') && false === strpos($url, 'https://')) {
				$url = "//{$url}";
			}
			$aBusiness['websites'][$keywebsites]['link'] = $url;
		}
        foreach($aBusiness['vistinghours'] as &$valueHour)
        {
            $valueHour = Phpfox::getService('directory.helper')->getHoursFormatToView($valueHour);
        }

		$this -> template() -> assign(array('sTextCategories' => $aCategories, 'aBusiness' => $aBusiness, 'aVisitingHoursDetail' => $aVisitingHoursDetail, 'todayOfWeek' => $todayOfWeek, 'aSuggestedBusinesses' => $aSuggestedBusinesses, 'isPrintPage' => (isset($aYnDirectoryDetail['isPrintPage']) ? 1 : 0)));
		/*custom field*/

		$iBusinessId = $aBusiness['business_id'];
		$aMainCategory = Phpfox::getService('directory') -> getBusinessMainCategory($iBusinessId);
		$aCustomFields = [];
		if (!empty($aMainCategory['category_id'])) {
            $aCustomFields = Phpfox::getService('directory') -> getCustomFieldByCategoryId($aMainCategory['category_id']);
        }

		$keyCustomField = array();

		$aCustomData = array();
		if ($iBusinessId) {
			$aCustomDataTemp = Phpfox::getService('directory.custom') -> getCustomFieldByBusinessId($iBusinessId);

			if (count($aCustomFields)) {
				foreach ($aCustomFields as $aField) {
					foreach ($aCustomDataTemp as $aFieldValue) {
						if ($aField['field_id'] == $aFieldValue['field_id']) {
							$aCustomData[] = $aFieldValue;
						}
					}
				}
			}

		}

		if (count($aCustomData)) {
			$aCustomFields = $aCustomData;
		}

		$this -> template() -> assign(array('aCustomFields' => $aCustomFields,'sCustomClassName' => 'ync-block' ));

	}

}
?>
