<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_Add extends Phpfox_Component
{

    private $_unitCurrencyFee = null;
    private $_symbolCurrencyFee = null;

    private $_iFeatureFee = 5;
    private $_iDefaultFeatureFee = 5;
    private $_iDefaultPublishFee = 10;
    private $_iThemeId = 1;


    private $_bIsEdit = false;
    private $_bCanEditPersonalData = true;
    private $_aCallback = false;
    private $_sModule = null;
    private $_iItem = null;
    private $_iEditedAuctionId = null;
    private $_aEditedAuction = array();

    private function _initVariables()
    {

        $aCurrentCurrencies = Phpfox::getService('ecommerce.helper')->getCurrentCurrencies();
        $this->_unitCurrencyFee = $aCurrentCurrencies[0]['currency_id'];
        $this->_symbolCurrencyFee = $aCurrentCurrencies[0]['symbol'];
        $this->_iDefaultFeatureFee = Phpfox::getUserParam('auction.how_much_is_user_worth_for_auction_featured');

        $this->_iDefaultPublishFee = Phpfox::getUserParam('auction.how_much_is_user_worth_for_auction_publishing');


        $this->_bIsEdit = false;
        $this->_bCanEditPersonalData = true;
        $this->_aCallback = false;
        $this->_sModule = $this->request()->get('module', false);
        $this->_iItem = $this->request()->getInt('item', false);
        $this->_iEditedAuctionId = null;
        $this->_aEditedAuction = array();
        $this->_aCloneAuction = array();

    }

    private function _checkIsInPageAndPagePermission()
    {
        //will check later

        if ($this->_sModule != 'auction' && $this->_iItem !== false) {

            switch ($this->_sModule) {
                case 'pages':
                    $this->_aCallback = Phpfox::callback('auction.getAuctionsDetails',
                        array('item_id' => $this->_iItem));
                    break;

                default:
                    $this->_aCallback = Phpfox::callback($this->_sModule . '.getAuctionsDetails',
                        array('item_id' => $this->_iItem));
                    break;
            }

            if ($this->_aCallback) {
                $this->template()->setBreadcrumb($this->_aCallback['breadcrumb_title'],
                    $this->_aCallback['breadcrumb_home']);
                $this->template()->setBreadcrumb($this->_aCallback['title'], $this->_aCallback['url_home']);
                if ($this->_sModule == 'pages' && !Phpfox::getService('pages')->hasPerm($this->_iItem,
                        'auction.share_auctions')) {
                    return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings '));
                }
            }
        }
    }

    private function _checkIfHavingPermissionToAddOrEditCampaign()
    {
        //will check later
        /*
            if ($this->_sModule && $this->_iItem && Phpfox::hasCallback($this->_sModule, 'viewauction')) {
                $this->_aCallback = Phpfox::callback($this->_sModule . '.viewauction', $this->_iItem);
                $this->template()->setBreadcrumb($this->_aCallback['breadcrumb_title'], $this->_aCallback['breadcrumb_home']);
                $this->template()->setBreadcrumb($this->_aCallback['title'], $this->_aCallback['url_home']);
                if ($this->_sModule == 'pages' && !Phpfox::getService('pages')->hasPerm($this->_iItem, 'auction.share_campaigns')) {
                    return false;
                }
            }

            return true;
        */
    }

    private function _checkIsInEditAuction()
    {
        if ($this->request()->getInt('id') && !isset($_POST['val']['add'])) {
            $this->_iEditedAuctionId = $this->request()->getInt('id');
            return true;
        } else {
            return false;
        }
    }

    private function _getValidationParams($aVals = array())
    {

        $aValidation = array(
            'name' => array(
                'def' => 'required',
                'title' => _p('fill_title_for_auction')
            ),
        );

        return $aValidation;
    }

    private function _checkIfSubmittingAForm()
    {
        if ($this->request()->getArray('val')) {
            return true;
        } else {
            return false;
        }
    }

    public function _checkEditPermission()
    {
        return true;
    }


    private function pay($aVals, $iAuctionId, $iProductId)
    {


        $currency_id = $this->_unitCurrencyFee;
        $publishFee = doubleval($this->_iDefaultPublishFee);
        $featureFee = doubleval(($aVals['feature_number_days'] * $this->_iDefaultFeatureFee));

        $fee = $publishFee + $featureFee;

        if ($fee > 0) {
            // add invoice
            $iInvoice = Phpfox::getService('ecommerce.process')->addInvoice($iProductId, $currency_id, $fee, 'product',
                array(
                    'pay_type' => (Phpfox::getUserParam('auction.can_feature_auction') ? ($aVals['feature_number_days'] > 0 ? 'feature' : '') : '') . '|' . ($publishFee > 0 ? 'publish' : ''),
                    'feature_days' => $aVals['feature_number_days']
                ), 'auction');
            $aPurchase = Phpfox::getService('ecommerce')->getInvoice($iInvoice);

            // process payment
            if (empty($iInvoice['status'])) {
                $this->setParam('gateway_data', array(
                        'item_number' => 'auction|' . $aPurchase['invoice_id'],
                        'currency_code' => $aPurchase['default_currency_id'],
                        'amount' => $aPurchase['default_cost'],
                        'item_name' => ($publishFee > 0 ? 'publish' : '') . '|' . ($aVals['feature_number_days'] > 0 ? 'feature' : ''),
                        'return' => Phpfox::permalink('auction.detail', $iProductId, $aVals['name'], false,
                                '') . 'businesspayment_done/',
                        'recurring' => '',
                        'recurring_cost' => '',
                        'alternative_cost' => '',
                        'alternative_recurring_cost' => ''
                    )
                );
                return $aPurchase['invoice_id'];
            }
        } else {
            /*feature with no fee*/
            $start_feature_time = 0;
            $end_feature_time = 0;

            if ($aVals['feature_number_days'] > 0) {

                $start_time = Phpfox::getLib('date')->mktime(0, 0, 0, $aVals['start_time_month'],
                    $aVals['start_time_day'], $aVals['start_time_year']);

                if ($start_time < PHPFOX_TIME) {/*in available time of auction*/
                    $start_feature_time = PHPFOX_TIME;
                } else {/*start time of auction in future*/
                    $start_feature_time = $start_time;

                }

                $end_feature_time = $start_feature_time + ((int)$aVals['feature_number_days'] * 86400);

                if ($end_feature_time >= 4294967295) {
                    $end_feature_time = 4294967295;
                }

            }
            Phpfox::getService('ecommerce.process')->updateProductFeatureTime($iProductId, $start_feature_time,
                $end_feature_time, (int)$aVals['feature_number_days'], $featureFee);


            /*publish with no fee*/
            $status = 'draft';
            if (Phpfox::getService('ecommerce.helper')->getUserParam('auction.admin_want_auction_to_be_automatically_approved_after_published',
                Phpfox::getUserId())) {
                $status = 'approved';
            } else {
                $status = 'pending';
            }

            Phpfox::getService('ecommerce.process')->updateProductStatus($iProductId, $status);

            if ($status == 'approved') {
                // call approve function
                Phpfox::getService('ecommerce.process')->approveProduct($iProductId, null, 'auction');
            }


            return true;

        }

        return false;

    }


    private function _prepareDataForClone($iAuctionCloneId)
    {

        $oAuctionService = Phpfox::getService('auction');

        if ($this->_aCloneAuction = $oAuctionService->getAuctionForEdit($iAuctionCloneId)) {

            $this->_aCloneAuction['start_time_month'] = Phpfox::getTime('n', PHPFOX_TIME, false);
            $this->_aCloneAuction['start_time_day'] = Phpfox::getTime('j', PHPFOX_TIME, false);
            $this->_aCloneAuction['start_time_year'] = Phpfox::getTime('Y', PHPFOX_TIME, false);


            $this->_aCloneAuction['end_time_month'] = Phpfox::getTime('n', PHPFOX_TIME, false);
            $this->_aCloneAuction['end_time_day'] = Phpfox::getTime('j', PHPFOX_TIME, false);
            $this->_aCloneAuction['end_time_year'] = Phpfox::getTime('Y', PHPFOX_TIME, false);
            $this->_sModule = $this->_aCloneAuction['module_id'];
            $this->_iItem = $this->_aCloneAuction['item_id'];
            $this->_checkIsInPageAndPagePermission();
            if (Phpfox::isModule('tag')) {

                $this->_aCloneAuction['tag_list'] = '';

                $aTags = Phpfox::getService('tag')->getTagsById('auction', $iAuctionCloneId);

                if (isset($aTags[$iAuctionCloneId])) {
                    foreach ($aTags[$iAuctionCloneId] as $aTag) {
                        $this->_aCloneAuction['tag_list'] .= ' ' . $aTag['tag_text'] . ',';
                    }
                    $this->_aCloneAuction['tag_list'] = trim(trim($this->_aCloneAuction['tag_list'], ','));
                }
            }


            $this->template()->setHeader('cache', array(
                '<script type="text/javascript">
                    $Behavior.auctionEditCategory = function(){
                            var aCategories = explode(\',\', \'' . $this->_aCloneAuction['categories'] . '\'); 
                            for (i in aCategories) {
                                 $(\'#js_mp_holder_\' + aCategories[i]).show();
                                 $(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true);
                            }

                            var iMainCategoryId = $(\'#js_mp_id_0\').val();
                            ynauction.changeCustomFieldByMainCategory(iMainCategoryId,' . $iAuctionCloneId . ');

                    }
                 </script>'
            ));


            $this->template()->assign(array(
                'aForms' => $this->_aCloneAuction,
                'iAuctionId' => $iAuctionCloneId,
            ));

        } else {
            Phpfox_Error::set(_p('unable_to_find_the_auction_you_are_trying_to_edit'));
        }
    }

    public function process()
    {
        Phpfox::getService('auction.helper')->buildMenu();
        $aGlobalSetting = Phpfox::getService('ecommerce')->getGlobalSetting();
        $bUsingAdaptive = isset($aGlobalSetting['actual_setting']['payment_settings']) ? ($aGlobalSetting['actual_setting']['payment_settings']) : 0;

        if ($bUsingAdaptive) {
            $aGatewayValues = Phpfox::getService('api.gateway')->getUserGateways(Phpfox::getUserId());

            if (!isset($aGatewayValues['paypal']['gateway']['paypal_email']) || ($aGatewayValues['paypal']['gateway']['paypal_email'] == '')) {
                $sError = _p('please_input_your_paypal_email_at_account_account_settings_before_creating_auction');
                Phpfox_Error::display($sError);
            }
        }

        Phpfox::isUser(true);

        $this->_initVariables();


        /*check permission*/
        Phpfox::getUserParam('auction.can_create_auction', true);

        $aValidationParam = $this->_getValidationParams();

        $oValid = Phpfox::getLib('validator')->set(array(
                'sFormName' => 'ync_add_auction_form',
                'aParams' => $aValidationParam
            )
        );

        /*check action publish from manage auction*/
        if ($iAuctionCloneId = $this->request()->get('cloneid')) {
            $this->_prepareDataForClone($iAuctionCloneId);
        }


        $this->_checkIsInPageAndPagePermission();

        if ($this->_checkIfSubmittingAForm()) {

            $aVals = $this->request()->getArray('val');
            if (($iFlood = Phpfox::getUserParam('auction.minute_to_control_how_long_user_can_create_a_auction')) !== 0) {
                $aFlood = array(
                    'action' => 'last_post', // The SPAM action
                    'params' => array(
                        'field' => 'product_creation_datetime', // The time stamp field
                        'table' => Phpfox::getT('ecommerce_product'), // Database table we plan to check
                        'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
                        'time_stamp' => $iFlood * 60 // Seconds);
                    )
                );

                // actually check if flooding
                if (Phpfox::getLib('spam')->check($aFlood)) {
                    Phpfox_Error::set(_p('you_are_posting_a_litte_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
                }
            }

            if (Phpfox_Error::isPassed()) {

                if ($this->_sModule && $this->_iItem && !$this->_bIsEdit) {
                    $aVals['module_id'] = $this->_sModule;
                    $aVals['item_id'] = $this->_iItem;
                }

                $aSendParam = array();
                if ($this->_sModule) {
                    $aSendParam['module'] = $this->_sModule;
                }
                if ($this->_iItem) {
                    $aSendParam['item'] = $this->_iItem;
                }

                if ($iId = Phpfox::getService('auction.process')->add($aVals)) /*add auction*/ {
                    $aAuction = Phpfox::getService('auction')->getQuickAuctionByAuctionId($iId);
                    $aSendParam['id'] = $iId;
                    $aSendParam['tab'] = 'main';
                    //handle for save draft,publish or feature.
                    //resend to this controller and set tab to photos
                    if (isset($aVals['draft']) && $aVals['draft'] = 'draft') {
                        $this->url()->send('auction.detail', array($aAuction['product_id']),
                            _p('your_auction_has_been_added'));
                    } elseif (isset($aVals['publish']) && $aVals['publish'] = 'publish') {
                        $iAuctionId = $aAuction['auction_id'];
                        $iProductId = $aAuction['product_id'];

                        $invoice_id = $this->pay($aVals, $iAuctionId, $iProductId);

                        if ($invoice_id === true) {
                            // create business withou fee
                            $this->url()->permalink('auction.detail', $aAuction['product_id'], $aVals['name'], true,
                                _p('your_auction_has_been_added'));
                        } else {
                            if ((int)$invoice_id > 0) {
                                $this->template()->assign(array(
                                    'invoice_id' => $invoice_id,
                                ));
                            }
                        }
                    }

                } else {
                    $this->_setFailForms($aVals);
                }

            }
        }

        if (!isset($invoice_id)) {

            $aCategories = array();//Phpfox::getService('ecommerce.category')->getCategories();
            $aUOMs = Phpfox::getService('ecommerce.uom')->getAll();
            $aGlobalSetting = Phpfox::getService('auction')->getGlobalSetting();
            $aSellerSettings = Phpfox::getService('ecommerce.sellersettings')->get(Phpfox::getUserId());

            $iRatioBuyItNowPrice = isset($aSellerSettings['limit_percent_buy_it_now_price']) ? $aSellerSettings['limit_percent_buy_it_now_price'] : 100;

            if (Phpfox::isModule('attachment')) {
                $this->setParam('attachment_share', array(
                        'type' => 'auction',
                        'id' => 'ynauction_add_auction_form',
                    )
                );
            }

            $this->template()->assign('bNoAttachaFile', true);

            $this->template()->setTitle(_p('add'))
                ->setBreadcrumb(_p('auctions'),
                    ($this->_aCallback === false ? $this->url()->makeUrl('auction') : $this->url()->makeUrl($this->_aCallback['url_home_pages'])))
                ->setBreadcrumb(
                    (_p('add_new_auction'))
                    , ($this->_aCallback == false ? $this->url()->makeUrl('auction',
                    array('add')) : $this->url()->makeUrl('auction',
                    array('add', 'module' => $this->_aCallback['module_id'], 'item' => $this->_aCallback['item_id'])))
                    , true
                )
                ->setEditor(array('wysiwyg' => true));

            $this->template()->assign(array(
                'sCreateJs' => $oValid->createJS(),
                'sGetJsForm' => $oValid->getJsForm(),
                'sFormUrl' => $this->url()->makeUrl('auction.add'),
                'sBackUrl' => $this->url()->makeUrl('auction'),
                'sModule' => ($this->_aCallback !== false ? $this->_sModule : ''),
                'iItem' => ($this->_aCallback !== false ? $this->_iItem : ''),
                'bIsEdit' => $this->_bIsEdit,
                'bCanEditPersonalData' => $this->_bCanEditPersonalData,
                'aCategories' => $aCategories,
                'sCategories' => Phpfox::getService('ecommerce.category')->get(),
                'aCurrentCurrencies' => Phpfox::getService('ecommerce.helper')->getCurrentCurrencies(),
                'iDefaultFeatureFee' => $this->_iDefaultFeatureFee,
                'iDefaultPublishFee' => $this->_iDefaultPublishFee,
                'core_path' => Phpfox::getParam('core.path'),
                'aUOMs' => $aUOMs,
                'aGlobalSetting' => $aGlobalSetting,
                'iRatioBuyItNowPrice' => $iRatioBuyItNowPrice,
                'max_upload_size_photos' => Phpfox::getUserParam('ecommerce.max_size_for_icons'),
                'corepath' => phpfox::getParam('core.path'),
            ))->setHeader('cache', array(
                'pager.css' => 'style_css',
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                'switch_legend.js' => 'static_script',
                'switch_menu.js' => 'static_script',
                'quick_edit.js' => 'static_script',
                'progress.js' => 'static_script',
                'country.js' => 'module_core'
            ));

        } else {
            // confirm payment 
            $this->template()->setTitle(_p('review_and_confirm_purchase'))
                ->setBreadcrumb(_p('module_menu'), $this->url()->makeUrl('auction'))
                ->setBreadcrumb(_p('review_and_confirm_purchase'), null, true);
        }

        Phpfox::getService('auction.helper')->loadAuctionJsCss();


    }

    private function _setFailForms($aVals)
    {

        // process for category 
        $iMainCategoryId = 0;
        if (isset($aVals['category'])) {
            $iMainCategoryId = (int)$aVals['category']['0']['0'];
        }


        // process for custom field 
        if (isset($aVals['custom'])) {
            $aCustomFields = Phpfox::getService('ecommerce')->getCustomFieldByCategoryId($iMainCategoryId);
            $custom = $aVals['custom'];
            foreach ($aCustomFields as $keyaCustomFields => $valueaCustomFields) {
                foreach ($custom as $keycustom => $valuecustom) {
                    if ($valueaCustomFields['field_id'] == $keycustom) {
                        if (is_array($valuecustom)) {
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
            Phpfox::getBlock('auction.custom.form', array(
                'aCustomFields' => $aCustomFields,
            ));

            $sContent = $oAjax->getContent(false);
            $sContent = stripslashes($sContent);
            $sContent = base64_encode($sContent);

            $this->template()->setHeader('cache', array(
                '<script type="text/javascript">;$Behavior.auctionAddCategoryCustomField = function(){              
                    $(\'#ynauction_customfield_category\').html(ynauctionhelper.base64_decode(\'' . $sContent . '\'));   
                    setTimeout(function(){ynauction.addValidateForCustomField(); }, 2000);
                           
                 };
                </script>'
            ));


        }
        $this->template()->setHeader('cache', array(
            '<script type="text/javascript">
                    $Behavior.auctionEditCategory = function(){
                            var aCategories = explode(\',\', \'' . $aVals['category']['0']['0'] . '\'); 
                            for (i in aCategories) {
                                 $(\'#js_mp_holder_\' + aCategories[i]).show();
                                 $(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true);
                            }
                    }
                 </script>'
        ));

        $this->template()->assign(array(
            'aForms' => $aVals,
            'bFail' => 1
        ));
    }

    public function getCategoriesFromForm($aVals)
    {
        $aAuctionCategories = array();
        if (isset($aVals['category']) && count($aVals['category'])) {
            if (empty($aVals['category'][0])) {
                return false;
            } else {
                if (!is_array($aVals['category'])) {
                    $aAuctionCategories[] = $aVals['category'];
                } else {
                    foreach ($aVals['category'] as $aCategory) {

                        foreach ($aCategory as $iCategory) {
                            if (empty($iCategory)) {
                                continue;
                            }

                            if (!is_numeric($iCategory)) {
                                continue;
                            }

                            $aAuctionCategories[] = $iCategory;
                        }
                    }
                }
            }
        }
        return $aAuctionCategories;
    }


}

?>