<?php

defined('PHPFOX') or exit('NO DICE!');

class Coupon_Component_Controller_Add extends Phpfox_Component {

    private $_iPublishFee = 10;
    private $_iFeatureFee = 5;
    private $_iTotalFee = 10;
    private $_unitCurrencyFee = null;
    private $_symbolCurrencyFee = null;
    private $_bIsEdit = false;
    private $_bCanEditPersonalData = true;
    private $_aCallback = false;
    private $_sModule = null;
    private $_iItem = null;
    private $_iEditedCouponId = null;
    private $_aEditedCoupon = array();

    private function _initVariables() {
        $this->_iPublishFee = (int)Phpfox::getUserParam('coupon.how_much_user_publish_coupon');
        $this->_iFeatureFee = (int)Phpfox::getUserParam('coupon.how_much_user_feature_coupon');

        $this->_unitCurrencyFee = Phpfox::getService('coupon.helper')->getDefaultCurrency();
        $this->_symbolCurrencyFee = Phpfox::getService('coupon.helper')->getCurrencySymbol($this->_unitCurrencyFee);

        $this->_iTotalFee = $this->_iPublishFee;  
        $this->_bIsEdit = false;
        $this->_bCanEditPersonalData = true;
        $this->_aCallback = false;
        $this->_sModule = $this->request()->get('module', false);
        $this->_iItem = $this->request()->getInt('item', false);
        $this->_iEditedCouponId = null;
        $this->_aEditedCoupon = array();
    }

    private function _checkIsInPageAndPagePermission() {
        if ($this->_sModule !== false && $this->_iItem !== false) {
            /*
             * @todo: implement below callback later
             */
             switch ($this->_sModule) {
                 case 'pages':
                     $this->_aCallback = Phpfox::callback('coupon.getCouponsDetails', array('item_id' => $this->_iItem));
                     break;
                 case 'groups':
                     $this->_aCallback = Phpfox::callback('coupon.getCouponsGroupDetails', array('item_id' => $this->_iItem));
                     break;

                 default:
                     $this->_aCallback = Phpfox::callback($this->_sModule . '.getCouponsDetails', array('item_id' => $this->_iItem));                     
                     break;
             }
                                  
            if ($this->_aCallback) {
                $this->template()->setBreadcrumb($this->_aCallback['breadcrumb_title'], $this->_aCallback['breadcrumb_home']);
                $this->template()->setBreadcrumb($this->_aCallback['title'], $this->_aCallback['url_home']);
                if ($this->_sModule == 'pages' && !Phpfox::getService('pages')->hasPerm($this->_iItem, 'coupon.share_campaigns')) {
                    return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
                }
            }
        }
    }

    private function _checkIsInEditCampaign() {
        if ($this->request()->getInt('id') && !isset($_POST['val']['add'])) {
            $this->_iEditedCouponId = $this->request()->getInt('id');
            return true;
        } else {
            return false;
        }
    }

    /**
     * by : datlv
     * @return bool
     */
    private function _checkIfHavingPermissionToAddOrEditCampaign() {
        if ($this->_sModule && $this->_iItem && Phpfox::hasCallback($this->_sModule, 'viewCoupon')) {
            $this->_aCallback = Phpfox::callback($this->_sModule . '.viewCoupon', $this->_iItem);
            $this->template()->setBreadcrumb($this->_aCallback['breadcrumb_title'], $this->_aCallback['breadcrumb_home']);
            $this->template()->setBreadcrumb($this->_aCallback['title'], $this->_aCallback['url_home']);
            if ($this->_sModule == 'pages' && !Phpfox::getService('pages')->hasPerm($this->_iItem, 'coupon.share_campaigns')) {
                return false;
            }
        }

        return true;
    }

    /**
     * check for spam
     * @by : datlv
     */
    private function _checkIfHavingSpamActionAndHandleIt() {
        if (($iFlood = Phpfox::getUserParam('coupon.flood_control_coupon')) !== 0 && !$this->_bIsEdit) {
            $aFlood = array(
                'action' => 'last_post', // The SPAM action
                'params' => array(
                    'field' => 'time_stamp', // The time stamp field
                    'table' => Phpfox::getT('coupon'), // Database table we plan to check
                    'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
                    'time_stamp' => $iFlood * 60 // Seconds);
                )
            );

            // actually check if flooding
            if (Phpfox::getLib('spam')->check($aFlood)) {
                Phpfox_Error::set(_p('your_are_posting_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
            }
        }
    }

    /**
     * this function will get and prepare neccesary data for edit form
     * @by datlv
     */
    private function _prepareEditForm() {
        $oCouponService = Phpfox::getService('coupon')->callback($this->_aCallback);
        if ($this->_aEditedCoupon = $oCouponService->getCouponForEdit($this->_iEditedCouponId)) {

            if ($this->_aEditedCoupon['module_id'] != 'coupon') {
                $this->_sModule = $this->_aEditedCoupon['module_id'];
                $this->_iItem = $this->_aEditedCoupon['item_id'];
            }

            if(!Phpfox::getUserParam('coupon.can_edit_own_coupon') || (Phpfox::isAdmin() && !Phpfox::getUserParam('coupon.can_edit_other_user_coupon')))
            {
                $this->url()->send('coupon');
            }

            if (Phpfox::getUserId() != $this->_aEditedCoupon['user_id']) {
                $this->_bCanEditPersonalData = false;
            }

            $this->_bIsEdit = true;
            if (!empty($this->_aEditedCoupon['image_path'])) {
                $this->_aEditedCoupon['current_image'] = Phpfox::getLib('image.helper')->display(
                    array(
                        'server_id' => $this->_aEditedCoupon['server_id'],
                        'path' => 'core.url_pic',
                        'file' => $this->_aEditedCoupon['image_path'],
                        'suffix' => '_200',
                        'return_url' => true
                    )
                );
            }

            if($this->_aEditedCoupon['start_time'])
            {
                $this->_aEditedCoupon['start_time_month'] = Phpfox::getTime('n', $this->_aEditedCoupon['start_time'],false);
                $this->_aEditedCoupon['start_time_day'] = Phpfox::getTime('j', $this->_aEditedCoupon['start_time'],false);
                $this->_aEditedCoupon['start_time_year'] = Phpfox::getTime('Y', $this->_aEditedCoupon['start_time'],false);
            }
            else
            {
                $this->_aEditedCoupon['start_time_month'] = Phpfox::getTime('n', PHPFOX_TIME,false);
                $this->_aEditedCoupon['start_time_day'] = Phpfox::getTime('j', PHPFOX_TIME,false);
                $this->_aEditedCoupon['start_time_year'] = Phpfox::getTime('Y', PHPFOX_TIME,false);
            }

            if($this->_aEditedCoupon['end_time'])
            {
                $this->_aEditedCoupon['end_time_month'] = Phpfox::getTime('n', $this->_aEditedCoupon['end_time'],false);
                $this->_aEditedCoupon['end_time_day'] = Phpfox::getTime('j', $this->_aEditedCoupon['end_time'],false);
                $this->_aEditedCoupon['end_time_year'] = Phpfox::getTime('Y', $this->_aEditedCoupon['end_time'],false);
            }
            else
            {
                $this->_aEditedCoupon['end_time_month'] = Phpfox::getTime('n', PHPFOX_TIME, false);
                $this->_aEditedCoupon['end_time_day'] = Phpfox::getTime('j', PHPFOX_TIME ,false);
                $this->_aEditedCoupon['end_time_year'] = Phpfox::getTime('Y', PHPFOX_TIME ,false);
            }

            if($this->_aEditedCoupon['expire_time'])
            {
                $this->_aEditedCoupon['expire_time_month'] = Phpfox::getTime('n', $this->_aEditedCoupon['expire_time'],false);
                $this->_aEditedCoupon['expire_time_day'] = Phpfox::getTime('j', $this->_aEditedCoupon['expire_time'],false);
                $this->_aEditedCoupon['expire_time_year'] = Phpfox::getTime('Y', $this->_aEditedCoupon['expire_time'],false);
            }
            else
            {
                $this->_aEditedCoupon['expire_time_month'] = Phpfox::getTime('n', PHPFOX_TIME,false);
                $this->_aEditedCoupon['expire_time_day'] = Phpfox::getTime('j', PHPFOX_TIME,false);
                $this->_aEditedCoupon['expire_time_year'] = Phpfox::getTime('Y', PHPFOX_TIME,false);
                $this->_aEditedCoupon['unlimit_time'] = 'checked';
            }

            if(  $this->_aEditedCoupon['discount_type'] == 'special_price'){
                $this->_aEditedCoupon['discount_value'] = null ;
            }

            // prepare tab menus
            $aMenus = array(
                'main' => _p('coupon_detail'),
            );

            $this->template()->buildPageMenu('js_coupon_block', $aMenus, array(
                    'link' => $this->url()->permalink('coupon.detail', $this->_aEditedCoupon['coupon_id'], $this->_aEditedCoupon['title']),
                    'phrase' => _p('view_this_coupon')
                )
            );

            $this->_aEditedCoupon['unlimit_time'] = $this->_aEditedCoupon['expire_time'] ? '' : 'checked';
            $this->_iTotalFee = $this->_aEditedCoupon['is_featured'] ? $this->_iPublishFee + $this->_iFeatureFee : $this->_iPublishFee;

            $this->template()->assign(array(
                'aForms' => $this->_aEditedCoupon,
            ))->setHeader('cache', array(
                '<script type="text/javascript">$Behavior.funraisingEditCategory = function(){var aCategories = explode(\',\', \'' . $this->_aEditedCoupon['categories'] . '\'); for (i in aCategories) { $(\'#js_mp_holder_\' + aCategories[i]).show(); $(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true); }}</script>'
            ));

            (($sPlugin = Phpfox_Plugin::get('fundraising.component_controller_add_process_edit')) ? eval($sPlugin) : false);
        } else {
            Phpfox_Error::set(_p('unable_to_find_the_coupon_you_are_trying_to_edit'));
        }
    }

    /**
     * check validator for form
     * @by : datlv
     * @param array $aVals
     * @return array
     */
    private function _getValidationParams($aVals = array()) {

        $aParam = array(
            'title' => array(
                'def' => 'required',
                'title' => _p('fill_title_for_coupon'),
            ),
            'location_venue' => array(
                'def' => 'required',
                'title' => _p('fill_in_coupon_location'),
            ),
            'quantity' => array(
                'def' => 'number',
                'title' => _p('fill_in_a_discount_value_for_coupon'),
            ),
        );

        return $aParam;
    }

    /**
     * check if this form was submit
     * @by : datlv
     * @return bool
     */
    private function _checkIfSubmittingAForm() {
        if ($this->request()->getArray('val')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * update this coupon to db
     * @by : datlv
     * @param $aVals
     */
    private function _updateCoupon($aVals) {
        $iId = 0;
        $aVals['draft_publish'] = isset($aVals['is_publish']) ? $aVals['is_publish'] : 0;
        if (isset($aVals['update']) || isset($aVals['draft_update']) || isset($aVals['draft_publish'])) {
            $iId = Phpfox::getService('coupon.process')->update($this->_aEditedCoupon['coupon_id'], $aVals);
        }
        
        if ($iId) {
            $aSendParam['id'] = $iId;
            if($this->_sModule == 'pages')
            {
                $aSendParam['module'] = $this->_sModule;
                $aSendParam['item'] = $this->_iItem;
            }
            $sMessage = _p('coupon_updates') . '</br>';
            if(Phpfox_error::get())
            {
                foreach(Phpfox_error::get() as $sError)
                {
                    $sMessage .= $sError . '</br>' ;
                }
            }

            $this->url()->send('coupon.add', $aSendParam, $sMessage);
        }
        
        return false;
    }

    public function _checkEditPermission()
    {
        $bCanEdit = true;

        if($this->_aEditedCoupon['user_id'] != Phpfox::getUserId() &&  !Phpfox::getUserParam('coupon.can_edit_other_user_coupon'))
        {
            $bCanEdit = false;
        }
        if($this->_aEditedCoupon['user_id'] == Phpfox::getUserId() && !Phpfox::getUserParam('coupon.can_edit_own_coupon'))
        {
            $bCanEdit = false;
        }
        if(!Phpfox::isAdmin())
        {
            $iStatus = $this->_aEditedCoupon['status'];

            if($iStatus != Phpfox::getService('coupon')->getStatusCode('draft') && $iStatus != Phpfox::getService('coupon')->getStatusCode('denied'))
            {
                $bCanEdit = false;
            }
        }

        return $bCanEdit;
    }

    /**
     * Class process method which is used to execute this component.
     */
    public function process()
    {
        Phpfox::isUser(true);

        $this->_initVariables();

        $this->_checkIsInPageAndPagePermission();
        
        if ($this->_checkIsInEditCampaign()) {
            $this->_prepareEditForm();

            if(!$this->_checkEditPermission())
            {
                $this->url()->send('subscribe');
            }

            $aFields = Phpfox::getService('coupon.custom')->getByCouponId($this->_iEditedCouponId);
        } else {

            Phpfox::getUserParam('coupon.can_create_coupon', true);
            $aFields = Phpfox::getService('coupon.custom')->getCustomField();
        }

        if (!$this->_checkIfHavingPermissionToAddOrEditCampaign()) {
            return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
        }

        $aValidationParam = $this->_getValidationParams();

        $oValid = Phpfox::getLib('validator')->set(array(
                'sFormName' => 'ync_edit_coupon_form',
                'aParams' => $aValidationParam
            )
        );

        if ($this->_checkIfSubmittingAForm()) {

            $aVals = $this->request()->getArray('val');
            if(isset($aVals['cancel']))
                $this->url()->send('coupon');
            
            $aValidationParam = $this->_getValidationParams($aVals);

            $oValid = Phpfox::getLib('validator')->set(array(
                    'sFormName' => 'ync_edit_coupon_form',
                    'aParams' => $aValidationParam
                )
            );

            if ($this->_verifyCustomForm($aVals) && $oValid->isValid($aVals))
            {
                $this->_checkIfHavingSpamActionAndHandleIt();
            
                if (Phpfox_Error::isPassed())
                {
  
                    if ($this->_sModule && $this->_iItem && !$this->_bIsEdit)
                    {
                        $aVals['module_id'] = $this->_sModule;
                        $aVals['item_id'] = $this->_iItem;
                    }
            
                    $aSendParam = array();
                    if ($this->_sModule)
                        $aSendParam['module'] = $this->_sModule;
                    if ($this->_iItem)
                        $aSendParam['item'] = $this->_iItem;
            
                    if ($this->_bIsEdit)
                    {
                        if (!$this->_updateCoupon($aVals))
                        {
                            $this->template()->assign('bFail', 1);
                        }
                    }
                    elseif ($iId = Phpfox::getService('coupon.process')->add($aVals))
                    {
                        //resend to this controller and set tab to photos
                        $aSendParam['id'] = $iId;
                        $aSendParam['tab'] = 'main';
                        $this->url()->send('coupon.add', $aSendParam, _p('your_coupon_has_been_added'));
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

        

        foreach($aFields as $k=>$aField)
        {
            if($aField['var_type'] != 'text' && $aField['var_type'] != 'textarea' && empty($aField['option']))
            {
                unset($aFields[$k]);
            }
        }

        $aCategories = Phpfox::getService('coupon.category')->getCategories();
        
        $aCurrency = Phpfox::getService('coupon.helper')->getAllCurrency();

        $this->template()->setTitle(($this->_bIsEdit ? _p('editing_coupon') . ': ' . $this->_aEditedCoupon['title'] : _p('adding_a_new_coupon')))
            ->setBreadcrumb(_p('coupons'), ($this->_aCallback === false ? $this->url()->makeUrl('coupon') : $this->url()->makeUrl($this->_aCallback['url_home_pages'])))
            ->setBreadcrumb(($this->_bIsEdit ? _p('editing_coupon') . ': ' . Phpfox::getLib('parse.output')->shorten($this->_aEditedCoupon['title'], Phpfox::getService('core')->getEditTitleSize(), '...') : _p('adding_a_new_coupon')), ($this->_iEditedCouponId > 0 ? ($this->_aCallback == false ? $this->url()->makeUrl('coupon', array('add', 'id' => $this->_iEditedCouponId)) : $this->url()->makeUrl('coupon', array('add', 'id' => $this->_iEditedCouponId, 'module' => $this->_aCallback['module_id'], 'item' => $this->_aCallback['item_id'])) ) : ($this->_aCallback == false ? $this->url()->makeUrl('coupon', array('add')) : $this->url()->makeUrl('coupon', array('add', 'module' => $this->_aCallback['module_id'], 'item' => $this->_aCallback['item_id'])))), true)
            ->setEditor(array('wysiwyg' => true))
            ->setPhrase(array(
                'coupon.total_fee_multi_currency',
                'coupon.confirm_publish_coupon_multi_currency',
                'coupon.confirm_publish_again',
                'yes',
                'no'
            ))
            ->setHeader(array(
                'pager.css' => 'style_css',
                'global.css' => 'module_coupon',
                'detail.css'=> 'module_coupon',
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                'switch_legend.js' => 'static_script',
                'switch_menu.js' => 'static_script',
                'quick_edit.js' => 'static_script',
                'progress.js' => 'static_script',
                'add.js' => 'module_coupon',
                'yncoupon.js' => 'module_coupon',
                'jquery.validate.js' => 'module_coupon',
                'jquery.carouFredSel-6.2.1-packed.js' => 'module_coupon',
                'country.js' => 'module_core',
                'magnific-popup.css' => 'module_coupon',
                'jquery.magnific-popup.js' => 'module_coupon',
            ))
            ->assign(array(
                'sCreateJs' => $oValid->createJS(),
                'sGetJsForm' => $oValid->getJsForm(),
                'sModule' => ($this->_aCallback !== false ? $this->_sModule : ''),
                'iItem' => ($this->_aCallback !== false ? $this->_iItem : ''),
                'bIsEdit' => $this->_bIsEdit,
                'bCanEditPersonalData' => $this->_bCanEditPersonalData,
                'aCategories' => $aCategories,
                'iPublishFee' => $this->_iPublishFee,
                'iFeatureFee' => $this->_iFeatureFee,
                'iTotalFee' => $this->_iTotalFee,
                'symbolCurrencyFee' => $this->_symbolCurrencyFee,
                'aTempPredefined' => array('', ''),
                'sCategories' => Phpfox::getService('coupon.category')->get(),
                'iDeniedCode' => Phpfox::getService('coupon')->getStatusCode('denied'),
                'aCurrency' => $aCurrency,
                'aFields' => $aFields
            ));
        $this->template()->assign('bNoAttachaFile', true);
        if (Phpfox::isModule('attachment')) {
            $this->setParam('attachment_share', array(
                'type' => 'coupon',
                'id' => 'ync_edit_coupon_form',
                'edit_id' => ($this->_bIsEdit ? $this->_iEditedCouponId : 0),
            ));
        }
		
    }
    
    private function _verifyCustomForm($aVals)
    {
        if(isset($aVals['custom'])) {
            $aFieldValues = $aVals['custom'];

            $aFields =Phpfox::getService('coupon.custom')->getCustomField();

            foreach($aFields as $k=>$aField)
            {
                if( $aField['is_required'] &&  ( !isset($aFieldValues[$aField['field_id']]) || ( isset($aFieldValues[$aField['field_id']]) && empty($aFieldValues[$aField['field_id']]) ) ) )
                {
                    return Phpfox_Error::set(_p('custom_field_is_required', array('custom_field' => _p($aField['phrase_var_name']))));
                }
            }

        }
        return true;

    }

    private function _setFailForms($aVals)
    { 
        $aFields =Phpfox::getService('coupon.custom')->getCustomField();    

        if(isset($aVals['custom'])) {
            foreach ($aVals['custom'] as $key_custom => $custom_value) {
                foreach ($aFields as &$aField) {
                    if($aField['field_id'] == $key_custom){
                        if(is_array($custom_value)) {
                            foreach ($custom_value as $key => $value) {
                                $aField['value'][$value] = $value;
                            }
                        }
                        else{
                            $aField['value'] = $custom_value;
                        }
                    }
                }
            }
        }
        if (isset($aVals['feature_coupon']))
        {
            $this->_iTotalFee = $this->_iPublishFee + $this->_iFeatureFee;
        }
        $this->template()->assign(array(
            'aFields' => $aFields,
            'aForms' => $aVals,
            'bFail' => 1
        ));

        $this->template()->setHeader('cache', array(
            '<script type="text/javascript">
                $Behavior.funraisingEditCategory = function(){var aCategories = explode(\',\', \'' . implode(',', $aVals['category']) . '\'); for (i in aCategories) { $(\'#js_mp_holder_\' + aCategories[i]).show(); $(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true); }}
            </script>',

            

        ));

        return $aFields;
    }
	
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('coupon.component_controller_add_clean')) ? eval($sPlugin) : false);
        $this->template()->clean(array(
                'bIsEdit',
                'aCategories'
            )
        );
    }
}

?>