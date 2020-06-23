<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_Add extends Phpfox_Component
{
    private $_iFeatureFee = 5;
	private $_iDefaultFeatureFee = 5;
	private $_iThemeId = 1;
    private $_unitCurrencyFee = null;
    private $_symbolCurrencyFee = null;
	private $_bIsEdit = false;
    private $_aCallback = false;
    private $_sModule = null;
    private $_iItem = null;
    private $_iEditedBusinessId = null;
    private $_aEditedBusiness = array();

    private function _initVariables() {
    	$aGlobalSetting =  Phpfox::getService('directory')->getGlobalSetting();
        $this->_iFeatureFee = (int)$aGlobalSetting[0]['default_feature_fee'];
        $this->_iDefaultFeatureFee = (int)$aGlobalSetting[0]['default_feature_fee'];
        $this->_iThemeId = (int)$aGlobalSetting[0]['default_theme_id'];

    	$aCurrentCurrencies = Phpfox::getService('directory.helper')->getCurrentCurrencies();
        $this->_unitCurrencyFee = $aCurrentCurrencies[0]['currency_id'];
        $this->_symbolCurrencyFee = $aCurrentCurrencies[0]['symbol'];

        $this->_bIsEdit = false;
        $this->_bCanEditPersonalData = true;
        $this->_aCallback = false;
        $this->_sModule = $this->request()->get('module', false);
        $this->_iItem = $this->request()->getInt('item', false);
        $this->_iEditedBusinessId = null;
        $this->_aEditedBusiness = array();
    }

    private function _checkIsInPageAndPagePermission() {
        if ($this->_sModule !== false && $this->_iItem !== false) {
            //https://jira.younetco.com/browse/PFBIZPAGE-606
            if (in_array($this->_sModule, array('groups', 'pages'))) {
                Phpfox::addMessage(_p('you_do_not_have_permission_for_this_action_please_contact_administrator_for_more_details'));
                $this->url()->send('directory');
            }
            if (Phpfox::hasCallback($this->_sModule, 'getItem')) {
                $aCallback = Phpfox::callback($this->_sModule . '.getItem', $this->_iItem);
                if ($aCallback === false) {
                    return Phpfox_Error::display(_p('Cannot find the parent item.'));
                }
            }
        }
    }

    private function _checkIsInEditBusiness() {
        if ($this->request()->getInt('id') && !isset($_POST['val']['add'])) {
            $this->_iEditedBusinessId = $this->request()->getInt('id');
            return true;
        } else {
            return false;
        }
    }

    private function _getValidationParams($aVals = array()) {

        $aParam = array(
        );

        return $aParam;
    }

    private function _checkIfHavingPermissionToAddOrEditBusiness() {
        if ($this->_sModule && $this->_iItem && Phpfox::hasCallback($this->_sModule, 'viewBusiness')) {
            $this->_aCallback = Phpfox::callback($this->_sModule . '.viewBusiness', $this->_iItem);
            $this->template()->setBreadcrumb($this->_aCallback['breadcrumb_title'], $this->_aCallback['breadcrumb_home']);
            $this->template()->setBreadcrumb($this->_aCallback['title'], $this->_aCallback['url_home']);
            if ($this->_sModule == 'pages' && !Phpfox::getService('pages')->hasPerm($this->_iItem, 'directory.share_business')) {
                return false;
            }
        }

        return true;
    }

    private function _checkIfSubmittingAForm() {
        if ($this->request()->getArray('val')) {
            return true;
        } else {
            return false;
        }
    }

    private function _prepareEditForm() {
    }

    private function _verifyForm($aVals){
        if ($aVals['visiting_hours_hour_starttime'] > $aVals['visiting_hours_hour_endtime']) {
            Phpfox_Error::set(_p('Visiting hours start time must be earlier visiting hours endtime'));
            return false;
        }

        if (empty($aVals['category']) || !$this->_verifyCategories($aVals['category'])) {
            Phpfox_Error::set(_p('please_select_category'));
            return false;
        }
        
        return true;
    }

    private function _verifyCategories($aCategories)
    {
        $iValid = false;
        foreach ($aCategories as $key => $aCategory) {
            if (!empty($aCategory[0])) {
                $iValid = true;
                break;
            }
        }

        return $iValid;
    }

    private function _verifyCustomForm($aVals)
    {
        return true;

    }

    private function _checkIfHavingSpamActionAndHandleIt() {
    }

    private function _setFailForms($aVals)
    { 
        if(isset($aVals['location_title'])){
            $aVals['all_location'] = array();
            foreach($aVals['location_title'] as $keylocation_title => $vallocation_title){
                $aVals['all_location'][] = array(
                    'location_title' => $aVals['location_title'][$keylocation_title], 
                    'location_fulladdress' => $aVals['location_fulladdress'][$keylocation_title], 
                    'location_address' => $aVals['location_address'][$keylocation_title], 
                    'location_address_city' => $aVals['location_address_city'][$keylocation_title], 
                    'location_address_country' => $aVals['location_address_country'][$keylocation_title], 
                    'location_address_zipcode' => $aVals['location_address_zipcode'][$keylocation_title], 
                    'location_address_lat' => $aVals['location_address_lat'][$keylocation_title], 
                    'location_address_lng' => $aVals['location_address_lng'][$keylocation_title], 
                );
            }
        }

        if(isset($aVals['visiting_hours_dayofweek_id'])){
            $aVals['all_visiting_hours'] = array();
            foreach($aVals['visiting_hours_dayofweek_id'] as $keyvisiting_hours_dayofweek_id => $valvisiting_hours_dayofweek_id){
                $aVals['all_visiting_hours'][] = array(
                    'visiting_hours_dayofweek_id' => $aVals['visiting_hours_dayofweek_id'][$keyvisiting_hours_dayofweek_id], 
                    'visiting_hours_hour_starttime' => $aVals['visiting_hours_hour_starttime'][$keyvisiting_hours_dayofweek_id], 
                    'visiting_hours_hour_endtime' => $aVals['visiting_hours_hour_endtime'][$keyvisiting_hours_dayofweek_id], 
                );
            }
        }

        if(isset($aVals['customfield_user_title'])){
            $aVals['all_customfield_user'] = array();
            foreach($aVals['customfield_user_title'] as $keycustomfield_user_title => $valcustomfield_user_title){
                $aVals['all_customfield_user'][] = array(
                    'customfield_user_title' => $aVals['customfield_user_title'][$keycustomfield_user_title], 
                    'customfield_user_content' => $aVals['customfield_user_content'][$keycustomfield_user_title], 
                );
            }
        }
        // process for category 
        if(isset($aVals['category'])){
            $aBusinessCategories = $this->getCategoriesFromForm($aVals);
            $editCategories = $aBusinessCategories; 
            $aParentCategory = array();
            $iMainCategoryId = $editCategories[(int)$aVals['maincategory']];
            $aFirstCategoryId = array();

            foreach ($editCategories as $key => $aCategory) {
                $iParent =  Phpfox::getService('directory.category')->getParentId($aCategory);
                    $aParentCategory[] = $iParent;
                    if($iParent == $aCategory ){
                        if($key != 0){
                            $aFirstCategoryId[] = $aCategory; 
                        }
                        $aVals['editCategories'][] = $aCategory;
                    }
            }

            $sParentCategory = implode(",", $aParentCategory);
            $aFirstCategoryId = implode(",", $aFirstCategoryId);
            $aEditCategoryId = implode(",", $aVals['editCategories']);
            $aVals['categories'] = implode(",", $editCategories);
            $aVals['categories'] = trim($aVals['categories'], ',');

            $this->template()->setHeader('cache', array(
                '<script type="text/javascript">;$Behavior.directoryAddCategory = function(){
                        var aCategories = explode(\',\', \'' . $aVals['categories'] . '\');
                        var aParentCategories = explode(\',\', \'' . $sParentCategory . '\');
                        var aFirstCategoryId = explode(\',\', \'' . $aFirstCategoryId . '\');
                        var aEditCategoryId = explode(\',\', \'' . $aEditCategoryId . '\');

                        for (i in aCategories) {
                             $(\' .section_category_\'+aParentCategories[i]+\' #js_mp_holder_\' + aCategories[i]).show();
                             $(\' .section_category_\'+aParentCategories[i]+\' #js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true);
                            
                             $(\'.section_category_\'+aFirstCategoryId[i]+\' #yndirectory_add\').hide();
                             $(\'.section_category_\'+aFirstCategoryId[i]+\' #yndirectory_delete\').show();
                             
                         } 
                         for(j in aEditCategoryId){
                             $(\' .section_category_\'+aEditCategoryId[j]+\' .yndirectory-categorylist-maincategory \').val(j);
                         }
                         setTimeout(function(){
                            $(\'.section_category_'.$iMainCategoryId.' #js_mp_holder_0 input.yndirectory-categorylist-maincategory\').attr(\'checked\', true);
                            $(\'.section_category_'.$iMainCategoryId.' #js_mp_holder_0 input.yndirectory-categorylist-maincategory\')[0].checked = true;
                        }, 200);
                        
                     };
                </script>'
            ));
        }

        // process for custom field 
        if(isset($aVals['custom'])){
            $aCustomFields = Phpfox::getService('directory')->getCustomFieldByCategoryId($iMainCategoryId);
            $custom = $aVals['custom'];
            foreach ($aCustomFields as $keyaCustomFields => $valueaCustomFields) {
                foreach ($custom as $keycustom => $valuecustom) {
                    if($valueaCustomFields['field_id'] == $keycustom){
                        if(is_array($valuecustom)){
                            $tmpValue = array();
                            foreach ($valuecustom as $keyvaluecustom => $valuevaluecustom) {
                                $tmpValue[$valuevaluecustom] = $valueaCustomFields['option'][$valuevaluecustom];
                            }
                            $aCustomFields[$keyaCustomFields]['value'] = $tmpValue;
                        } else {
                            $aCustomFields[$keyaCustomFields]['value'] = $valuecustom;
                        }
                    }
                }
            }
            $oAjax = Phpfox::getLib('ajax');
            Phpfox::getBlock('directory.custom.form', array(
                'aCustomFields' => $aCustomFields, 
            ));

            $sContent = $oAjax->getContent(false);
            $sContent =  stripslashes($sContent);
            $sContent = base64_encode($sContent);

            $this->template()->setHeader('cache', array(
                '<script type="text/javascript">;$Behavior.directoryAddCategoryCustomField = function(){              
                    $(\'#yndirectory_customfield_category\').html(yndirectoryhelper.base64_decode(\'' . $sContent . '\'));   
                    setTimeout(function(){yndirectory.addValidateForCustomField(); }, 2000);
                           
                 };
                </script>'
            ));
        }

        $this->template()->assign(array(
            // 'aFields' => $aFields,
            'aForms' => $aVals,
            'bFail' => 1
        ));
    }    

    public function getCategoriesFromForm($aVals)
    {
        $aBusinessCategories = array();
        if (isset($aVals['category']) && count($aVals['category']))
        {
            if(empty($aVals['category'][0]))
            {
                return false;
            }
            else if(!is_array($aVals['category']))
            {
                $aBusinessCategories[] = $aVals['category'];
            }
            else{
                foreach ($aVals['category'] as $aCategory)
                {

                    foreach ($aCategory as $iCategory){
                        if (empty($iCategory))
                        {
                            continue;
                        }

                        if (!is_numeric($iCategory))
                        {
                            continue;
                        }

                        $aBusinessCategories[] = $iCategory;
                    }
                }
            }
        }
        return $aBusinessCategories;
    }    

    private function payPackage($aVals, $iId){
        if (isset($aVals['create']) && $aVals['create']){
            if((int)$aVals['package_id'] > 0){
                $package_id = $aVals['package_id'];
                $aPackage = Phpfox::getService('directory.package')->getById($package_id);
                if(isset($aPackage['package_id']) == false || (int)$aPackage['active'] != 1){
                    // do nothing
                } else {
                    $currency_id = $this->_unitCurrencyFee;
                    $packageFee = doubleval( $aPackage['fee']);
                    $featureFee = doubleval(($aVals['feature_number_days'] * $this->_iDefaultFeatureFee));
                    $fFee =  $packageFee + $featureFee;
                    if($fFee > 0){
                        // add invoice 
                        $iInvoice = Phpfox::getService('directory.process')->addInvoice($iId,$currency_id, $fFee, 'business', array(
                            'pay_type' => ( Phpfox::getUserParam('directory.can_feature_business')?($featureFee > 0 ? 'feature' : ''):'' ). '|' .($packageFee > 0 ? 'package' : ''), 
                            'aPackage' => $aPackage, 
                            'feature_days' => $aVals['feature_number_days']
                        ));
                        $aPurchase = Phpfox::getService('directory')->getInvoice($iInvoice);

                        // process payment 
                        if (empty($iInvoice['status'])){
                            $this->setParam('gateway_data', array(
                                    'item_number' => 'directory|' . $aPurchase['invoice_id'],
                                    'currency_code' => $aPurchase['default_currency_id'],
                                    'amount' => $aPurchase['default_cost'],
                                    'item_name' => ($packageFee > 0 ? 'package' : '') . '|' . ($featureFee > 0 ? 'feature' : ''),
                                    'return' => Phpfox::permalink('directory.detail', $iId, $aVals['name'], false, '') . 'businesspayment_done/',
                                    'recurring' => '',
                                    'recurring_cost' => '',
                                    'alternative_cost' => '',
                                    'alternative_recurring_cost' => ''                      
                                )
                            );
                            if(Phpfox::getService('directory.helper')->getUserParam('directory.business_created_by_user_automatically_approved', Phpfox::getUserId())){
                                $status = Phpfox::getService('directory.helper')->getConst('business.status.running');
                            } else {
                                $status = Phpfox::getService('directory.helper')->getConst('business.status.pending');
                            }
                            Phpfox::getService('directory.process')->updateBusinessStatus($iId, $status);

                            return $aPurchase['invoice_id'];

                        }
                    } else {
                        // pay zero fee - package
                        $status = Phpfox::getService('directory.helper')->getConst('business.status.draft');
                        if(Phpfox::getService('directory.helper')->getUserParam('directory.business_created_by_user_automatically_approved', Phpfox::getUserId())){
                            $status = Phpfox::getService('directory.helper')->getConst('business.status.approved');
                        } else {
                            $status = Phpfox::getService('directory.helper')->getConst('business.status.pending');
                        }
                        Phpfox::getService('directory.process')->updateBusinessStatus($iId, $status);

                        if($status == Phpfox::getService('directory.helper')->getConst('business.status.approved')){
                            // call approve function 
                            Phpfox::getService('directory.process')->approveBusiness($iId, null);                                    
                        }
                        $theme = isset($aPackage['themes'][0])?$aPackage['themes'][0]:1;
                        Phpfox::getService('directory.process')->updateThemeForBusiness(array('theme' => $theme['theme_id'], 'business_id' => $iId));
                        // pay zero fee - feature
                        if((int)$aVals['feature_number_days'] > 0){
                            $start_time = PHPFOX_TIME;
                            $end_time = $start_time + ((int)$aVals['feature_number_days'] * 86400); 
                            Phpfox::getService('directory.process')->updateBusinessFeatureTime($iId, $start_time, $end_time);
                        }
                        return true;
                    }
                }
            }
        }

        return false;
    }

	public function process()
	{
		Phpfox::getService('directory.helper')->buildMenu();
		// check permission 
        $bCanCreateBusiness = false; 
        if(Phpfox::getService('directory.permission')->canCreateBusiness()){
            $bCanCreateBusiness = true; 
        }
        $bCanCreateBusinessForClaiming = false; 
        if(Phpfox::getService('directory.permission')->canCreateBusinessForClaiming()){
            $bCanCreateBusinessForClaiming = true; 
        }
        if($bCanCreateBusiness == false && $bCanCreateBusinessForClaiming == false){
            return Phpfox::getService('directory.permission')->canCreateBusiness(true); 
        }
		if(Phpfox::getService('directory.permission')->canCreateBusinessWithLimit() == false){
			return Phpfox_Error::display(_p('directory.you_have_reached_your_creating_limit_please_contact_administrator'));
		}
		// init 
        $this->_initVariables();
        $this->_checkIsInPageAndPagePermission();
        $sParams = '';
        if ($this->_checkIsInEditBusiness()) {
        	// prepare for editing 
            $this->_prepareEditForm();

            // if(!$this->_checkEditPermission())
            // {
            //     $this->url()->send('subscribe');
            // }

            // $aFields = Phpfox::getService('coupon.custom')->getByCouponId($this->_iEditedCouponId);
        } else {
        	// prepare for adding 
			$type = $this->request()->get('type', false);
			$package_id = $this->request()->get('package', false);
			
			if(($type === false || in_array($type, array('business', 'claiming')) == false)
				)
			{
				Phpfox::getLib('url')->send($this->url()->makeUrl('directory.businesstype'));
			}
            $sParams .= 'type_' . $type . '/';

			$aPackage = null;
			if($type == 'business'){
                if($bCanCreateBusiness == false){
                    return Phpfox::getService('directory.permission')->canCreateBusiness(true); 
                }
				if(($package_id === false || (int)$package_id <= 0)){
					Phpfox::getLib('url')->send($this->url()->makeUrl('directory.businesstype'));
				}

				$aPackage = Phpfox::getService('directory.package')->getById($package_id);
				if(isset($aPackage['package_id']) == false || (int)$aPackage['active'] != 1){
					Phpfox::getLib('url')->send($this->url()->makeUrl('directory.businesstype'));
				}
                $sParams .= 'package_' . $package_id . '/';
			} else {
                if($bCanCreateBusinessForClaiming == false){
                    return Phpfox::getService('directory.permission')->canCreateBusinessForClaiming(true); 
                }                
            }
            $this->template()->assign(array(
                'type' => $type,
                'package_id' => $package_id,
            ));
        }

        if (!$this->_checkIfHavingPermissionToAddOrEditBusiness()) {
            return Phpfox_Error::display(_p('directory.unable_to_view_this_item_due_to_privacy_settings'));
        }

        $aValidationParam = $this->_getValidationParams();
        $oValid = Phpfox::getLib('validator')->set(array(
                'sFormName' => 'yndirectory_edit_directory_form',
                'aParams' => $aValidationParam
            )
        );

		// process 
		if ($this->_checkIfSubmittingAForm()) {
            if(isset($aVals['back'])){
                Phpfox::getLib('url')->send($this->url()->makeUrl('directory.businesstype'));
            }

            $aVals = $this->request()->getArray('val');
            $aVals['type'] = $type;
            $aVals['package_id'] = $package_id;


            $aValidationParam = $this->_getValidationParams($aVals);
            $oValid = Phpfox::getLib('validator')->set(array(
                    'sFormName' => 'yndirectory_edit_directory_form',
                    'aParams' => $aValidationParam
                )
            );

            if ($this->_verifyForm($aVals) && $this->_verifyCustomForm($aVals) && $oValid->isValid($aVals)){
                $this->_checkIfHavingSpamActionAndHandleIt();

                $oValidator = Phpfox_Validator::instance();
                $oValidator->verify('email', $aVals['email']);
                $sRegex = '/^(?:(ftp|http|https):)?(?:\/\/(?:((?:%[0-9a-f]{2}|[\-a-z0-9_.!~*\'\(\);:&=\+\$,])+)@)?(?:((?:[a-z0-9](?:[\-a-z0-9]*[a-z0-9])?\.)*[a-z](?:[\-a-z0-9]*[a-z0-9])?)|([0-9]{1,3}(?:\.[0-9]{1,3}){3}))(?::([0-9]*))?)?((?:\/(?:%[0-9a-f]{2}|[\-a-z0-9_.!~*\'\(\):@&=\+\$,;])+)+)?\/?(?:\?.*)?$/i';
                $bCheck = true;
                foreach ($aVals['web_address'] as $iKey => $sUrl) {
                    if (!empty($sUrl)) {
                        $params = explode('.', $sUrl);
                        if ((sizeof($params === 3) AND $params[0] == 'www' && substr($sUrl, 0,
                                    3) == 'www') || $this->verify($sRegex, $sUrl)) {
                            $bCheck = true;
                        } else {
                            Phpfox_Error::set(_p('invalid_url'));
                            $bCheck = false;
                        }
                    }
                }
                if (Phpfox_Error::isPassed() && $bCheck){
                    if ($this->_sModule && $this->_iItem && !$this->_bIsEdit)
                    {
                        $aVals['module_id'] = $this->_sModule;
                        $aVals['item_id'] = $this->_iItem;
                    }
                    $aSendParam = array();
                    if ($this->_sModule){                        
                        $aSendParam['module'] = $this->_sModule;
                    }
                    if ($this->_iItem){
                        $aSendParam['item'] = $this->_iItem;
                    }
                    if ($this->_bIsEdit)
                    {
                    }
                    elseif ($iId = Phpfox::getService('directory.process')->addBusiness($aVals))
                    {
                        if((int)$aVals['package_id'] > 0){
                            // create business type 
                            $invoice_id = $this->payPackage($aVals, $iId);
                            if($invoice_id === true){
                                // create business withou fee
                                $this->url()->permalink('directory.detail', $iId, $aVals['name'], true, _p('directory.business_successfully_added'));
                            } else if((int)$invoice_id > 0){
                                $this->template()->assign(array(
                                    'invoice_id' => $invoice_id, 
                                ));
                            }else if(isset($aVals['draft'])){
                                $this->url()->permalink('directory.detail', $iId, $aVals['name'], true, "");
                            } else {
                                Phpfox::getLib('url')->send('directory.businesstype', null, _p('directory.some_issues_happen_please_try_again_thanks'));
                            }
                        } else {
                            // create claiming type
                            if(isset($aVals['draft'])){
                                $this->url()->permalink('directory.detail', $iId, $aVals['name'], true, "");
                            }
                            else{
                            $this->url()->permalink('directory.detail', $iId, $aVals['name'], true, _p('directory.business_successfully_added'));
                        }}
                    }
                    else
                    {
                        $aFields   =   $this->_setFailForms($aVals);
                    }

                }
            }
            elseif (!$this->_bIsEdit)
            {
                $aFields = $this->_setFailForms($aVals);
            }
		}
        if ($this->_sModule && $this->_iItem && !$this->_bIsEdit)
        {
            $sParams .='/module_'.$this->_sModule.'/item_'.$this->_iItem;
        }
        if(!isset($invoice_id)){
            $aGlobalSetting =  Phpfox::getService('directory')->getGlobalSetting();
            $aBusinessSizes =  Phpfox::getService('directory.helper')->getBusinessSize();
            $aVisitingHours =  Phpfox::getService('directory.helper')->getVisitingHours();
            $aVisitingHours = Phpfox::getService('directory.helper')->getHoursFormat($aVisitingHours);

            // end 
            $this->template()->assign(array(
                'sBackUrl' => $this->url()->makeUrl('directory.businesstype'), 
                'sFormUrl' => $this->url()->makeUrl('directory.add') . $sParams, 
                'sParams' => $sParams, 
                'aPackage' => $aPackage, 
                'aGlobalSetting' => $aGlobalSetting[0], 
                'sCreateJs' => $oValid->createJS(),
                'sGetJsForm' => $oValid->getJsForm(),
                'sModule' => ($this->_aCallback !== false ? $this->_sModule : ''),
                'iItem' => ($this->_aCallback !== false ? $this->_iItem : ''),
                'bIsEdit' => $this->_bIsEdit,
                'bCanEditPersonalData' => $this->_bCanEditPersonalData,
                'aBusinessSizes' => $aBusinessSizes,
                'aVisitingHours' => $aVisitingHours,
                'aTimeZones' => Phpfox::getService('core')->getTimeZones(),
                'sCategories' => Phpfox::getService('directory.category')->get(),
                'aCurrentCurrencies' => Phpfox::getService('directory.helper')->getCurrentCurrencies(),
                'iDefaultFeatureFee' => $this->_iDefaultFeatureFee,
                'core_path' => Phpfox::getParam('core.path'),
                'max_upload_size_photos' => Phpfox::getParam('directory.max_upload_size_photos'),
                'apiKey' => Phpfox::getParam('core.google_api_key'),
                'corepath' => phpfox::getParam('core.path'),
            ));

            $this->template()
                ->setBreadcrumb(_p('directory.module_menu_business'), $this->url()->makeUrl('directory'))
                ->setBreadcrumb( _p('directory.create_new_business'), $this->url()->makeUrl('directory.businesstype'))
                ->setBreadcrumb(_p('directory.create_new_business'), $this->url()->makeUrl('directory.businesstype'), true)
                ->setEditor(array('wysiwyg' => true))
                ->setPhrase(array(
                ))
                ->setHeader('cache', array(
                    'pager.css' => 'style_css',
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'switch_legend.js' => 'static_script',
                    'switch_menu.js' => 'static_script',
                    'quick_edit.js' => 'static_script',
                    'progress.js' => 'static_script',
                    'share.js' => 'module_attachment',
                    'country.js' => 'module_core',
                    'magnific-popup.css'       => 'module_directory',
                    'jquery.magnific-popup.js'  => 'module_directory',
                ))
                ; 

            if (Phpfox::isModule('attachment')) {
               $this->setParam('attachment_share', array(
						'type' => 'directory',
						'id' => 'yndirectory_edit_directory_form',
						'edit_id' => ($this->_bIsEdit ? $this->_iEditedBusinessId : 0),
					)
				);
            }

        } else {
            // confirm payment 
            $this->template()->setTitle(_p('directory.review_and_confirm_purchase'))
                ->setBreadcrumb(_p('directory.module_menu_business'), $this->url()->makeUrl('directory'))
                ->setBreadcrumb(_p('directory.review_and_confirm_purchase'), null, true)
                ;
        }

        $this->template()->setTitle(_p('directory.create_new_business'));
        Phpfox::getService('directory.helper')->loadDirectoryJsCss();
	}

    private function verify($sPattern, $sValue)
    {
        if (!preg_match($sPattern, $sValue)) {
            return false;
        } else {
            return true;
        }
    }

    public function clean()
    {
        $this->template()->clean(array(
                'bIsEdit',
            )
        );
    }

}
?>