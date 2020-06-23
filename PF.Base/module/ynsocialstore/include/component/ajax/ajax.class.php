<?php

defined('PHPFOX') or exit('NO DICE!');

class Ynsocialstore_Component_Ajax_Ajax extends Phpfox_Ajax
{
    public function formatMoney() {
        Phpfox::isUser(true);
        $money = $this->get('money');
        $currency = $this->get('currency');

        $response = [
            'status' => false
        ];

        if(isset($money) && isset($currency)) {
            $response = [
                'status' => true,
                'formatted_money' => html_entity_decode(Phpfox::getService('core.currency')->getCurrency($money, $currency))
            ];
            if($sellerMoneyJson = $this->get('seller_money')) {
                $sellerMoney = json_decode($sellerMoneyJson, true);
                $formattedSellerMoney = [];
                foreach($sellerMoney as $sellerId => $seller) {
                    $formattedSellerMoney[$sellerId] = html_entity_decode(Phpfox::getService('core.currency')->getCurrency($seller['money'], $seller['currency']));
                }
                $response['formatted_seller_money'] = $formattedSellerMoney;
            }

            if($productMoneyJson = $this->get('product_money')) {
                $productMoney = json_decode($productMoneyJson, true);
                $response['formatted_product_money'] = html_entity_decode(Phpfox::getService('core.currency')->getCurrency($productMoney['money'], $productMoney['currency']));
            }
        }
        echo json_encode($response);
    }

    public function activepackage()
    {
        Phpfox::isAdmin(true);

        $bActive = $this->get('active');
        $iId = $this->get('id');

        if (Phpfox::getService('ynsocialstore.package.process')->activepackage($iId, $bActive)) {
            if($bActive==1)
            {
                $this->call("$('#showpackage_{$iId}').show();");
                $this->call("$('#hidepackage_{$iId}').hide();");
            }
            else {
                $this->call("$('#showpackage_{$iId}').hide();");
                $this->call("$('#hidepackage_{$iId}').show();");
            }
        }
    }

    public function deletepackage()
    {
        Phpfox::isAdmin(true);

        $iId = $this->get('id');
        if (Phpfox::getService('ynsocialstore.package.process')->delete($iId)) {
            $this->call('window.location.reload();');
        }
    }

    public function deleteStore()
    {
        Phpfox::isUser(true);
        // Get Params
        if(!Phpfox::getService('ynsocialstore.permission')->canDeleteStore(true, $this->get('iOwnerId'))) {
            return false;
        }
        $iStoreId = (int)$this->get('iStoreId');
        if ($iStoreId) {
            Phpfox::getService('ynsocialstore.process')->delete($iStoreId);
        }

        $this->call("$('.mfp-close-btn-in .mfp-close').trigger('click');");
        Phpfox::addMessage(_p('stores_successfully_deleted'));
        if ((bool)$this->get('bIsDetail')) {
            $this->call("window.location.href = '" . Phpfox_Url::instance()->makeUrl('ynsocialstore.store') . "'");
        } else {
            $this->call('window.location.reload();');
        }

    }

    public function denyStore()
    {
        Phpfox::isUser(true);
        // Get Params
        if(!Phpfox::getService('ynsocialstore.permission')->canDenyStore(true, $this->get('sStatus'))) {
            return false;
        }
        $iStoreId = (int)$this->get('iStoreId');
        if ($iStoreId) {
            Phpfox::getService('ynsocialstore.process')->denyStore($iStoreId);
        }

        $this->call('window.location.reload();');
    }

    public function approveStore()
    {
        Phpfox::isUser(true);
        // Get Params
        if(!Phpfox::getService('ynsocialstore.permission')->canApproveStore(true, $this->get('sStatus'))) {
            return false;
        }
        // Get Params
        $iStoreId = (int)$this->get('iStoreId');

        if ($iStoreId) {
            Phpfox::getService('ynsocialstore.process')->approveStore($iStoreId);
        }

        $this->call('window.location.href = window.location.href');
    }

    public function reopenStore()
    {
        Phpfox::isUser(true);
        $onAdmin = $this->get('onAdmin','');
        $iStoreId = (int)$this->get('iStoreId');

        // Get Params
        if(!Phpfox::getService('ynsocialstore.permission')->canReopenStore($this->get('iOwnerId'), $this->get('sStatus'))) {
            $this->alert(_p('you_can_not_reopen_this_store'));
            return false;
        }
        if ($iStoreId) {
            Phpfox::getService('ynsocialstore.process')->reopenStore($iStoreId);
        }
        if(empty($onAdmin)) {
            $this->call('window.location.reload();');
        }
        else{
            $this->call('$("#ynstore_status_'.$iStoreId.'").html("<div class=\"ynstore-status ynstatus_public\">'._p('public').' </div>");$(".ynstore-close-open-store-' . $iStoreId . '").html(" <a href=\"javascript:void(0)\" onclick=\"ynsocialstore.closeStore('.$iStoreId.','.$this->get('iOwnerId').',\'public\'); return false;\"><i class=\"ico ico-close-circle\"></i>' . _p('closed') . ' </a>");');
        }
    }

    public function featureStore()
    {
        Phpfox::isUser(true);
        $onAdmin = $this->get('onAdmin','');
        $iStoreId = (int)$this->get('iStoreId');
        $bIsFeatured = $this->get('bIsFeatured');
        // Get Params

        if(!Phpfox::getService('ynsocialstore.permission')->canFeatureStore(true, $this->get('iOwnerId'), $this->get('sStatus'))) {
            $this->alert(_p('you_do_not_have_permission_to_feature_this_store'));
            return false;
        }

        if ($iStoreId) {
            Phpfox::getService('ynsocialstore.process')->featureStore($iStoreId, $bIsFeatured);
        }

        if(empty($onAdmin)) {
            if (!$bIsFeatured)
            {
                $this->html('#ynstore_feature_store_'.$iStoreId, '<div class="js_item_is_active"><a href="javascript:void(0)" onclick="managestores.featureStore('.$iStoreId .','. $this->get('iOwnerId'). ',\'' .$this->get('sStatus'). '\','. (int)!$bIsFeatured .'); return false;" class="js_item_active_link" ></a></div>');
            }
            else
            {
                $this->html('#ynstore_feature_store_'.$iStoreId, '<div class="js_item_is_not_active"><a href="javascript:void(0)" onclick="managestores.featureStore('.$iStoreId .','. $this->get('iOwnerId'). ',\'' .$this->get('sStatus'). '\','. (int)!$bIsFeatured .'); return false;" class="js_item_active_link" ></a></div>');
            }

        }
        else{
            if(!$bIsFeatured) {
                $this->call('$(".ynstore-feature-store-' . $iStoreId . '").html(\'<a href="javascript:void(0)" onclick="ynsocialstore.updateFeatureStore(this,'.$iStoreId.',1); return false;"><i class="ico ico-diamond"></i>' . _p('un_featured') . ' </a>\');');
                $this->call('$(".ynstore_entry_feature_icon-'.$iStoreId.'").css("visibility","visible")');
            }
            else{
                $this->call('$(".ynstore-feature-store-' . $iStoreId . '").html(\'<a href="javascript:void(0)" onclick="ynsocialstore.updateFeatureStore(this,'.$iStoreId.',0); return false;"><i class="ico ico-diamond-o"></i>' . _p('featured') . ' </a>\');');
                $this->call('$(".ynstore_entry_feature_icon-'.$iStoreId.'").css("visibility","hidden")');
            }
        }
    }

    public function closeStore()
    {
        Phpfox::isUser(true);
        // Get Params
        $onAdmin = $this->get('onAdmin','');
        $iStoreId = (int)$this->get('iStoreId');
        if (!Phpfox::getService('ynsocialstore.permission')->canCloseStore($this->get('iOwnerId'), $this->get('sStatus'))) {
            $this->alert(_p('you_can_not_close_this_store'));
            return false;
        }
        if ($iStoreId) {
            Phpfox::getService('ynsocialstore.process')->closeStore($iStoreId);
        }
        if (empty($onAdmin)) {
            $this->call('window.location.reload();');
        }
        else {
            $this->call('$("#ynstore_status_'.$iStoreId.'").html("<div class=\"ynstore-status ynstatus_closed\">'._p('closed').' </div>");$(".ynstore-close-open-store-' . $iStoreId . '").html(" <a href=\"javascript:void(0)\" onclick=\"ynsocialstore.openStore('.$iStoreId.','.$this->get('iOwnerId').',\'closed\'); return false;\"><i class=\"fa fa-check\"></i>' . _p('open') . ' </a>");');
        }
    }
    public function loadAjaxMapDetail(){

        $iStoreId = $this->get('iStoreId',0);

        $aLocations = Phpfox::getService('ynsocialstore')->getAddressByStoreId($iStoreId);

        echo json_encode(array(
            'status' => 'SUCCESS',
            'sCorePath' => Phpfox::getParam('core.path_actual').'PF.Base/',
            'data' => $aLocations
        ));

    }

    public function repositionCoverPhoto()
    {
        Phpfox::isUser(true);
        if(!Phpfox::getService('ynsocialstore.permission')->canEditStore(true, $this->get('iOwnerId'))) {
            return false;
        }
        
        $iStoreId = $this->get('id');
        if ($iStoreId) {
            Phpfox::getService('ynsocialstore.process')->updatePositionCoverStorePhoto($this->get('position'), $iStoreId);
        }
    }

    public function setStoreSession()
    {
        $type = $this->get('type');
        $iStoreId = $this->get('store_id');
        Phpfox::getService('ynsocialstore.helper')->setSessionBeforeAddItemFromSubmitForm($iStoreId, $type);
        $this->call("window.location.href = $('#ynsocialstore_add_new_item').attr('href');");
    }

    public function coverupload()
    {
        $this->setTitle(_p('cover_photo'));
        $aParams = array(
            'store_id' => $this->get('store_id'),
        );
        Phpfox::getBlock('ynsocialstore.store.coverupload', $aParams);
    }

    public function photogallery()
    {
        $this->setTitle(_p('choose_from_gallery'));

        $aParams = array(
            'store_id' => $this->get('store_id'),
            'type' => $this->get('type'),
        );

        Phpfox::getBlock('ynsocialstore.store.photogallery', $aParams);
    }

    public function loadphotogallery()
    {
        $page = (int)$this->get('page', 0);
        $aParams = array(
            'store_id' => $this->get('store_id'),
            'type' => $this->get('type'),
            'page' => $page,
        );

//        d(array('ajax',$page));
        Phpfox::getBlock('ynsocialstore.store.photogallery', $aParams);
        if ($page > 1)
            $this->call('$(\'#js_store_popup_gallery_content_ajax\').append(\'' . $this->getContent() . '\');');
        else
            $this->call('$(\'#js_store_popup_gallery_content\').html(\'' . $this->getContent() . '\');');
    }

    public function loadmoregallery()
    {
        $page = (int)$this->get('page');

        if (!isset($page))
            return;

        $aParams = array(
            'store_id' => $this->get('store_id'),
            'type' => $this->get('type'),
            'page' => $page,
        );

        Phpfox::getBlock('ynsocialstore.store.loadmoregallery', $aParams);
        $this->call('$(\'#js_store_popup_gallery_content_ajax\').append(\'' . $this->getContent() . '\');');
    }

    public function loadAjaxMapView(){

        $iPage = 0;
        $sType = $this->get('typeStore','');
        $iPage = 0;
        $aConditions = [];
        $iLimit = 6;

        if($sType == 'newstore')
        {
            $iLimit = Phpfox::getParam('ynsocialstore.max_item_block_new_stores',6);
            $aConditions[] = ' AND dbus.status="public" AND dbus.module_id ="ynsocialstore" ';
            $sOrder = 'dbus.time_stamp DESC';
        }
        $aStoresMap = Phpfox::getService('ynsocialstore')->getStoreForMap($aConditions, $sOrder, $iPage, $iLimit);

        $aStoresMap = Phpfox::getService('ynsocialstore.process')->prePareDataForMap($aStoresMap);

        echo json_encode(array(
            'status' => 'SUCCESS', 
            'sCorePath' => Phpfox::getParam('core.path'),
            'data' => $aStoresMap
        ));             

    }
    public function featureStoreInBox()
    {
        $iStoreId = (int) $this->get('iStoreId');
        Phpfox::getBlock('ynsocialstore.store.featureinbox', array('iStoreId' => $iStoreId));
        $this->setTitle(_p('feature_this_store'));
    }

    public function setCoverPhoto()
    {
        $iStoreId = $this->get('page_id');
        $iPhotoId = $this->get('photo_id');

        if (Phpfox::getService('ynsocialstore.process')->setCoverPhoto($iStoreId , $iPhotoId))
        {
            $this->call('window.location.reload();');

        }
    }
    public function updateFavorite()
    {
        $iStoreId = $this->get('iStoreId');
        $bFavorite = $this->get('bFavorite');
        if($bFavorite) {
            Phpfox::getService('ynsocialstore.favourite')->add(Phpfox::getUserId(), $iStoreId);
            $this->call('$("#ynstore-detail-favorite-store-'.$iStoreId.'").html(\'<a class="btn btn-default" onclick="ynsocialstore.updateFavorite('.$iStoreId.',0);return false;">
                    <i class="ico ico-star"></i>&nbsp;'. _p('Favorited').'</a>\');');
        }
        else{
            $this->call("$('#js_store_id_".$iStoreId."').remove();");
            Phpfox::getService('ynsocialstore.favourite')->delete(Phpfox::getUserId(), $iStoreId);
            $this->call('$("#ynstore-detail-favorite-store-'.$iStoreId.'").html(\'<a class="btn btn-default" onclick="ynsocialstore.updateFavorite('.$iStoreId.',1);return false;">
                    <i class="ico ico-star-o"></i>&nbsp;'. _p('Favorite').'</a>\');');
        }

    }
    public function updateFollow()
    {
        $iStoreId = $this->get('iStoreId');
        $bFollowing = $this->get('bFollowing');
        if($bFollowing) {
            Phpfox::getService('ynsocialstore.following')->add(Phpfox::getUserId(), $iStoreId);
            $this->call('$("#ynstore-detail-follow-store-'.$iStoreId.'").html(\'<a class="btn btn-default" onclick="ynsocialstore.updateFollow('.$iStoreId.',0);return false;">
                    <i class="ico ico-check"></i>&nbsp;'. _p('Following').'</a>\');');
            $this->call('$(".js-ynstore-detail-follow-store-'.$iStoreId.'").html(\'<a class="btn btn-default" onclick="ynstoreupdateFollow('.$iStoreId.',0);return false;">
                    <i class="ico ico-check"></i>&nbsp;'. _p('Following').'</a>\');');
        }
        else{
            Phpfox::getService('ynsocialstore.following')->delete(Phpfox::getUserId(), $iStoreId);
            $this->call("$('#js_store_id_".$iStoreId."').remove();");
            $this->call('$("#ynstore-detail-follow-store-'.$iStoreId.'").html(\'<a class="btn btn-primary" onclick="ynsocialstore.updateFollow('.$iStoreId.',1);return false;">
                    <i class="ico ico-plus"></i>&nbsp;'. _p('Follow').'</a>\');');
            $this->call('$(".js-ynstore-detail-follow-store-'.$iStoreId.'").html(\'<a class="btn btn-primary" onclick="ynstoreupdateFollow('.$iStoreId.',1);return false;">
                    <i class="ico ico-plus"></i>&nbsp;'. _p('Follow').'</a>\');');
        }

    }

    public function getUsers()
    {
        switch ($this->get('sType')) {
            case 'favorite':
                $sTitle = _p('favorited_by_users');
                break;
            case 'following':
                $sTitle = _p('following_by_users');
                break;
            case 'friend-bought-this':
                $sTitle = _p('also_bought_by_friends');
                break;
            default:
                break;
        }
        if (!isset($sTitle)) return false;

        $page = (int)$this->get('page', 0);
        Phpfox::getBlock('ynsocialstore.user');
        if ($page > 0)
            $this->call('$(\'#js_content_users\').append(\'' . $this->getContent() . '\');');
        else
            $this->setTitle($sTitle);

    }

    public function addFeedComment()
    {
        Phpfox::isUser(true);

        $aVals = (array) $this->get('val');

        if (Phpfox::getLib('parse.format')->isEmpty($aVals['user_status']))
        {
            $this->alert(_p('user.add_some_text_to_share'));
            $this->call('$Core.activityFeedProcess(false);');
            return;
        }

        $aStore = Phpfox::getService('ynsocialstore')->getStoreForEdit($aVals['callback_item_id']);

        if (!isset($aStore['store_id']))
        {
            $this->alert(_p('unable_to_find_the_store_you_are_trying_to_comment_on'));
            $this->call('$Core.activityFeedProcess(false);');
            return;
        }

        $sLink = Phpfox::permalink('ynsocialstore.store', $aStore['store_id'], Phpfox::getLib('parse.input')->cleanTitle($aStore['name']));
        $aCallback = array(
            'module' => 'ynsocialstore',
            'table_prefix' => 'ynstore_',
            'link' => $sLink,
            'email_user_id' => $aStore['user_id'],
            'subject' => _p('full_name_wrote_a_comment_on_your_store_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aStore['name'])),
            'message' => _p('full_name_wrote_a_comment_on_your_store_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aStore['name'])),
            'notification' => 'ynsocialstore_comment',
            'feed_id' => 'ynsocialstore_comment',
            'item_id' => $aStore['store_id']
        );

        $aVals['parent_user_id'] = $aVals['callback_item_id'];
        if (!empty(!empty($aVals['location']['latlng']))) {
            $latlng = explode(',', $aVals['location']['latlng']);
            if (count($latlng) == 2) {
                $aVals['location_latlng'] = json_encode(array(
                    'latitude' => $latlng[0],
                    'longitude' => $latlng[1],
                ));
            }
        }
        $aVals['location_name'] = !empty($aVals['location']['name']) ? $aVals['location']['name'] : null;

        if (isset($aVals['user_status']) && ($iId = Phpfox::getService('feed.process')->callback($aCallback)->addComment($aVals)))
        {
            Phpfox_Database::instance()->updateCounter('ynstore_store', 'total_comment', 'store_id', $aStore['store_id']);

            Phpfox::getService('feed')->callback($aCallback)->processAjax($iId);
        }
        else
        {
            $this->call('$Core.activityFeedProcess(false);');
        }
    }

    public function AddFaqStoreBlock(){
        $faq_id = $this->get('faq_id');
        $store_id = $this->get('store_id');
        Phpfox::getComponent('ynsocialstore.store.add-faq', array('faq_id' => $faq_id,'store_id' => $store_id));
    }
    public function addFaq(){

        $faq_id = $this->get('faq_id');
        $store_id = $this->get('store_id');
        $answer = $this->get('answer');
        $question = trim($this->get('question'));
        $isHide = $this->get('disable','');

        if($answer != '' &&  $question != ''){
            Phpfox::getService('ynsocialstore.process')->saveFAQForStore($answer,$question,$store_id,$faq_id,$isHide);
            $this->call("location.reload();");
        }
        else{
            $this->call('$(\'#js_add_faq_page #message\').html(\''._p('Please fill input data').'\');');
        }
    }
    public function deleteFAQ(){
        $faq_id = $this->get('faq_id');
        Phpfox::getService('ynsocialstore.process')->deleteFaq($faq_id);
        $this->call("location.reload();");

    }

    public function upgradePackages()
    {
//        Phpfox::getComponent('ynsocialstore.store.upgrade-packages', array(), 'controller');

        $package_id = (int)$this->get('package_id');
        $iId = (int)$this->get('store_id');
        $aPackage = Phpfox::getService('ynsocialstore.package')->getById($package_id);
        $aStore = Phpfox::getService('ynsocialstore')->getStoreById($iId);
        if(isset($aPackage['package_id']) == false || (int)$aPackage['active'] != 1 || !$iId){
            // do nothing
            $this->call('window.location.reload();');
        } else {
            $aCurrentCurrencies = Phpfox::getService('ynsocialstore.helper')->getCurrentCurrencies();
            $currency_id = $aCurrentCurrencies[0]['currency_id'];
            $packageFee = doubleval( $aPackage['fee']);
            $fFee =  $packageFee;
            if($fFee > 0){
                // add invoice
                $iInvoice = Phpfox::getService('ynsocialstore.process')->addInvoice($iId,$currency_id, $fFee, 'store', array(
                    'pay_type' => ($packageFee > 0 ? 'package' : ''),
                    'aPackage' => $aPackage,
                    'feature_days' => 0,
                    'change_package_id' => $package_id,
                ), 'store');

                $aPurchase = Phpfox::getService('ynsocialstore')->getInvoice($iInvoice);

                // process payment
                if (empty($iInvoice['status'])){
                    $params = array(
                        'gateway_data' => array(
                            'item_number' => 'ynsocialstore|' . $aPurchase['invoice_id'],
                            'currency_code' => $aPurchase['default_currency_id'],
                            'amount' => $aPurchase['default_cost'],
                            'item_name' => ($packageFee > 0 ? $aPackage['name'] : ''),
                            'return' => Phpfox_Url::instance()->permalink('ynsocialstore.store', $iId, $aStore['name']). 'storepayment_done/',
                            'recurring' => '',
                            'recurring_cost' => '',
                            'alternative_cost' => '',
                            'alternative_recurring_cost' => ''
                        )
                    );

                    Phpfox::getBlock('api.gateway.form', $params);
                    $this->call('$(\'#manage_packages_page\').html(\''.addslashes($this->getContent(false)).'\')');
                    return false;
                }
            } else {
                // pay zero fee - package
                if(Phpfox::getService('ynsocialstore.helper')->getUserParam('ynsocialstore.auto_approved_store',(int)Phpfox::getUserId())){
                    $status = 'public';
                } else {
                    $status = 'pending';
                }
                Phpfox::getService('ynsocialstore.process')->updatePackageForStore($package_id, $iId);
                Phpfox::getService('ynsocialstore.process')->updateStoreStatus($iId, $status);

                if($status == 'public'){
                    // call approve function
                    Phpfox::getService('ynsocialstore.process')->approveStoreByPackage($iId, null);
                }

                $this->call("location.reload();");
            }
        }
    }
    function updateCompareBar()
    {
        Phpfox::getBlock('ynsocialstore.compare');
        $this->html('#ynstore-compare-dashboard',$this->getContent(false));
    }
    public function getCharts()
    {
        $aVals = $this->get('val');
        $sType = $this->get('sType');
        $sFromDatePicker = $this->get('js_from__datepicker');
        $sToDatePicker = $this->get('js_to__datepicker');

        if (empty($sFromDatePicker))
        {
            Phpfox_Error::set(_p('ecommerce.from_date_is_not_valid'));
        }
        if (empty($sToDatePicker))
        {
            Phpfox_Error::set(_p('ecommerce.to_date_is_not_valid'));
        }

        $iFromTimestamp = 0;
        $iToTimestamp = 0;

        if ($aVals && !empty($sFromDatePicker) && !empty($sToDatePicker))
        {
            // 11th December, 2010
            $sFromDate = $aVals['from_day'] . '-' . $aVals['from_month'] . '-' . $aVals['from_year'];
            $sToDate = $aVals['to_day'] . '-' . $aVals['to_month'] . '-' . $aVals['to_year'];

            $iFromTimestamp = strtotime($sFromDate);
            $iToTimestamp = strtotime($sToDate);
            if ($iFromTimestamp > $iToTimestamp)
            {
                Phpfox_Error::set(_p('ecommerce.from_date_must_be_less_than_to_date'));
            }
        }
        if(isset($aVals['store_id']))
        {
            $store_id = $aVals['store_id'];
        }
        else{
            $store_id = 0;
        }
        if (Phpfox_Error::isPassed())
        {
            Phpfox::getBlock('ynsocialstore.store.insight-charts', array('iFromTimestamp' => $iFromTimestamp, 'iToTimestamp' => $iToTimestamp,'sType' => $sType,'iStoreId' => $store_id));
            $this->html('#charts_holder', $this->getContent(false));
            $this->call("$('#charts_holder').show();");
        }

        $this->call('$("#statistic_button").prop("disabled", false);');
        $this->call('$("#charts_loading").hide();');
        $this->errorSet('.insight_store_search_message');
        $this->call('$Core.loadInit();');
    }

    public function moderation()
    {
        Phpfox::isUser(true);

        switch ($this->get('action'))
        {
            case 'approve':
                if(!Phpfox::getUserParam('ynsocialstore.can_approve_store'))
                {
                    $sMessage = _p('You do not have permission on this action');
                    break;
                }
                foreach ((array) $this->get('item_moderate') as $iId)
                {
                    Phpfox::getService('ynsocialstore.process')->approveStore($iId);
                }
                $sMessage = _p('stores_successfully_approved');
                break;
            case 'delete':
                if(!Phpfox::getUserParam('ynsocialstore.can_delete_product_of_other_users'))
                {
                    $sMessage = _p('You do not have permission on this action');
                    break;
                }
                foreach ((array) $this->get('item_moderate') as $iId)
                {
                    Phpfox::getService('ynsocialstore.process')->delete($iId);
                }
                $sMessage = _p('stores_successfully_deleted');
                break;
            case 'feature':
                if(!Phpfox::getUserParam('ynsocialstore.can_feature_store'))
                {
                    $sMessage = _p('You do not have permission on this action');
                    break;
                }
                foreach ((array) $this->get('item_moderate') as $iId)
                {
                    Phpfox::getService('ynsocialstore.process')->featureStore($iId, 0);
                }
                $sMessage = _p('stores_successfully_featured');
                break;
            case 'unfeature':
                if(Phpfox::getUserParam('ynsocialstore.can_feature_store'))
                {
                    $sMessage = _p('You do not have permission on this action');
                    break;
                }
                foreach ((array) $this->get('item_moderate') as $iId)
                {
                    Phpfox::getService('ynsocialstore.process')->featureStore($iId, 1);
                }
                $sMessage = _p('stores_successfully_un_featured');
                break;
            case 'deny':
                if(!Phpfox::getUserParam('ynsocialstore.can_approve_store'))
                {
                    $sMessage = _p('You do not have permission on this action');
                    break;
                }
                foreach ((array) $this->get('item_moderate') as $iId)
                {
                    Phpfox::getService('ynsocialstore.process')->denyStore(Phpfox::getUserId(),$iId);
                }
                $sMessage = _p('stores_successfully_denied');
                break;
            case 'deletePhoto':
                foreach ((array) $this->get('item_moderate') as $iId)
                {
                    Phpfox::getService('photo.process')->delete($iId);
                    $this->remove('#js_photo_id_' . $iId);
                }
                $sMessage = _p('photo_s_successfully_deleted');
                $this->alert($sMessage, _p('moderation'), 300, 150, true);
                break;
            case 'deleteAlbum':
                Phpfox::getUserParam('photo.can_delete_other_photos', true);
                foreach ((array) $this->get('item_moderate') as $iId)
                {
                    Phpfox::getService('photo.album.process')->delete($iId);
                    $this->remove('#js_album_id_' . $iId);
                }
                $sMessage = _p('album_s_successfully_deleted');
                $this->alert($sMessage, _p('moderation'), 300, 150, true);
                break;
            case 'closeStore':
                foreach ((array) $this->get('item_moderate') as $iId)
                {
                    $aStore = Phpfox::getService('ynsocialstore')->getStoreById($iId);
                    if (!empty($aStore['user_id']) && Phpfox::getService('ynsocialstore.permission')->canCloseStore($aStore['user_id'], $aStore['status'])) {
                        Phpfox::getService('ynsocialstore.process')->closeStore($iId);
                    }
                }
                $sMessage = _p('stores_successfully_closed');
                break;
            case 'reopenStore':
                foreach ((array) $this->get('item_moderate') as $iId)
                {
                    $aStore = Phpfox::getService('ynsocialstore')->getStoreById($iId);
                    if (!empty($aStore['user_id']) && Phpfox::getService('ynsocialstore.permission')->canReopenStore($aStore['user_id'], $aStore['status'])) {
                        Phpfox::getService('ynsocialstore.process')->reopenStore($iId);
                    }
                }
                $sMessage = _p('stores_successfully_opened');
                break;
            case 'unfavorite':
                foreach ((array) $this->get('item_moderate') as $iId)
                {
                    Phpfox::getService('ynsocialstore.favourite')->delete(Phpfox::getUserId(), $iId);
                }
                $sMessage = _p('stores_successfully_un_favorite');
                break;
            case 'unfollow':
                foreach ((array) $this->get('item_moderate') as $iId)
                {
                    Phpfox::getService('ynsocialstore.following')->delete(Phpfox::getUserId(), $iId);
                }
                $sMessage = _p('stores_successfully_un_follow');
                break;
            //============PRODUCT SECTION===========
            case 'deleteProduct':
                if(!Phpfox::getUserParam('ynsocialstore.can_delete_product_of_other_users'))
                {
                    $sMessage = _p('You do not have permission on this action');
                    break;
                }
                foreach ((array) $this->get('item_moderate') as $iId)
                {
                    Phpfox::getService('ynsocialstore.product.process')->delete($iId);
                }
                $sMessage = _p('products_successfully_deleted');
                break;
            case 'approveProduct':
                if(!Phpfox::getUserParam('ynsocialstore.can_approve_product'))
                {
                    $sMessage = _p('You do not have permission on this action');
                    break;
                }
                foreach ((array) $this->get('item_moderate') as $iId)
                {
                    Phpfox::getService('ynsocialstore.product.process')->approveProduct($iId);
                }
                $sMessage = _p('products_successfully_approved');
                break;
            case 'denyProduct':
                if(!Phpfox::getUserParam('ynsocialstore.can_approve_product'))
                {
                    $sMessage = _p('You do not have permission on this action');
                    break;
                }
                foreach ((array) $this->get('item_moderate') as $iId)
                {
                    Phpfox::getService('ynsocialstore.product.process')->denyProduct($iId);
                }
                $sMessage = _p('products_successfully_denied');
                break;
            case 'unfeatureProduct':
                if(!Phpfox::getUserParam('ynsocialstore.can_approve_product'))
                {
                    $sMessage = _p('You do not have permission on this action');
                    break;
                }
                foreach ((array) $this->get('item_moderate') as $iId)
                {
                    Phpfox::getService('ynsocialstore.product.process')->featureProduct($iId, 1);
                }
                $sMessage = _p('products_successfully_un_featured');
                break;
        }

        $this->alert($sMessage, 'Moderation', 300, 150, true);
        $this->hide('.moderation_process');
        $this->call('setTimeout(function(){ window.location.href = window.location.href; },3000);');
    }

    public function changeReNewBefore()
    {
        Phpfox::isUser(true);
        if(!Phpfox::getService('ynsocialstore.permission')->canEditStore(true, Phpfox::getUserId())) {
            return false;
        }

        Phpfox::getService('ynsocialstore.process')->updateReNewNotificationBefore($this->get('store_id'), $this->get('number_of_days'));

        $this->alert(_p('receive_renewal_notification_successfully_updated'), null, 400, true);
    }

    public function featureProduct()
    {
        Phpfox::isUser(true);
        $aCore = $this->get('core');

        $onAdmin = !empty($aCore['is_admincp']) ? $aCore['is_admincp'] : 0;

        $iProductId = (int)$this->get('iProductId');
        $bIsFeatured = $this->get('bIsFeatured');
        // Get Params

        if(!Phpfox::getService('ynsocialstore.permission')->canFeatureProduct(true, $this->get('iOwnerId'), $this->get('sStatus'))) {
            $this->alert(_p('you_do_not_have_permission_to_feature_this_product'));
            return false;
        }

        if ($iProductId) {
            Phpfox::getService('ynsocialstore.product.process')->featureProduct($iProductId, $bIsFeatured);
        }

        if($onAdmin) {
            if (!$bIsFeatured)
            {
                $this->html('#ynstore_feature_product_'.$iProductId, '<div class="js_item_is_active"><a href="javascript:void(0)" onclick="manageproducts.featureProduct('.$iProductId .','. $this->get('iOwnerId'). ',\'' .$this->get('sStatus'). '\',' . (int)!$bIsFeatured .'); return false;" class="js_item_active_link" ></a></div>');
            }
            else
            {
                $this->html('#ynstore_feature_product_'.$iProductId, '<div class="js_item_is_not_active"><a href="javascript:void(0)" onclick="manageproducts.featureProduct('.$iProductId .','. $this->get('iOwnerId'). ',\'' .$this->get('sStatus'). '\','. (int)!$bIsFeatured .'); return false;" class="js_item_active_link" ></a></div>');
            }

        }
        else{
            if(!$bIsFeatured) {
                $this->call('$(".ynstore-feature-product-' . $iProductId . '").html(\'<a href="javascript:void(0)" onclick="ynsocialstore.updateFeatureProduct(this,'.$iProductId.',1); return false;"><i class="ico ico-diamond"></i>' . _p('un_featured') . ' </a>\');');
                $this->call('$(".ynstore_entry_feature_icon-'.$iProductId.'").css("visibility","visible");');
                $this->call('$("#ynstore_product_detail_feature").show()');
            }
            else{
                $this->call('$(".ynstore-feature-product-' . $iProductId . '").html(\'<a href="javascript:void(0)" onclick="ynsocialstore.updateFeatureProduct(this,'.$iProductId.',0); return false;"><i class="ico ico-diamond-o"></i>' . _p('featured') . ' </a>\');');
                $this->call('$(".ynstore_entry_feature_icon-'.$iProductId.'").css("visibility","hidden");');
                $this->call('$("#ynstore_product_detail_feature").hide();');
            }
        }
    }

    public function deleteProduct()
    {
        Phpfox::isUser(true);
        // Get Params
        if(!Phpfox::getService('ynsocialstore.permission')->canDeleteProduct(true, $this->get('iOwnerId'))) {
            return false;
        }

        $iProductId = (int)$this->get('iProductId');

        if ($iProductId) {
            Phpfox::getService('ynsocialstore.product.process')->delete($iProductId);
        }

        $this->call("$('.mfp-close-btn-in .mfp-close').trigger('click');");

        if ((bool)$this->get('bIsDetail')) {
            $this->call("window.location.href = '" . Phpfox_Url::instance()->makeUrl('ynsocialstore') . "'");
        } else {
            $this->call('window.location.reload();');
        }

    }

    public function closeProduct()
    {
        Phpfox::isUser(true);
        // Get Params
        $aCore = $this->get('core');

        $onAdmin = !empty($aCore['is_admincp']) ? $aCore['is_admincp'] : 0;

        $iProductId = (int)$this->get('iProductId');
        if (!Phpfox::getService('ynsocialstore.permission')->canCloseProduct($this->get('iOwnerId'), $this->get('sStatus'))) {
            $this->alert(_p('you_cannot_close_this_product'));
            return false;
        }
        if ($iProductId) {
            Phpfox::getService('ynsocialstore.product.process')->closeProduct($iProductId);
        }
        if ($onAdmin) {
            $this->call('setTimeout(function() {window.location.href = window.location.href},500);');
        }
        else {
            if ($this->get('sType', '') == 'manage') {
                $this->call('$("#js_product_status_' . $iProductId . '").html("' . _p('closed') . '");$("#ynstore_status_' . $iProductId . '").html("<div class=\"ynstore-status ynstatus_closed\">' . _p('closed') . ' </div>");$(".ynstore-close-open-product-' . $iProductId . '").html(" <a href=\"javascript:void(0)\" onclick=\"ynsocialstore.openProduct(' . $iProductId . ',' . $this->get('iOwnerId') . ',\'paused\',\'manage\'); return false;\">' . _p('open') . ' </a>");');
            } else {
                $this->call('$("#ynstore_status_' . $iProductId . '").html("<div class=\"ynstore-status ynstatus_closed\">' . _p('closed') . ' </div>");$(".ynstore-close-open-product-' . $iProductId . '").html(" <a href=\"javascript:void(0)\" onclick=\"ynsocialstore.openProduct(' . $iProductId . ',' . $this->get('iOwnerId') . ',\'paused\'); return false;\"><i class=\"fa fa-check\"></i>' . _p('open') . ' </a>");');
            }
        }
    }

    public function reopenProduct()
    {
        Phpfox::isUser(true);
        $onAdmin = $this->get('onAdmin','');
        $iProductId = (int)$this->get('iProductId');

        // Get Params
        if(!Phpfox::getService('ynsocialstore.permission')->canReopenProduct($this->get('iOwnerId'), $this->get('sStatus'))) {
            $this->alert(_p('you_can_not_reopen_this_product'));
            return false;
        }
        if ($iProductId) {
            Phpfox::getService('ynsocialstore.product.process')->reopenProduct($iProductId);
        }
        if(empty($onAdmin)) {
            $this->call('setTimeout(function() {window.location.href = window.location.href},500);');
        }
        else{
            if($this->get('sType', '') == 'manage'){
                $this->call('$("#js_product_status_' . $iProductId . '").html("' . _p('public') . '");$("#ynstore_status_'.$iProductId.'").html("<div class=\"ynstore-status ynstatus_public\">'._p('public').' </div>");$(".ynstore-close-open-product-' . $iProductId . '").html(" <a href=\"javascript:void(0)\" onclick=\"ynsocialstore.closeProduct('.$iProductId.','.$this->get('iOwnerId').',\'running\',\'manage\'); return false;\">' . _p('close') . ' </a>");');
            }
            else{
                $this->call('$("#ynstore_status_'.$iProductId.'").html("<div class=\"ynstore-status ynstatus_public\">'._p('public').' </div>");$(".ynstore-close-open-product-' . $iProductId . '").html(" <a href=\"javascript:void(0)\" onclick=\"ynsocialstore.closeProduct('.$iProductId.','.$this->get('iOwnerId').',\'running\'); return false;\"><i class=\"fa fa-times\"></i>' . _p('close') . ' </a>");');
            }
        }
    }

    public function approveProduct()
    {
        Phpfox::isUser(true);
        // Get Params
        if(!Phpfox::getService('ynsocialstore.permission')->canApproveProduct(true, $this->get('sStatus'))) {
            return false;
        }
        // Get Params
        $iProductId = (int)$this->get('iProductId');

        if ($iProductId) {
            Phpfox::getService('ynsocialstore.product.process')->approveProduct($iProductId);
        }

        $this->call('window.location.href = window.location.href');
    }

    public function denyProduct()
    {
        Phpfox::isUser(true);
        // Get Params
        if(!Phpfox::getService('ynsocialstore.permission')->canDenyProduct(true, $this->get('sStatus'))) {
            return false;
        }
        // Get Params
        $iProductId = (int)$this->get('iProductId');

        if ($iProductId) {
            Phpfox::getService('ynsocialstore.product.process')->denyProduct($iProductId);
        }

        $this->call('window.location.href = window.location.href');
    }
    public function changeCustomFieldByCategory()
    {
        $oRequest = Phpfox::getLib('request');
        $iMainCategoryId = $oRequest->get('iCategoryId');
        $aCustomFields = Phpfox::getService('ecommerce')->getCustomFieldByCategoryId($iMainCategoryId);

        $keyCustomField = array();



        if($this->get('iProductId')){
            $aCustomDataTemp = Phpfox::getService('ecommerce.custom')->getCustomFieldByProductId($this->get('iProductId'),'ynsocialstore_product');

            if(count($aCustomFields)){
                foreach ($aCustomFields as $key => $aField) {
                    foreach ($aCustomDataTemp as $aFieldValue) {
                        if($aField['field_id'] == $aFieldValue['field_id']){
                            $aCustomFields[$key]['value'] = $aFieldValue['value'];
                            $aCustomFields[$key]['product_id'] = $aFieldValue['product_id'];
                            $aCustomFields[$key]['group_phrase_var_name'] = $aFieldValue['group_phrase_var_name'];
                        }
                    }
                }
            }

        }


        Phpfox::getBlock('ynsocialstore.custom.form', array(
            'aCustomFields' => $aCustomFields,
        ));
        // FAILURE/SUCCESS
        echo json_encode(array(
                             'status' => 'SUCCESS',
                             'content' => $this->getContent(false)
                         ));

    }
    public function changeStoreInAddProduct()
    {
        $iStoreId = $this->get('iStoreId');
        $iCurrencyId = $this->get('iCurrencyId');
        if (!$iStoreId) {
            echo json_encode(['status' => 'FAILURE']);
        } else {
            $aStore = Phpfox::getService('ynsocialstore')->getStoreById($iStoreId);
            $aPackage = Phpfox::getService('ynsocialstore.package')->getById($aStore['package_id']);
            if($aPackage)
            {
                $sText = _p('feature_this_product_symbol_feature_fee_day',['symbol'=>$iCurrencyId,'feature_fee'=>$aPackage['feature_product_fee']]);
                echo json_encode(['status' =>'SUCCESS', 'content' => $sText,'fee' => $aPackage['feature_product_fee']]);
            }
            else{
                echo json_encode(['status' => 'FAILURE']);
            }
        }
    }
    public function deleteReview()
    {
        $iReviewId = $this->get('iReviewId');
        if($iReviewId)
        {
            if(Phpfox::getService('ynsocialstore.reviews')->deleteReview($iReviewId,$this->get('iStoreId')))
            {
                $this->call('setTimeout(function() {window.location.href = window.location.href},100);');
            }
        }
    }

    public function addLike(){

        Phpfox::isUser(true);
        $iItemId = $this->get('item_id');
        $sTypeId = $this->get('type_id');

        if (Phpfox::getService('like.process')->add($sTypeId, $iItemId))
        {
            Phpfox::getLib('database')->updateCount('like', 'type_id = \'ynsocialstore_product\' AND item_id = ' . (int) ($this->get('item_id')) . '', 'total_like', 'ecommerce_product', 'product_id = ' . (int) ($this->get('item_id')));
        }

        $aProduct = Phpfox::getService('ynsocialstore.product')->getProductById($iItemId);
        $like_phrase = $aProduct['total_like'] == 1 ? _p('like'): _p('likes');
        $this->html('#ynstore_count_like_'.$iItemId, $aProduct['total_like'].' '.$like_phrase);
        $this->html('#ynstore_product_like_'.$iItemId, '<a href="javascript:void(0)" onclick="$.ajaxCall(\'ynsocialstore.deleteLike\', \'type_id=ynsocialstore_product&amp;item_id='.$iItemId.'\'); return false;"><i class="ico ico-thumbup"></i> '._p('liked').'</a>');
    }


    public function deleteLike(){

        Phpfox::isUser(true);
        $iItemId = $this->get('item_id');
        $sTypeId = $this->get('type_id');

        if (Phpfox::getService('like.process')->delete($sTypeId, $iItemId))
        {
            Phpfox::getLib('database')->updateCount('like', 'type_id = \'ynsocialstore_product\' AND item_id = ' . (int) ($this->get('item_id')) . '', 'total_like', 'ecommerce_product', 'product_id = ' . (int) ($this->get('item_id')));
        }
        $aProduct = Phpfox::getService('ynsocialstore.product')->getProductById($iItemId);
        $like_phrase = $aProduct['total_like'] == 1 ? _p('like'): _p('likes');
        $this->html('#ynstore_count_like_'.$iItemId, $aProduct['total_like'].' '.$like_phrase);
        $this->html('#ynstore_product_like_'.$iItemId, '<a href="javascript:void(0)" onclick="$.ajaxCall(\'ynsocialstore.addLike\', \'type_id=ynsocialstore_product&amp;item_id='.$iItemId.'\'); return false;"><i class="ico ico-thumbup-o"></i> '._p('like').'</a>');
    }

    public function featureProductInBox()
    {
        $iProductId = (int) $this->get('iProductId');
        Phpfox::getBlock('ynsocialstore.product.feature-inbox', array('iProductId' => $iProductId));
        $this->setTitle(_p('feature_this_product'));
    }

    public function deleteReviewProduct()
    {
        $iReviewId = $this->get('iReviewId');
        if($iReviewId)
        {
            if(Phpfox::getService('ynsocialstore.product.reviews')->deleteReview($iReviewId,$this->get('iProductId')))
            {
                $this->call('setTimeout(function() {window.location.href = window.location.href},100);');
            }
        }
    }
    public function loadmoreProductReview()
    {
        $iPage = (int)$this->get('iPage');
        $iProductId = (int)$this->get('iProductId');
        Phpfox::getBlock('ynsocialstore.product.loadmore-review',['iReviewPage' => $iPage,'iProductId' => $iProductId]);
        $this->call('$(\'#ynstore_detail_reviews\').append(\'' . $this->getContent(false) . '\');');
    }

    public function deleteImage()
    {
        $id = $this->get('id'); //image_id
        $is_main = $this->get('is_main');
        $iProductId = $this->get('pId');
        if (Phpfox::getService('ecommerce.process')->deleteImage($id)) {
            $iNewImage = 0;
            if (!empty($is_main) && !empty($iProductId)) {
                Phpfox::getService('ynsocialstore.product.process')->removeMainPhoto($iProductId);

                // Set another photo to cover
                $aImages = Phpfox::getService('ecommerce')->getImages($iProductId, 1);
                if (count($aImages) > 0) {
                    Phpfox::getService('ynsocialstore.product')->setMainProductPhoto($iProductId, $aImages[0]['image_id']);
                    $iNewImage = $aImages[0]['image_id'];
                }
            }

            $this->call('$("#js_photo_holder_' . $id . '").remove(); onAfterDeletePhotoSuccess(' . $iNewImage . ');');
        } else {
            $this->alert(_p('fail_to_delete_this_photo'));
        }
    }

    public function updateWishlist()
    {
        $iProductId = $this->get('iProductId');
        $bWishlist = $this->get('bWishlist');
        if ($bWishlist) {
            Phpfox::getService('ynsocialstore.product.wishlist')->add(Phpfox::getUserId(), $iProductId);
            if($this->get('iNotDetail','')){
                $this->call('$(".js-ynstore-wishlist-product-' . $iProductId . '").html(\'<a class="ynstore-compare-wishlist active" onclick="ynsocialstore.updateWishList(' . $iProductId . ',0,true);return false;">
                    <span><i class="ico ico-heart"></i></span></a>\');');
            }
            else {
                $this->call('$("#ynstore-detail-wishlist-product-' . $iProductId . '").html(\'<a class="ynstore-compare-wishlist active" onclick="ynsocialstore.updateWishList(' . $iProductId . ',0);return false;">
                    <span><i class="ico ico-heart"></i></span>' . _p('wishlist') . '</a>\');');
            }
        } else {
            Phpfox::getService('ynsocialstore.product.wishlist')->delete(Phpfox::getUserId(), $iProductId);
            if ($this->get('iNotDetail', '')) {
                $this->call('$(".js-ynstore-wishlist-product-' . $iProductId . '").html(\'<a class="ynstore-compare-wishlist" onclick="ynsocialstore.updateWishList(' . $iProductId . ',1,true);return false;">
                    <span><i class="ico ico-heart"></i></span></a>\');');
            } else {
                $this->call('$("#ynstore-detail-wishlist-product-' . $iProductId . '").html(\'<a class="ynstore-compare-wishlist" onclick="ynsocialstore.updateWishList(' . $iProductId . ',1);return false;">
                    <span><i class="ico ico-heart"></i></span>' . _p('wishlist') . '</a>\');');
            }
        }
    }

    public function setMainProductPhoto()
    {
        Phpfox::isUser(true);
        // Get Params
        if(!Phpfox::getService('ynsocialstore.permission')->canDeleteStore(true, $this->get('iOwnerId'))) {
            return false;
        }

        $iProductId = $this->get('iProductId');
        $iPhotoId = $this->get('iPhotoId');

        if ($iProductId && $iPhotoId) {
            if (Phpfox::getService('ynsocialstore.product')->setMainProductPhoto($iProductId, $iPhotoId)) {
                $this->alert(_p('successfully_set_main_photo_for_this_product'));
            }
        }
    }

    public function addAttributeElement(){
        $iElementId = $this->get('element_id');
        $iProductId = $this->get('product_id');
        Phpfox::getComponent('ynsocialstore.product.add-attribute-element', array('iElementId' => $iElementId,'iProductId' => $iProductId));
    }

    public function deleteElementAttr()
    {
        $iProductId = $this->get('iProductId');
        $iAttributeId = $this->get('iElementId');

        if (!empty($iProductId) && !empty($iAttributeId))
        {
            $iOwnerId = Phpfox::getService('ynsocialstore.product')->getOwnerId($iProductId);

            if (!$iOwnerId || !Phpfox::getService('ynsocialstore.permission')->canEditProduct(true, $iOwnerId)) {
                return false;
            }

            Phpfox::getService('ynsocialstore.product.process')->deleteElementAttr($iAttributeId);
            Phpfox::addMessage(_p('An element has been successfully deleted'));
        }
        else
        {
            Phpfox::addMessage(_p('An element not found or you do not have permission to deleted this element'));
        }

        $this->call('window.location.reload();');
    }
    public function changePageManageProducts(){
        $iPage = $this->get('page');
        $iModeView = $this->get('mode');

        $aSearch = array(
            'title'     => $this->get('title'),
            'category_id'  => $this->get('category_id'),
            'status'     => $this->get('status'),
        );
        Phpfox::getBlock('ynsocialstore.store.list-products', array('page' => $iPage,'search' => $aSearch,'iStoreId' => $this->get('iStoreId')));
        $this->html('#ynstore_store_manage_product', $this->getContent(false));
        $this->call('$Core.loadInit();');
    }
    public function updateCategoryInCompare()
    {
        $iCategoryId = $this->get('categoryId');
        Phpfox::getBlock('ynsocialstore.product.categories-compare',['id'=>$iCategoryId]);
        $this->html('#js_ynstore_categories_compare_select',$this->getContent(false));
        $this->call('$Core.loadInit();');
    }
    public function addToCartDigital()
    {
        Phpfox::isUser(true);
        $iProductId = $this->get('iProductId');
        if(!$iProductId)
        {
            $this->alert(_p('invalid_param'));
            return false;
        }
        else{
            if($this->get('type') == "buynow"){
                $aProduct = Phpfox::getService('ynsocialstore.product')->getProductById($iProductId);
                if (!$aProduct) {
                    $this->alert(_p('unable_to_find_the_product_you_are_looking_for'));
                    return false;
                }
                elseif ($aCartProduct = Phpfox::getService('ynsocialstore.product')->checkProductIsInCart($iProductId, (int)Phpfox::getUserId())) {
                    $sLink = Phpfox_Url::instance()->makeUrl('ynsocialstore.checkout',['id' => $aCartProduct['cartproduct_id']]);
                    $this->call('window.location.href ="' . $sLink . '"');
                    return true;
                }
                elseif ($aOrderProduct = Phpfox::getService('ynsocialstore.product')->checkIsBuyThisProduct($iProductId)) {
                    $this->alert(_p('you_already_bought_this_digital_product_please_go_to_detail_of_this_product_to_get_download_link'));
                    return false;
                }
                $aInsert = [
                    'product_id' => $iProductId,
                    'quantity' => 1,
                    'price' => Phpfox::getService('ynsocialstore.product')->getProductDiscountPrice($iProductId),
                    'product_data' => $aProduct,
                    'type' => 'buy',
                    'currency' => $aProduct['creating_item_currency'],
                    'module' => 'ynsocialstore'
                ];
                if ($iCartId = Phpfox::getService('ynsocialstore.product')->checkHavingCart((int)Phpfox::getUserId())) {
                    Phpfox::getService('ecommerce.cart.process')->edit((int)Phpfox::getUserId());
                    $aInsert['cart_id'] = $iCartId;
                    $iCartProductId = Phpfox::getService('ecommerce.cart.process')->addProducts($aInsert);
                    $this->updateMyCartBar();
                    $sLink = Phpfox_Url::instance()->makeUrl('ynsocialstore.checkout',['id' => $iCartProductId]);
                    $this->call('window.location.href ="' . $sLink . '";');
                    return true;
                } else {
                    $aInsert['cart_id'] = Phpfox::getService('ecommerce.cart.process')->add(['user_id' => (int)Phpfox::getUserId()]);
                    $iCartProductId = Phpfox::getService('ecommerce.cart.process')->addProducts($aInsert);
                    $this->updateMyCartBar();
                    $sLink = Phpfox_Url::instance()->makeUrl('ynsocialstore.checkout',['id' => $iCartProductId]);
                    $this->call('window.location.href ="' . $sLink . '";');
                    return true;
                }
            }
            else {
                $aProduct = Phpfox::getService('ynsocialstore.product')->getProductById($iProductId);
                if (!$aProduct) {
                    $this->alert(_p('unable_to_find_the_product_you_are_looking_for'));
                    return false;
                } elseif ($aCartProduct = Phpfox::getService('ynsocialstore.product')->checkProductIsInCart($iProductId, (int)Phpfox::getUserId())) {
                    $this->alert(_p('you_already_added_this_digital_product_to_your_cart'));
                    return false;
                } elseif ($aOrderProduct = Phpfox::getService('ynsocialstore.product')->checkIsBuyThisProduct($iProductId)) {
                    $this->alert(_p('you_already_bought_this_digital_product_please_go_to_detail_of_this_product_to_get_download_link'));
                    return false;
                }
                $aInsert = [
                    'product_id' => $iProductId,
                    'quantity' => 1,
                    'price' => Phpfox::getService('ynsocialstore.product')->getProductDiscountPrice($iProductId),
                    'product_data' => $aProduct,
                    'type' => 'buy',
                    'currency' => $aProduct['creating_item_currency'],
                    'module' => 'ynsocialstore'
                ];
                if ($iCartId = Phpfox::getService('ynsocialstore.product')->checkHavingCart((int)Phpfox::getUserId())) {
                    Phpfox::getService('ecommerce.cart.process')->edit((int)Phpfox::getUserId());
                    $aInsert['cart_id'] = $iCartId;
                    $iCartProductId = Phpfox::getService('ecommerce.cart.process')->addProducts($aInsert);
                } else {
                    $aInsert['cart_id'] = Phpfox::getService('ecommerce.cart.process')->add(['user_id' => (int)Phpfox::getUserId()]);
                    $iCartProductId = Phpfox::getService('ecommerce.cart.process')->addProducts($aInsert);
                }
            }
        }
        $this->updateMyCartBar();
        return true;
    }

    public function addToCartPhysical()
    {
        Phpfox::isUser(true);
        $aVals = $this->getAll();
        $iProductId = $this->get('iProductId');
        $iAttributeId = $this->get('iAttributeId',0);
        $isBuyNow = ($this->get('type') == 'buynow') ? true : false;
        $iPass = false;
        if(!$iProductId)
        {
            $this->alert(_p('invalid_param'));
            return false;
        }
        else{
            $aProduct = Phpfox::getService('ynsocialstore.product')->getProductById($iProductId);
            if(!$aProduct)
            {
                $this->alert(_p('unable_to_find_the_product_you_are_looking_for'));
                return false;
            }
            elseif($aVals['iCurrentProductLimit'] < $aVals['iQuantity'])
            {
                $this->alert(_p('you_can_not_add_more_this_item_to_cart_cause_reason'));
                return false;
            }
            $iPrice = (float)Phpfox::getService('ynsocialstore.product')->getProductDiscountPrice($iProductId);
            $aProduct = Phpfox::getService('ynsocialstore.product')->getProductForDetailById($iProductId);
            $aProduct['attribute_id'] = $iAttributeId;
            $aProduct['quantity_in_cart_order']  = Phpfox::getService('ynsocialstore.product')->countOrderAndCartOfProductByUser($iProductId,(int)Phpfox::getUserId());
            $max_quantity_can_add = ($aProduct['enable_inventory'] == 1 && $aProduct['max_order'] > 0) ? ($aProduct['max_order'] - $aProduct['quantity_in_cart_order']): ($aProduct['product_quantity_main'] > 0 ? $aProduct['product_quantity'] - $aProduct['quantity_in_cart_order'] : 'unlimited');
            if($max_quantity_can_add === 0)
            {
                $this->alert(_p('you_can_not_add_more_this_item_to_cart_cause_reason'));
                return false;
            }
            if((int)$iAttributeId == 0)
            {
                $this->addProductToCart($iProductId,$iPrice,$aVals['iQuantity'],$aProduct,$iAttributeId,$isBuyNow);
            }
            else{
                $aElement = Phpfox::getService('ynsocialstore.product')->getElementAttribute($iAttributeId);
                $aElementsOnUserCart = Phpfox::getService('ynsocialstore.product')->countOnCartProductByUser($iProductId, (int)Phpfox::getUserId());
                $aElementsOnOrder = Phpfox::getService('ynsocialstore.product')->countOrderOfProduct($iProductId);
                $iInUsed = 0;
                if (isset($aElementsOnUserCart[$iAttributeId])) {
                    $iInUsed = $aElementsOnUserCart[$iAttributeId];
                }
                if (isset($aElementsOnOrder[$iAttributeId])) {
                    $iInUsed = $iInUsed + $aElementsOnOrder[$iAttributeId];
                }
                if($aElement['quantity'] != 0)
                {
                    if(($aElement['quantity'] - $iInUsed) >= $aVals['iQuantity'])
                    {
                        $iPrice = (float)$aElement['price'];
                        $this->addProductToCart($iProductId,$iPrice,$aVals['iQuantity'],$aProduct,$iAttributeId,$isBuyNow);
                        $iPass = true;
                    }
                }
                else{
                    $iPrice = (float)$aElement['price'];
                    $this->addProductToCart($iProductId,$iPrice,$aVals['iQuantity'],$aProduct,$iAttributeId,$isBuyNow);
                    $iPass = true;
                }
                if(!$iPass) {
                    $this->alert(_p('you_can_not_add_more_this_item_to_cart_cause_reason'));
                    return false;
                }
            }

            if((int)$this->get('iAttribute')){
                if($aVals['iCurrentProductLimit'] !== 'unlimited' && (int)$aVals['iCurrentProductLimit']){
                    $this->call('$("#max_quantity_can_add").val('.((int)$aVals['iCurrentProductLimit'] - (int)$aVals['iQuantity']).');');
                }
                if((int)$aVals['iAttributeRemain'] > 0){

                    $this->call('$(".js_selected_attribute").find("a").data("remain","'.((int)$aVals['iAttributeRemain'] - (int)$aVals['iQuantity']).'");');
                }
                $this->call('$(".js_selected_attribute > a").trigger("click");');
            }
            else{
                if($aVals['iCurrentProductLimit'] != 'unlimited' && (int)$aVals['iCurrentProductLimit']){
                    $this->call('$("#max_quantity_can_add").val('.((int)$aVals['iCurrentProductLimit'] - (int)$aVals['iQuantity']).');');
                }
                if((int)$aVals['iCurrentAttributeLimit'] > 0){
                    $this->call('$("#max_order_by_attribute").val("'.((int)$aVals['iCurrentAttributeLimit'] - (int)$aVals['iQuantity']).'");');
                }

            }
        }
        $this->updateMyCartBar();
        return true;
    }
    public function addToCartFromEntry()
    {
        Phpfox::isUser(true);
        $iProductId = $this->get('iProductId');
        $sProductType = $this->get('sProductType');
        $iDiscountPrice = Phpfox::getService('ynsocialstore.product')->getProductDiscountPrice($iProductId);
        if($sProductType == 'digital')
        {
           return $this->addToCartDigital();
        }
        else{
            $aElements = Phpfox::getService('ynsocialstore.product')->getAllElementsOrderPrice($iProductId);
            $aProduct = Phpfox::getService('ynsocialstore.product')->getProductForDetailById($iProductId);
            $aProduct['quantity_in_cart_order']  = Phpfox::getService('ynsocialstore.product')->countOrderAndCartOfProductByUser($iProductId,(int)Phpfox::getUserId());
            $max_quantity_can_add = ($aProduct['enable_inventory'] == 1 && $aProduct['max_order'] > 0) ? ($aProduct['max_order'] - $aProduct['quantity_in_cart_order']): ($aProduct['product_quantity_main'] > 0 ? $aProduct['product_quantity'] - $aProduct['quantity_in_cart_order'] : 'unlimited');
            if($max_quantity_can_add === 0)
            {
                $this->alert(_p('you_can_not_add_more_this_item_to_cart_cause_reason'));
                return false;
            }
            if(empty($aElements))
            {
                $this->addProductToCart($iProductId,$iDiscountPrice,1,$aProduct);
                $this->updateMyCartBar();
                return true;
            }
            else{
                $aElementsOnUserCart = Phpfox::getService('ynsocialstore.product')->countOnCartProductByUser($iProductId, (int)Phpfox::getUserId());
                $aElementsOnOrder = Phpfox::getService('ynsocialstore.product')->countOrderOfProduct($iProductId);
                foreach ($aElements as $key => $aElement) {
                    $iInUsed = 0;
                    if (isset($aElementsOnUserCart[$aElement['attribute_id']])) {
                        $iInUsed = $aElementsOnUserCart[$aElement['attribute_id']];
                    }
                    if (isset($aElementsOnOrder[$aElement['attribute_id']])) {
                        $iInUsed = $iInUsed + $aElementsOnOrder[$aElement['attribute_id']];
                    }
                    if($aElement['quantity'] != 0)
                    {
                        if(($aElement['quantity'] - $iInUsed) > 0)
                        {
                            $iPrice = (float)$aElement['price'];
                            $this->addProductToCart($iProductId,$iPrice,1,$aProduct,$aElement['attribute_id']);
                            $this->updateMyCartBar();
                            return true;
                        }
                    }
                    else{
                        $iPrice = (float)$aElement['price'];
                        $this->addProductToCart($iProductId,$iPrice,1,$aProduct,$aElement['attribute_id']);
                        $this->updateMyCartBar();
                        return true;
                    }
                }
                $this->alert(_p('you_can_not_add_more_this_item_to_cart_cause_reason'));
                return false;
            }
        }

    }
    private function addProductToCart($iProductId,$iPrice,$iQuantity,$aProduct,$iAttributeId = 0,$isBuyNow = false)
    {
        if($isBuyNow){

            $aInsert = [
                'product_id' => $iProductId,
                'quantity' => $iQuantity,
                'attribute_id' => $iAttributeId,
                'price' => $iPrice,
                'product_data' => $aProduct,
                'type' => 'buy',
                'currency' => $aProduct['creating_item_currency'],
                'module' => 'ynsocialstore'
            ];
            if ($iCartId = Phpfox::getService('ynsocialstore.product')->checkHavingCart((int)Phpfox::getUserId())) {
                Phpfox::getService('ecommerce.cart.process')->edit((int)Phpfox::getUserId());
                $aInsert['cart_id'] = $iCartId;
                $iCartProductId = Phpfox::getService('ecommerce.cart.process')->addProducts($aInsert);
                $this->updateMyCartBar();
                $sLink = Phpfox_Url::instance()->makeUrl('ynsocialstore.checkout',['id' => $iCartProductId]);
                $this->call('window.location.href ="' . $sLink . '";');
            } else {
                $aInsert['cart_id'] = Phpfox::getService('ecommerce.cart.process')->add(['user_id' => (int)Phpfox::getUserId()]);
                $iCartProductId = Phpfox::getService('ecommerce.cart.process')->addProducts($aInsert);
                $this->updateMyCartBar();
                $sLink = Phpfox_Url::instance()->makeUrl('ynsocialstore.checkout',['id' => $iCartProductId]);
                $this->call('window.location.href ="' . $sLink . '";');
            }
        }
        else {
            if ($aCartProduct = Phpfox::getService('ynsocialstore.product')->checkProductIsInCart($iProductId, (int)Phpfox::getUserId(), $iAttributeId)) {
                $aUpdate = [
                    'cartproduct_price' => $iPrice,
                    'cartproduct_quantity' => $aCartProduct['cartproduct_quantity'] + $iQuantity,
                ];
                Phpfox::getLib('database')->update(Phpfox::getT('ecommerce_cart_product'), $aUpdate, 'cartproduct_id =' . $aCartProduct['cartproduct_id']);

            } else {
                $aInsert = [
                    'product_id' => $iProductId,
                    'quantity' => $iQuantity,
                    'attribute_id' => $iAttributeId,
                    'price' => $iPrice,
                    'product_data' => $aProduct,
                    'type' => 'buy',
                    'currency' => $aProduct['creating_item_currency'],
                    'module' => 'ynsocialstore'
                ];
                if ($iCartId = Phpfox::getService('ynsocialstore.product')->checkHavingCart((int)Phpfox::getUserId())) {
                    Phpfox::getService('ecommerce.cart.process')->edit((int)Phpfox::getUserId());
                    $aInsert['cart_id'] = $iCartId;
                    $iCartProductId = Phpfox::getService('ecommerce.cart.process')->addProducts($aInsert);
                } else {
                    $aInsert['cart_id'] = Phpfox::getService('ecommerce.cart.process')->add(['user_id' => (int)Phpfox::getUserId()]);
                    $iCartProductId = Phpfox::getService('ecommerce.cart.process')->addProducts($aInsert);
                }
            }
        }
    }
    public function deleteCart()
    {
        $sDeleted = $this->get('sDeleted');
        if(empty($sDeleted))
        {
            return false;
        }
        Phpfox::getLib('database')->delete(Phpfox::getT('ecommerce_cart_product'), 'cartproduct_id IN ('.$sDeleted.')');
        return true;

    }
    public function updateMyCart()
    {
        $iCartID = $this->get('cart_id');
        $aCartData = $this->get('cart_data');
        $noRefesh = $this->get('no_refesh');
        $aDuplicateCart = [];
        $aDeleteCart = [];
        if(!count($aCartData))
        {
            Phpfox::getLib('database')->delete(Phpfox::getT('ecommerce_cart_product'), 'cartproduct_cart_id ='.$iCartID.' AND cartproduct_module like \'ynsocialstore\' AND cartproduct_payment_status = \'init\'');
        }
        else {
            foreach ($aCartData as $key => $aData) {
                $aDeleteCart[] = $aData['cart_id'];
                $aUpdate = [
                    'cartproduct_product_id' => $aData['cart_product_id'],
                    'cartproduct_price' => $aData['cart_attr_price'],
                    'cartproduct_quantity' => $aData['cart_quantity'],
                ];
                if (!isset($aDuplicateCart[$aData['cart_product_id']][$aData['cart_attr_id']])) {
                    $aDuplicateCart[$aData['cart_product_id']][$aData['cart_attr_id']] = $aUpdate;
                } else {
                    $quantity = $aDuplicateCart[$aData['cart_product_id']][$aData['cart_attr_id']]['cartproduct_quantity'];
                    $aDuplicateCart[$aData['cart_product_id']][$aData['cart_attr_id']]['cartproduct_quantity'] = $quantity + $aData['cart_quantity'];
                }
            }
            $aProducts = [];
            if (count($aDuplicateCart)) {
                Phpfox::getLib('database')->delete(Phpfox::getT('ecommerce_cart_product'), 'cartproduct_cart_id =' . $iCartID . ' AND cartproduct_module like \'ynsocialstore\' AND cartproduct_payment_status = \'init\' AND cartproduct_id IN ('.implode(',',$aDeleteCart).')');
                foreach ($aDuplicateCart as $key => $aCart) {
                    foreach ($aCart as $key2 => $aCartProduct) {
                        if (!isset($aProducts[$key])) {
                            $aProducts[$key] = Phpfox::getService('ynsocialstore.product')->getProductForDetailById($key);
                        }
                        $aInsert = [
                            'product_id' => $key,
                            'quantity' => $aCartProduct['cartproduct_quantity'],
                            'attribute_id' => $key2,
                            'price' => $aCartProduct['cartproduct_price'],
                            'product_data' => $aProducts[$key],
                            'type' => 'buy',
                            'currency' => $aProducts[$key]['creating_item_currency'],
                            'module' => 'ynsocialstore',
                            'cart_id' => $iCartID
                        ];
                        if ($aCartProduct['cartproduct_quantity'] > 0) {
                            Phpfox::getService('ecommerce.cart.process')->addProducts($aInsert);
                        }
                    }
                }
            }
        }
        if(!$noRefesh)
        {
            $this->alert(_p('Your cart updated successfully'));
            $this->call('setTimeout(function(){window.location.reload();},2000)');
        }
        return true;
    }
    public function updateMyCartBar()
    {
        Phpfox::getBlock('ynsocialstore.my-cart',['boxSize' => 'max']);
        $this->html('#ynstore-my-cart-dashboard',$this->getContent(false));
        $this->call('ynsocialstore.initMyCartCallout();');
    }
    public function deleteOneCart()
    {
        $iCartId = $this->get('cartproduct_id');
        if(!(int)$iCartId)
        {
            return false;
        }
        Phpfox::getService('ecommerce.process')->removeCart($iCartId);
        $this->updateMyCartBar();
        return true;
    }
    public function deleteAllCart()
    {
        $iCartID = $this->get('cart_id');
        if(!(int)$iCartID)
        {
            return false;
        }
        Phpfox::getLib('database')->delete(Phpfox::getT('ecommerce_cart_product'), 'cartproduct_cart_id ='.$iCartID.' AND cartproduct_module like \'ynsocialstore\' AND cartproduct_payment_status = \'init\'');
        $this->updateMyCartBar();
        return true;
    }
    public function updateCartQuantity()
    {
        $iCartId = $this->get('cartproduct_id');
        $iQuantity = $this->get('cartproduct_quantity');

        if(!(int)$iCartId || !(int)$iQuantity)
        {
            return false;
        }
        Phpfox::getLib('database')->update(Phpfox::getT('ecommerce_cart_product'),['cartproduct_quantity' => $iQuantity],'cartproduct_id ='.$iCartId);
        $this->updateMyCartBar();
        return true;
    }
    public function checkMinOrderWithQuantity()
    {
        $iStoreId = $this->get('store_id');
        $aListProducts = $this->get('list_product');
        $type = $this->get('sType');
        if((int)$iStoreId < 0 || !count($aListProducts))
        {
            $this->call('$(\'#ynstore_mycart_loading\').hide();');
            $this->call('$(\'#ynstore_mycart_buy_all\').removeClass(\'disabled\');');
            $this->call('$(\'#ynstore_checkout_place_order\').removeClass(\'disabled\');');
            return false;
        }
        $aProducts = [];
        $hasError = false;
        foreach($aListProducts as $aProduct)
        {
            if(!isset($aProducts[$aProduct['productid']])) {
                $aProducts[$aProduct['productid']]['quantity'] = $aProduct['quantity'];
                $aProducts[$aProduct['productid']]['storeid'] = $aProduct['storeid'];
            }
            else{
                $aProducts[$aProduct['productid']]['quantity'] = $aProducts[$aProduct['productid']]['quantity'] + $aProduct['quantity'];
            }
        }
        foreach($aProducts as $key => $aPItem)
        {
            list($iCheck, $aItem) = Phpfox::getService('ynsocialstore.product')->checkQuantityWithMinOrder($key,$aPItem['quantity']);

            if ($iCheck != 1) {
                $hasError = true;
                $sError = _p('product_name_has_minimum_order_is_min_order', ['product_name' => $aItem['name'], 'min_order' => $aItem['min_order']]);
                $this->call('$("#js_error_message_store_' . $aPItem['storeid'] . '").removeClass("hide").append(\'<div>' . $sError . '</div>\');');
            }
        }

        $sLink = Phpfox_Url::instance()->makeUrl('ynsocialstore.checkout',$iStoreId > 0 ? ['sellerid' => $iStoreId] : null);
        if(!$hasError && $type == "mycart")
        {
            $this->call('ynsocialstore.updateMyCartData(true,'.$iStoreId.');');
            $this->call('setTimeout(function(){window.location.href="'.$sLink.'"},3000);');
        }
        elseif(!$hasError && $type == "checkout")
        {
            $this->call('$("#ynstore_checkout_form").submit();');
        }
        else{
            $this->call('$(\'#ynstore_mycart_loading\').hide();');
            $this->call('$(\'#ynstore_mycart_buy_all\').removeClass(\'disabled\');');
            $this->call('$(\'#ynstore_checkout_place_order\').removeClass(\'disabled\');');
        }
    }
}
