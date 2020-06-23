<?php

defined('PHPFOX') or exit('NO DICE!');


class Directory_Component_Controller_comparebusiness extends Phpfox_Component
{
	public function process()
	{
		Phpfox::getService('directory.helper')->buildMenu();
		$category_id = $this->request()->get('category', '');
		$business_ids_cookie = Phpfox::getCookie('yndirectory_compare_name');
		if($business_ids_cookie == '' || (int)$category_id <= 0){
			$this->url()->send('directory');
			return false;
		}

		$aCategory = array();
		$aBusinessCompare = array();
        $aListOfBusinessIdToCompareCookie = explode(',', $business_ids_cookie);
        foreach ($aListOfBusinessIdToCompareCookie as $key => $iBusinessId) {
            if($category = Phpfox::getService('directory')->getLastChildCategoryIdOfBusiness($iBusinessId)){
            	if($category_id == $category['category_id']){
	            	$aBusinessCompare[] = Phpfox::getService('directory')->getBusinessById($iBusinessId);
            	}
            	$aBusiness = $iBusinessId;
                if(isset($aCategory[$category['category_id']])){
                	$aCategory[$category['category_id']]['list_business'][] = $aBusiness;
                } else {
                    $aCategory[$category['category_id']] = array(
                        'data' => $category, 
                        'list_business' => array($aBusiness), 
                    );                                            
                }
            }
        }

        if(isset($aCategory[$category_id]) == false || isset($aCategory[$category_id]['list_business']) == false || count($aCategory[$category_id]['list_business']) < 2){
			$this->url()->send('directory');
			return false;
        }

        $parent_category_id = Phpfox::getService('directory.category')->getParentId($category_id);
        $aCustomFields = Phpfox::getService('directory')->getCustomFieldByCategoryId($parent_category_id);

        foreach ($aCategory as $key => $aCategoryItem) {
        	if(isset($aCategoryItem['list_business'])){
	        	$aCategory[$key]['total_business'] = count($aCategoryItem['list_business']);
        	} else {
        		$aCategory[$key]['total_business'] = 0;
        	}
        }

        $aFields =  Phpfox::getService('directory')->getFieldsComparison();
        foreach ($aBusinessCompare as $key => $value) {
        	$aPhones = Phpfox::getService('directory')->getBusinessPhone($value['business_id']);
        	$aWebsites = Phpfox::getService('directory')->getBusinessWebsite($value['business_id']);
        	$aBusinessCompare[$key]['total_phone'] = count($aPhones);
        	$aBusinessCompare[$key]['list_phone'] = ($aPhones);
        	$aBusinessCompare[$key]['total_website'] = count($aWebsites);
        	$aBusinessCompare[$key]['list_website'] = ($aWebsites);
        	$aBusinessCompare[$key]['total_follow'] = Phpfox::getService('directory')->getCountFollowerOfBusiness($value['business_id']);

            $aVistingHourInfo = Phpfox::getService('directory')->getBusinessVistingHour($value['business_id']);
            foreach ($aVistingHourInfo as $key2 => $value2) {
            	$aVistingHourInfo[$key2]['phrase'] = Phpfox::getService('directory.helper')->getPhraseById('date.dayofweek', $value2['vistinghour_dayofweek']);
            }
            $aBusinessCompare[$key]['total_visitinghour'] = count($aVistingHourInfo);
            $aBusinessCompare[$key]['list_visitinghour'] = ($aVistingHourInfo);

			$aCustomData = array();
        	$aCustomDataTemp = Phpfox::getService('directory.custom')->getCustomFieldByBusinessId($value['business_id']);
            if(count($aCustomFields)){
                foreach ($aCustomFields as $aField) {
                        foreach ($aCustomDataTemp as $aFieldValue) {
                            if($aField['field_id'] == $aFieldValue['field_id']){
                                $aCustomData[] = $aFieldValue;
                            }
                        }
                }
            }

            $aBusinessCompare[$key]['total_customdata'] = count($aCustomData);
            $aBusinessCompare[$key]['list_customdata'] = ($aCustomData);
            $aBusinessCompare[$key]['members'] = Phpfox::getService('directory')->getCountMemberOfBusiness($value['business_id']);
            if (empty($aBusinessCompare[$key]['logo_path'])) {
                $aBusinessCompare[$key]['default_logo_path'] = Phpfox::getService('directory')->getStaticPath() . 'module/directory/static/image/default_ava.png';
            }
        }

        $aFieldStatus = Phpfox::getService('directory')->doComparisonField($aFields);
		// check permission 
		$sCompareLink = Phpfox::permalink('directory.comparebusiness', null, null);
		$this->template()->assign(array(
            'aFields' => $aFields, 
			'aFieldStatus' => $aFieldStatus, 
			'aCategory' => $aCategory, 
			'aBusinessCompare' => $aBusinessCompare, 
			'category_id' => $category_id, 
			'sCompareLink' => $sCompareLink, 
			'aCustomFields' => $aCustomFields, 
		));

		$this->template()->setHeader(
            array('jquery.rating.css' => 'style_css')
            )
			->setBreadcrumb(_p('directory.module_menu_business'), $this->url()->makeUrl('directory'));

		$this->template()->setTitle(_p('directory.compare'));

		Phpfox::getService('directory.helper')->loadDirectoryJsCss();
	}
}
