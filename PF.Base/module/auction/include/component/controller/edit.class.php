<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_Edit extends Phpfox_Component
{

    private $_unitCurrencyFee = null;
    private $_symbolCurrencyFee = null;

    private $_iDefaultFeatureFee = 5;
    private $_iDefaultPublishFee = 10;
    private $_iPublishFeeAgain = true;

    private $_bIsEdit = false;
    private $_bCanEditPersonalData = true;
    private $_aCallback = false;
    private $_sModule = null;
    private $_iItem = null;
    private $_iEditedAuctionId = null;
    private $_iEditedProductId = null;
    private $_aEditedAuction = array();

    private function _initVariables()
    {
        $aCurrentCurrencies = Phpfox::getService('ecommerce.helper')->getCurrentCurrencies();
        $this->_unitCurrencyFee = $aCurrentCurrencies[0]['currency_id'];
        $this->_symbolCurrencyFee = $aCurrentCurrencies[0]['symbol'];
        $this->_iDefaultFeatureFee = Phpfox::getUserParam('auction.how_much_is_user_worth_for_auction_featured');
        $this->_iDefaultPublishFee = Phpfox::getUserParam('auction.how_much_is_user_worth_for_auction_publishing');

        $aGlobalSetting = Phpfox::getService('ecommerce')->getGlobalSetting();
        $this->_iPublishFeeAgain = isset($aGlobalSetting['actual_setting']['publish_item_fee_again']) ? $aGlobalSetting['actual_setting']['publish_item_fee_again'] : false;
        $this->_bIsEdit = false;
        $this->_bCanEditPersonalData = true;
        $this->_aCallback = false;
        $this->_sModule = $this->request()->get('module', false);
        $this->_iItem = $this->request()->getInt('item', false);
        $this->_iEditedAuctionId = null;
        $this->_aEditedAuction = array();
    }

    private function _checkIsInEditAuction()
    {
        if ($this->request()->getInt('id') && !isset($_POST['val']['add'])) {
            $aAuction = Phpfox::getService('auction')->getQuickAuctionByProductId($this->request()->getInt('id'));
            $this->_iEditedProductId = $this->request()->getInt('id');
            $this->_iEditedAuctionId = $aAuction['auction_id'];
            return true;
        } else {
            return false;
        }
    }

    private function _prepareEditForm()
    {

        $oAuctionService = Phpfox::getService('auction');

        if ($this->_aEditedAuction = $oAuctionService->getAuctionForEdit($this->_iEditedProductId)) {
            if ($this->_aEditedAuction['module_id'] != 'auction') {
                $this->_sModule = $this->_aEditedAuction['module_id'];
                $this->_iItem = $this->_aEditedAuction['item_id'];
            }
            $this->_checkIsInPageAndPagePermission();
            if (!Phpfox::getService('auction.permission')->canEditAuction($this->_aEditedAuction['user_id'], $this->_aEditedAuction['product_id'])) {
                $this->url()->send('auction');
            }

            $this->_bIsEdit = true;

            if (!empty($this->_aEditedAuction['logo_path'])) {
                $this->_aEditedAuction['current_image'] = Phpfox::getLib('image.helper')->display(
                    array(
                        'server_id' => $this->_aEditedAuction['server_id'],
                        'path' => 'core.url_pic',
                        'file' => $this->_aEditedAuction['logo_path'],
                        'suffix' => '_200',
                        'return_url' => true
                    )
                );
            }

            if ($this->_aEditedAuction['start_time']) {
                $this->_aEditedAuction['start_time_month'] = Phpfox::getTime('n', $this->_aEditedAuction['start_time'], false);
                $this->_aEditedAuction['start_time_day'] = Phpfox::getTime('j', $this->_aEditedAuction['start_time'], false);
                $this->_aEditedAuction['start_time_year'] = Phpfox::getTime('Y', $this->_aEditedAuction['start_time'], false);

                $this->_aEditedAuction['start_time_text'] = Phpfox::getTime('j/n/Y', $this->_aEditedAuction['start_time'], false);
            } else {
                $this->_aEditedAuction['start_time_month'] = Phpfox::getTime('n', PHPFOX_TIME, false);
                $this->_aEditedAuction['start_time_day'] = Phpfox::getTime('j', PHPFOX_TIME, false);
                $this->_aEditedAuction['start_time_year'] = Phpfox::getTime('Y', PHPFOX_TIME, false);

                $this->_aEditedAuction['start_time_text'] = Phpfox::getTime('j/n/Y', PHPFOX_TIME, false);
            }

            if ($this->_aEditedAuction['end_time']) {
                $this->_aEditedAuction['end_time_month'] = Phpfox::getTime('n', $this->_aEditedAuction['end_time'], false);
                $this->_aEditedAuction['end_time_day'] = Phpfox::getTime('j', $this->_aEditedAuction['end_time'], false);
                $this->_aEditedAuction['end_time_year'] = Phpfox::getTime('Y', $this->_aEditedAuction['end_time'], false);

                $this->_aEditedAuction['end_time_text'] = Phpfox::getTime('j/n/Y', $this->_aEditedAuction['end_time'], false);
            } else {
                $this->_aEditedAuction['end_time_month'] = Phpfox::getTime('n', PHPFOX_TIME, false);
                $this->_aEditedAuction['end_time_day'] = Phpfox::getTime('j', PHPFOX_TIME, false);
                $this->_aEditedAuction['end_time_year'] = Phpfox::getTime('Y', PHPFOX_TIME, false);

                $this->_aEditedAuction['end_time_text'] = Phpfox::getTime('j/n/Y', PHPFOX_TIME, false);
            }

            if (Phpfox::isModule('tag')) {

                $this->_aEditedAuction['tag_list'] = '';

                $aTags = Phpfox::getService('tag')->getTagsById('auction', $this->_iEditedAuctionId);

                if (isset($aTags[$this->_iEditedAuctionId])) {
                    foreach ($aTags[$this->_iEditedAuctionId] as $aTag) {
                        $this->_aEditedAuction['tag_list'] .= ' ' . $aTag['tag_text'] . ',';
                    }
                    $this->_aEditedAuction['tag_list'] = trim(trim($this->_aEditedAuction['tag_list'], ','));
                }
            }

            /*get featured or not*/
            if ($this->_aEditedAuction['feature_start_time'] <= PHPFOX_TIME && $this->_aEditedAuction['feature_end_time'] >= PHPFOX_TIME
                && ($this->_aEditedAuction['product_status'] == 'approved' || $this->_aEditedAuction['product_status'] == 'running' || $this->_aEditedAuction['product_status'] == 'bidden')
            ) {
                $this->_aEditedAuction['featured'] = true;
            } else {
                $this->_aEditedAuction['featured'] = false;
            }

            if ($this->_aEditedAuction['feature_end_time'] != 0) {
                if ($this->_aEditedAuction['feature_end_time'] > $this->_aEditedAuction['end_time']) {

                    $this->_aEditedAuction['feature_end_time'] = $this->_aEditedAuction['end_time'];
                }
                $this->_aEditedAuction['expired_date'] = Phpfox::getService('ecommerce.helper')->convertTime($this->_aEditedAuction['feature_end_time']);
                $this->_aEditedAuction['start_date'] = Phpfox::getService('ecommerce.helper')->convertTime($this->_aEditedAuction['feature_start_time']);
            }


            $this->template()->setHeader('cache', array(
                '<script type="text/javascript">
                    $Behavior.auctionEditCategory = function(){
                            var aCategories = explode(\',\', \'' . $this->_aEditedAuction['categories'] . '\'); 
                            for (i in aCategories) {
                                 $(\'#js_mp_holder_\' + aCategories[i]).show();
                                 $(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true);
                            }

                            var iMainCategoryId = $(\'#js_mp_id_0\').val();
                            ynauction.changeCustomFieldByMainCategory(iMainCategoryId,' . $this->_aEditedAuction['product_id'] . ');

                    }
                 </script>'
            ));

            if ($this->_aEditedAuction['product_status'] != 'denied' && $this->_aEditedAuction['product_status'] != 'draft') {
                $this->_iDefaultPublishFee = 0;
            }

            if ($this->_aEditedAuction['product_status'] == 'approved' && $this->_aEditedAuction['start_time'] <= PHPFOX_TIME && $this->_aEditedAuction['end_time'] > PHPFOX_TIME) {
                Phpfox::getService('ecommerce.process')->updateProductStatus($this->_aEditedAuction['product_id'], 'running');
            }


            /* echo '<pre>';
             print_r($this->_aEditedAuction);
             die;*/
            $this->template()->assign(array(
                'aForms' => $this->_aEditedAuction,
                'aAuction' => $this->_aEditedAuction,
                'iAuctionId' => $this->_iEditedAuctionId,
            ));

        } else {
            Phpfox_Error::set(_p('unable_to_find_the_auction_you_are_trying_to_edit'));
        }
    }


    private function _getValidationParams($aVals = array())
    {

        $aParam = array();

        return $aParam;
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

    private function _checkIsInPageAndPagePermission() {
        //will check later

        if ($this->_sModule !== false && $this->_iItem !== false) {

            switch ($this->_sModule) {
                case 'pages':
                    $this->_aCallback = Phpfox::callback('auction.getAuctionsDetails', array('item_id' => $this->_iItem));
                    break;

                default:
                    $this->_aCallback = Phpfox::callback($this->_sModule . '.getAuctionsDetails', array('item_id' => $this->_iItem));
                    break;
            }

            if ($this->_aCallback) {
                $this->template()->setBreadcrumb($this->_aCallback['breadcrumb_title'], $this->_aCallback['breadcrumb_home']);
                $this->template()->setBreadcrumb($this->_aCallback['title'], $this->_aCallback['url_home']);
                if ($this->_sModule == 'pages' && !Phpfox::getService('pages')->hasPerm($this->_iItem, 'auction.share_auctions')) {
                    return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings '));
                }
            }
        }
    }

    private function pay($aVals, $iAuctionId, $iProductId)
    {


        $currency_id = $this->_unitCurrencyFee;
        $publishFee = doubleval($this->_iDefaultPublishFee);
        $featureFee = doubleval(($aVals['feature_number_days'] * $this->_iDefaultFeatureFee));


        if ($this->_aEditedAuction['product_status'] == 'denied' && $this->_aEditedAuction['creating_item_fee'] != 0) {
            if ($this->_iPublishFeeAgain) {
                $publishFee = doubleval($this->_iDefaultPublishFee);
            } else {
                $publishFee = 0;
            }
        }

        $fee = $publishFee + $featureFee;
        if ($fee > 0) {
            // add invoice
            $iInvoice = Phpfox::getService('ecommerce.process')->addInvoice($iProductId, $currency_id, $fee, 'product', array(
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
                        'return' => Phpfox::permalink('auction.detail', $iProductId, $aVals['name'], false, '') . 'businesspayment_done/',
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

                $start_time = Phpfox::getLib('date')->mktime(0, 0, 0, $aVals['start_time_month'], $aVals['start_time_day'], $aVals['start_time_year']);

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
            Phpfox::getService('ecommerce.process')->updateProductFeatureTime($iProductId, $start_feature_time, $end_feature_time, (int)$aVals['feature_number_days'], $featureFee);


            /*publish with no fee*/
            $status = 'draft';
            if (Phpfox::getService('ecommerce.helper')->getUserParam('auction.admin_want_auction_to_be_automatically_approved_after_published', Phpfox::getUserId())) {
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

    private function payFeature($aVals, $iAuctionId, $iProductId)
    {


        $currency_id = $this->_unitCurrencyFee;
        $featureFee = doubleval(($aVals['feature_number_days'] * $this->_iDefaultFeatureFee));


        if ($featureFee > 0) {
            // add invoice
            $iInvoice = Phpfox::getService('ecommerce.process')->addInvoice($iProductId, $currency_id, $featureFee, 'feature', array(
                'pay_type' => (Phpfox::getUserParam('auction.can_feature_auction') ? ($aVals['feature_number_days'] > 0 ? 'feature' : '') : ''),
                'feature_days' => $aVals['feature_number_days']
            ), 'auction');
            $aPurchase = Phpfox::getService('ecommerce')->getInvoice($iInvoice);

            // process payment
            if (empty($iInvoice['status'])) {
                $this->setParam('gateway_data', array(
                        'item_number' => 'auction|' . $aPurchase['invoice_id'],
                        'currency_code' => $aPurchase['default_currency_id'],
                        'amount' => $aPurchase['default_cost'],
                        'item_name' => ($aVals['feature_number_days'] > 0 ? 'feature' : ''),
                        // 'return' => $this->url()->makeUrl('directory.detail', array('id' => 'done', 'payment' => 'done')),
                        'return' => Phpfox::permalink('auction.detail', $iProductId, $aVals['name'], false, '') . 'businesspayment_done/',
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
            $this->_aEditedAuction = Phpfox::getService('auction')->getAuctionForEdit($this->_iEditedAuctionId);
            /*not in feature or feature is expired*/
            if (isset($this->_aEditedAuction['feature_end_time']) && $this->_aEditedAuction['feature_end_time'] < PHPFOX_TIME) {

                if ((int)$aVals['feature_number_days'] > 0) {

                    $start_feature_time = 0;
                    $end_feature_time = 0;

                    $start_time = Phpfox::getLib('date')->mktime(0, 0, 0, $aVals['start_time_month'], $aVals['start_time_day'], $aVals['start_time_year']);

                    if ($start_time < PHPFOX_TIME) {/*in available time of auction*/
                        $start_feature_time = PHPFOX_TIME;
                    } else {/*start time of auction in future*/
                        $start_feature_time = $start_time;

                    }

                    $end_feature_time = $start_feature_time + ((int)$aVals['feature_number_days'] * 86400);

                    if ($end_feature_time >= 4294967295) {
                        $end_feature_time = 4294967295;
                    }

                    Phpfox::getService('ecommerce.process')->updateProductFeatureTime($iProductId, $start_feature_time, $end_feature_time, (int)$aVals['feature_number_days'], $featureFee);

                }


            } else {/*already featured ,wanna expand feature time*/


                $start_feature_time = 0;
                $end_feature_time = 0;

                $feature_day = isset($this->_aEditedAuction['feature_day']) ? $this->_aEditedAuction['feature_day'] : 0;

                if ((int)$feature_day > 0) {

                    $start_feature_time = $this->_aEditedAuction['feature_start_time'];

                    $end_feature_time = $start_feature_time + ((int)$feature_day * 86400);

                    if ($end_feature_time >= 4294967295) {
                        $end_feature_time = 4294967295;
                    }

                    Phpfox::getService('ecommerce.process')->updateProductFeatureTime($iProductId, $start_feature_time, $end_feature_time, (int)$feature_day, $featureFee);

                }
            }


            return true;

        }

        return false;

    }


    public function process()
    {
        Phpfox::isUser(true);
        $this->_initVariables();

        if ($this->_checkIsInEditAuction()) {
            //prepare for editing
            $this->_prepareEditForm();
        }

        if (!(int)$this->_iEditedAuctionId) {
            $this->url()->send('auction');
        }

        if (!$this->_checkEditPermission()) {
            $this->url()->send('subscribe');
        }
        $aValidationParam = $this->_getValidationParams();

        $oValid = Phpfox::getLib('validator')->set(array(
                'sFormName' => 'ync_add_auction_form',
                'aParams' => $aValidationParam
            )
        );

        /*check action publish from manage auction*/
        if ($aAction = $this->request()->get('action')) {

            if ($aAction == 'publish') {

                $this->_aEditedAuction['feature_number_days'] = 0;
                $invoice_id = $this->pay($this->_aEditedAuction, $this->_iEditedAuctionId, $this->_aEditedAuction['product_id']);
                if ($invoice_id === true) {

                    $this->url()->send('auction.detail', array($this->_iEditedProductId), _p('your_auction_has_been_updated'));

                } elseif ((int)$invoice_id > 0) {
                    $this->template()->assign(array(
                        'invoice_id' => $invoice_id,
                    ));
                }

            }
        }

        if ($this->_checkIfSubmittingAForm()) {

            $aVals = $this->request()->getArray('val');

            if (isset($aVals['featureinbox'])) {
                $sUrlBack = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->url()->makeUrl('auction');
                /*not in feature or feature is expired*/
                if ($this->_aEditedAuction['feature_end_time'] < PHPFOX_TIME) {
                    $aUpdate['feature_day'] = $aVals['feature_number_days'];
                } else {/*already featured ,wanna expand feature time*/

                    $aUpdate['feature_day'] = $this->_aEditedAuction['feature_day'] + $aVals['feature_number_days'];
                }

                Phpfox::getLib('database')->update(Phpfox::getT('ecommerce_product'), $aUpdate, 'product_id = ' . (int)$this->_iEditedProductId);

                $invoice_id = $this->payFeature($aVals, $this->_iEditedAuctionId, $this->_aEditedAuction['product_id']);
                if ($invoice_id === true) {

                    $this->url()->send('auction.detail', array($this->_iEditedProductId), _p('your_auction_has_been_updated'));

                } elseif ((int)$invoice_id > 0) {
                    $this->template()->assign(array(
                        'invoice_id' => $invoice_id,
                    ));
                } else {
                    $this->url()->send($sUrlBack);
                }
            } else {

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

                    if ($this->_bIsEdit) {
                        if (Phpfox::getService('auction.process')->update($this->_iEditedAuctionId, $aVals)) {
                            if (isset($aVals['publish']) && $aVals['publish'] = 'publish') {
                                $invoice_id = $this->pay($aVals, $this->_iEditedAuctionId, $this->_aEditedAuction['product_id']);
                                if ($invoice_id === true) {

                                    $this->url()->send('auction.detail', array($this->_iEditedProductId), _p('your_auction_has_been_updated'));

                                } elseif ((int)$invoice_id > 0) {
                                    $this->template()->assign(array(
                                        'invoice_id' => $invoice_id,
                                    ));
                                }
                            } elseif (isset($aVals['save']) && $aVals['save'] = 'save') {
                                if ($this->_aEditedAuction['product_status'] == 'running' || $this->_aEditedAuction['product_status'] == 'bidden' || $this->_aEditedAuction['product_status'] == 'approved' || $this->_aEditedAuction['product_status'] == 'pending') {

                                    $invoice_id = $this->payFeature($aVals, $this->_iEditedAuctionId, $this->_aEditedAuction['product_id']);

                                    if ($invoice_id === true) {

                                        $this->url()->send('auction.detail', array($this->_iEditedProductId), _p('your_auction_has_been_updated'));

                                    } elseif ((int)$invoice_id > 0) {
                                        $this->template()->assign(array(
                                            'invoice_id' => $invoice_id,
                                        ));
                                    }

                                } else {
                                    $this->url()->send('auction.detail', array($this->_iEditedProductId), _p('your_auction_has_been_updated'));
                                }
                            } else {
                                $this->url()->send('auction.detail', array($this->_iEditedProductId), _p('your_auction_has_been_updated'));
                            }
                        }
                    }
                }

            }
        }

        if (!isset($invoice_id)) {

            $aCategories = array();//Phpfox::getService('ecommerce.category')->getCategories();
            $aUOMs = Phpfox::getService('ecommerce.uom')->getAll();
            $bCanEditAll = true;
            if ($this->_aEditedAuction['product_status'] == 'running' || $this->_aEditedAuction['product_status'] == 'bidden') {
                $bCanEditAll = false;
            }

            $this->template()->setHeader('cache', array(
                '<script type="text/javascript">
                    $Behavior.ynauctionEditCategory = function(){
                            var aCategories = explode(\',\', \'' . $this->_aEditedAuction['categories'] . '\');
                            for (i in aCategories) {
                                 $(\'#js_mp_holder_\' + aCategories[i]).show();
                                 $(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true);
                            }

                            var iMainCategoryId = $(\'#js_mp_id_0\').val();

                            ynauction.changeCustomFieldByMainCategory(iMainCategoryId,'.$this->_aEditedAuction['auction_id'].');

                    }
                 </script>'
            ));

            $this->template()
                ->setBreadcrumb(_p('auctions'), ($this->_aCallback === false ? $this->url()->makeUrl('auction') : $this->url()->makeUrl($this->_aCallback['url_home_pages'])))
                ->setBreadcrumb(
                    ($this->_bIsEdit ? _p('edit_an_auction') . ': ' . Phpfox::getLib('parse.output')->shorten($this->_aEditedAuction['name'], Phpfox::getService('core')->getEditTitleSize(), '...') : _p('add_new_auction'))
                    , ($this->_iEditedProductId > 0 ? ($this->_aCallback == false ? $this->url()->makeUrl('auction', array('edit', 'id' => $this->_iEditedProductId)) : $this->url()->makeUrl('auction', array('edit', 'id' => $this->_iEditedProductId, 'module' => $this->_aCallback['module_id'], 'item' => $this->_aCallback['item_id']))) : ($this->_aCallback == false ? $this->url()->makeUrl('auction', array('edit')) : $this->url()->makeUrl('auction', array('edit', 'module' => $this->_aCallback['module_id'], 'item' => $this->_aCallback['item_id']))))
                    , true
                )
                ->setEditor(array('wysiwyg' => true));

            $this->template()->assign(array(
                'sCreateJs' => $oValid->createJS(),
                'sGetJsForm' => $oValid->getJsForm(),
                'sFormUrl' => $this->url()->makeUrl('auction.edit') . 'id_' . $this->_iEditedProductId,
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
                'bCanEditAll' => $bCanEditAll,
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

            if (Phpfox::isModule('attachment')) {
                $this->setParam('attachment_share', array(
                        'type' => 'auction',
                        'id' => 'ynauction_edit_auction_form',
                        'edit_id' => ($this->_bIsEdit ? $this->_iEditedAuctionId : 0),
                    )
                );
            }

            $this->template()->assign('bNoAttachaFile', true);

        } else {
            // confirm payment 
            $this->template()->setTitle(_p('review_and_confirm_purchase'))
                ->setBreadcrumb(_p('module_menu'), $this->url()->makeUrl('auction'))
                ->setBreadcrumb(_p('review_and_confirm_purchase'), null, true)
              ;
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
            } else if (!is_array($aVals['category'])) {
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
        return $aAuctionCategories;
    }


}

?>