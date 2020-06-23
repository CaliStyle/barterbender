<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_Edit extends Phpfox_Component
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
        $this->_iPackageId = null;
        $this->_aPackageBusiness = array();
    }

    private function _checkIsInPageAndPagePermission() {
        if ($this->_sModule !== false && $this->_iItem !== false && $this->_sModule != 'directory') {
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
            $this->_bIsEdit = true;            
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
            $aEditedBusiness = Phpfox::getService('directory')->getBusinessForEdit($this->_iEditedBusinessId);
            $this->_aEditedBusiness = $aEditedBusiness;
            $this->_aPackageBusiness = json_decode($aEditedBusiness['package_data'],1);
            $type = $aEditedBusiness['type'];
            $this->_iPackageId = $this->_aPackageBusiness['package_id'];
            $editCategories = explode(",", $aEditedBusiness['categories']);
            $aParentCategory = array();
            $iMainCategoryId = Phpfox::getService('directory.category')->checkMainCategory($this->_iEditedBusinessId);
            $aFirstCategoryId = array();

            foreach ($editCategories as $key => $aCategory) {
                $iParent =  Phpfox::getService('directory.category')->getParentId($aCategory);
                    $aParentCategory[] = $iParent;
                    if($iParent == $aCategory ){
                        if($key != 0){
                            $aFirstCategoryId[] = $aCategory; 
                        }
                        $aEditedBusiness['editCategories'][] = $aCategory;
                    }
            }

            $aEditCategories = Phpfox::getService('directory.category')->getForBrowseByBusinessId($this->_iEditedBusinessId);
            $sEditCategories = json_encode($aEditCategories);
            $this->template()->setHeader('cache', array(
                '<script type="text/javascript">
                    $Behavior.directoryEditCategory = function() {
                        var aCategories = JSON.parse(\'' . $sEditCategories . '\');
                        var categorySection;

                        for (var i = 0; i < aCategories.length; i++) {
                            categorySection = $(\'.section_category_\' + aCategories[i].category_id).get(0);
                            $(categorySection).find(\'#js_mp_category_item_\' + aCategories[i].category_id).attr(\'selected\', true);
                            $(categorySection).find(\'#js_mp_holder_\' + aCategories[i].category_id).show();

                            if (i > 0) {
                                $(categorySection).find(\'#yndirectory_add\').hide();
                                $(categorySection).find(\'#yndirectory_delete\').show();
                            }

                            $(categorySection).find(\'.yndirectory-categorylist-maincategory\').val(i);

                            if (aCategories[i].is_main == 1) {
                                $(categorySection).find(\'.yndirectory-categorylist-maincategory\').attr(\'checked\', true);
                            }

                            if (aCategories[i].sub.length) {
                                $(categorySection).find(\'#js_mp_category_item_\' + aCategories[i].sub[0].category_id).attr(\'selected\', true);
                            }
                        }

                        yndirectory.changeCustomFieldByMainCategory('.$iMainCategoryId.');
                    }
                </script>'
            ));


            if (Phpfox::isModule('tag'))
            {

                $aEditedBusiness['tag_list'] = '';                    

                $aTags = Phpfox::getService('tag')->getTagsById('business', $this->_iEditedBusinessId);

                if (isset($aTags[$this->_iEditedBusinessId]))
                {
                    foreach ($aTags[$this->_iEditedBusinessId] as $aTag)
                    {
                        $aEditedBusiness['tag_list'] .= ' ' . $aTag['tag_text'] . ',';    
                    }
                    $aEditedBusiness['tag_list'] = trim(trim($aEditedBusiness['tag_list'], ','));
                }
            }

            /*get featured or not*/
            if($aEditedBusiness['feature_start_time'] <= PHPFOX_TIME &&  $aEditedBusiness['feature_end_time'] >= PHPFOX_TIME){
                $aEditedBusiness['featured'] = true;
            }
            else{
                $aEditedBusiness['featured'] = false;
            }


                if(4294967295 == $aEditedBusiness['feature_end_time'])
                {
                    $aEditedBusiness['is_unlimited'] = 1;   
                    $aEditedBusiness['expired_date'] = '';
                }
                else
                if($aEditedBusiness['feature_start_time'] <= PHPFOX_TIME && $aEditedBusiness['feature_end_time'] >= PHPFOX_TIME)
                {
                    $aEditedBusiness['is_unlimited'] = 0;
                    $aEditedBusiness['expired_date'] = Phpfox::getService('directory.helper')->convertTime($aEditedBusiness['feature_end_time']);   
                }

            $isClaimingDraft = ($aEditedBusiness['type'] == 'claiming' && Phpfox::getService('directory.helper')->getConst('business.status.claimingdraft') == $aEditedBusiness['business_status']) ? '1' : '0';
            $this->template()->assign(array(
                'type' => $type,
                'package_id' => $this->_iPackageId,
                'aForms' => $aEditedBusiness,
                'aEditedBusiness' => $aEditedBusiness,
                'iBusinessid' => $this->_iEditedBusinessId,
                'isClaimingDraft' => $isClaimingDraft,
                'corepath' => phpfox::getParam('core.path'),
            ));


    }

    private function _verifyForm($aVals){

        // if(strlen(trim($aVals['name'])) == 0){
        //     return Phpfox_Error::set(_p('directory.please_fill_name_field'));
        // }
        // if(strlen(trim($aVals['short_description'])) == 0){
        //     return Phpfox_Error::set(_p('directory.please_fill_short_description_field'));
        // }

        // $list = Phpfox::getService('directory.helper')->removeEmptyElement((array)$aVals['location_fulladdress']);
        // if(count($list) == 0 || Phpfox::getService('directory.helper')->checkEmptyInputArrayField($list) == true){
        //     return Phpfox_Error::set(_p('directory.please_fill_location_field'));
        // }
        // $list = Phpfox::getService('directory.helper')->removeEmptyElement((array)$aVals['phone']);
        // if(count($list) == 0 || Phpfox::getService('directory.helper')->checkEmptyInputArrayField($list) == true){
        //     return Phpfox_Error::set(_p('directory.please_fill_phone_field'));
        // }

        // if(Phpfox::getLib('mail')->checkEmail($aVals['email'])){
        //     return Phpfox_Error::set(_p('directory.please_input_valid_email'));
        // }

        // $list = Phpfox::getService('directory.helper')->removeEmptyElement((array)$aVals['category'][0]);
        // if(count($list) == 0 || Phpfox::getService('directory.helper')->checkEmptyInputArrayField($list) == true){
        //     return Phpfox_Error::set(_p('directory.please_select_category'));
        // }
        if ($aVals['visiting_hours_hour_starttime'] > $aVals['visiting_hours_hour_endtime']) {
            return Phpfox_Error::set(_p('Visiting hours start time must be earlier visiting hours endtime'));
        }
        return true;
    }

    private function _verifyCustomForm($aVals)
    {
        return true;
    }

    private function _checkIfHavingSpamActionAndHandleIt() {
    }

    private function _setFailForms($aVals)
    { 
        // $aFields =Phpfox::getService('coupon.custom')->getCustomField();    

        // if(isset($aVals['custom'])) {
        //     foreach ($aVals['custom'] as $key_custom => $custom_value) {
        //         foreach ($aFields as &$aField) {
        //             if($aField['field_id'] == $key_custom){
        //                 if(is_array($custom_value)) {
        //                     foreach ($custom_value as $key => $value) {
        //                         $aField['value'][$value] = $value;
        //                     }
        //                 }
        //                 else{
        //                     $aField['value'] = $custom_value;
        //                 }
        //             }
        //         }
        //     }
        // }
        // if (isset($aVals['feature_coupon']))
        // {
        //     $this->_iTotalFee = $this->_iPublishFee + $this->_iFeatureFee;
        // }
        // $this->template()->assign(array(
        //     'aFields' => $aFields,
        //     'aForms' => $aVals,
        //     'bFail' => 1
        // ));
        // $this->template()->setHeader('cache', array(
        //     '<script type="text/javascript">$Behavior.funraisingEditCategory = function(){var aCategories = explode(\',\', \'' . implode(',', $aVals['category']) . '\'); for (i in aCategories) { $(\'#js_mp_holder_\' + aCategories[i]).show(); $(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true); }}</script>'
        // ));

        // return $aFields;
    }    


    private function payFeature($aVals, $iId, $aBusiness = null){
        if(null == $aBusiness){
            $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($this->_iEditedBusinessId);
        }

        $currency_id = $this->_unitCurrencyFee;
        $featureFee = doubleval(((isset($aVals['feature_number_days'])?$aVals['feature_number_days']:0) * $this->_iDefaultFeatureFee));
        $fFee =   $featureFee;

        if($fFee > 0){
            // add invoice 
            $iInvoice = Phpfox::getService('directory.process')->addInvoice($iId, $currency_id, $fFee, 'feature', array(
                'aPackage' => array(), 
                'pay_type' => (Phpfox::getUserParam('directory.can_feature_business')?($featureFee > 0 ? 'feature' : ''):''), 
                'feature_days' => isset($aVals['feature_number_days']) ? $aVals['feature_number_days'] : 0
            ));
            $aPurchase = Phpfox::getService('directory')->getInvoice($iInvoice);

            // process payment 
            if (empty($iInvoice['status'])){
                $this->setParam('gateway_data', array(
                        'item_number' => 'directory|' . $aPurchase['invoice_id'],
                        'currency_code' => $aPurchase['default_currency_id'],
                        'amount' => $aPurchase['default_cost'],
                        'item_name' => 'feature',
                        // 'return' => $this->url()->makeUrl('directory.detail', array('id' => 'done', 'payment' => 'done')),
                        'return' => Phpfox::permalink('directory.detail', $iId, $aVals['name'], false, '') . 'businesspayment_done/',
                        'recurring' => '',
                        'recurring_cost' => '',
                        'alternative_cost' => '',
                        'alternative_recurring_cost' => ''                      
                    )
                ); 
                return $aPurchase['invoice_id'];                           
            }
        } else {
            // pay zero fee - feature
            if(isset($aVals['feature_number_days']) && (int)$aVals['feature_number_days'] > 0){
                $feature_days = (int)$aVals['feature_number_days'];
                /*already approved*/
                if(PHPFOX_TIME < $aBusiness['feature_end_time']){   //still in feature,wanna expend featured time.                              
                
                    $start_time = $aBusiness['feature_start_time'];
                    $end_time =   $aBusiness['feature_end_time'] + (int)$feature_days*86400 ;
                    $feature_days = (int)$feature_days + (int)$aBusiness['feature_day'];    
                }
                else{

                    $start_time = PHPFOX_TIME; 
                    $end_time =   $start_time + $feature_days*86400;
                }

                if($end_time >= 4294967295){
                    $end_time = 4294967295;
                }
            
                Phpfox::getService('directory.process')->updateBusinessFeatureTime($this->_iEditedBusinessId, $start_time, $end_time, $feature_days);
            }
        }
    } 

    public function process()
    {
        Phpfox::getService('directory.helper')->buildMenu();
        // init
        $this->_initVariables();

        if ($this->_checkIsInEditBusiness()) {
            // prepare for editing 
            $this->_prepareEditForm();
        }

        if(!(int)$this->_iEditedBusinessId){
                   $this->url()->send('directory');
        }
        // check permission 
        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($this->_iEditedBusinessId);
        $this->_sModule = $aBusiness['module_id'];
        $this->_iItem = $aBusiness['item_id'];
        $this->_checkIsInPageAndPagePermission();
        if(!Phpfox::getService('directory.permission')->canEditBusiness($aBusiness['user_id'],$this->_iEditedBusinessId)
          ){
                $this->url()->send('subscribe');
          }

        if (!$this->_checkIfHavingPermissionToAddOrEditBusiness()) {
            return Phpfox_Error::display(_p('directory.unable_to_view_this_item_due_to_privacy_settings'));
        }

        $this->setParam(array(
                'country_child_value' => $aBusiness['country_iso'],
                'country_child_id' => $aBusiness['country_child_id']
            )
        );

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

            if(isset($aVals['featureinbox'])){
                $sUrlBack = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->url()->makeUrl('directory');
                $invoice_id = $this->payFeature($aVals, $this->_iEditedBusinessId, $aBusiness);
                if((int)$invoice_id > 0){
                    $this->template()->assign(array(
                        'invoice_id' => $invoice_id, 
                    ));                            
                } 
                else{

                    $this->url()->send($sUrlBack);
                }
            } else {
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
                            if (!Phpfox::getService('directory.process')->updateBusiness($this->_iEditedBusinessId,$aVals))
                            {
                                $this->template()->assign('bFail', 1);
                            }
                                if($aVals['isClaimingDraft'] == 0) {
                                    $invoice_id = $this->payFeature($aVals, $this->_iEditedBusinessId, $aBusiness);
                                }
                                else{
                                    $invoice_id = 0;
                                }
                                if((int)$invoice_id > 0){
                                    $this->template()->assign(array(
                                        'invoice_id' => $invoice_id, 
                                    ));                            
                                } 
                                else{
                                    $this->url()->permalink('directory.detail', $this->_iEditedBusinessId, $aVals['name'], true, _p('directory.business_successfully_updated'));
                                }

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
        }

        if(!isset($invoice_id)){
            $aGlobalSetting =  Phpfox::getService('directory')->getGlobalSetting();
            $aBusinessSizes =  Phpfox::getService('directory.helper')->getBusinessSize();
            $aVisitingHours =  Phpfox::getService('directory.helper')->getVisitingHours();
            $aVisitingHours = Phpfox::getService('directory.helper')->getHoursFormat($aVisitingHours);
            // end 
            $this->template()->assign(array(
                'sBackUrl' => $this->url()->makeUrl('directory').'detail/' . $this->_iEditedBusinessId,
                'sFormUrl' => $this->url()->makeUrl('directory.edit') .'id_'.$this->_iEditedBusinessId, 
                'aPackage' => $this->_aPackageBusiness, 
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
                'apiKey' => Phpfox::getParam('core.google_api_key'),

            ));
        $this->template()
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

                ));
	        $this->template()->setBreadcrumb(_p('directory.edit_information'), $this->url()->permalink('directory.edit','id_'.$this->_iEditedBusinessId));

            if (Phpfox::isModule('attachment')) 
            {
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

        $this->template()->setTitle(_p('directory.edit_information'));
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