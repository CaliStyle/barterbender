<?php

defined('PHPFOX') or exit('NO DICE!');

class Coupon_Service_Process extends Phpfox_Service
{
    private $_iPublishFee = 10;
    private $_iFeatureFee = 5;
    private $_unitCurrencyFee = null;

    private $_aCategories = array();

    private $_bIsPublished = false;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('coupon');
        $this->_iPublishFee = (int)Phpfox::getUserParam('coupon.how_much_user_publish_coupon');
        $this->_iFeatureFee = (int)Phpfox::getUserParam('coupon.how_much_user_feature_coupon');
        $this->_unitCurrencyFee = Phpfox::getService('coupon.helper')->getDefaultCurrency();
    }
    
    /**
     * Update Coupon feature status
     * @author TienNPL
     * @param int $iCouponId is coupon id need to update feature status
     * @param int $iIsFeatured is the feature status
     */
    public function feature($iCouponId, $iIsFeatured)
    {
        $oCoupon =  Phpfox::getService('coupon');
        $this->database()->update($this->_sTable,array('is_featured' => $iIsFeatured ),"coupon_id = {$iCouponId}");
        
        if($iIsFeatured)
        {
            $iOwnerId = $oCoupon->getCouponOwnerId($iCouponId);
            if($iOwnerId)
            {
                // Add notification
                $iSenderUserId = $iOwnerId;
                if((int)Phpfox::getUserId() > 0)
                {
                    $iSenderUserId = Phpfox::getUserId();   
                }
                Phpfox::getService("notification.process")->add("coupon_feature",$iCouponId, $iOwnerId, $iSenderUserId);
                
                // Push mail to queue
                $aOwnerEmail =  array($oCoupon->getOwnerEmail($iOwnerId));
                Phpfox::getService('coupon.helper')->pushMailToQueue($iCouponId, 'couponfeatured_owner', $aOwnerEmail);
            }
        }
    }

    /**
     * this function will catch all categories submited and store them into $_aCategories for later use
     * @by : datlv
     * @param $aVals
     * @return bool
     */
    public function getCategoriesFromForm($aVals)
    {
        if (isset($aVals['category']) && count($aVals['category']))
        {
            if(empty($aVals['category'][0]))
            {
                return false;
            }
            else if(!is_array($aVals['category']))
            {
                $this->_aCategories[] = $aVals['category'];
            }
            else{
                foreach ($aVals['category'] as $iCategory)
                {
                    if (empty($iCategory))
                    {
                        continue;
                    }

                    if (!is_numeric($iCategory))
                    {
                        continue;
                    }

                    $this->_aCategories[] = $iCategory;
                }
            }
            return true;
        }
    }

    /**
     * @by : datlv
     * @param $aVals
     * @return bool
     */
    public function add($aVals) {

        $oFilter = Phpfox::getLib('parse.input');

        // check if the user entered a forbidden word
        Phpfox::getService('ban')->checkAutomaticBan($aVals['description'] . ' ' . $aVals['title'] . ' ' . $aVals['term_condition'] . ' ' . $aVals['location_venue']);

        // Check if links in titles
        if (!Phpfox::getLib('validator')->check($aVals['title'], array('url'))) {
            return Phpfox_Error::set(_p('we_do_not_allow_links_in_titles'));
        }

        if(!$this->getCategoriesFromForm($aVals))
            return Phpfox_Error::set(_p('provide_a_category_this_coupon_will_belong_to'));
        ;
        if(($aVals['coupon_type'] == 'discount' && ($aVals['discount_value'] == '' || !is_numeric($aVals['discount_value']) || (int)$aVals['discount_value'] < 0 )) || ($aVals['coupon_type'] == 'special_price' && ($aVals['special_price_value'] == '' || !is_numeric($aVals['special_price_value']) || $aVals['special_price_value'] < 0) )) {
            return Phpfox_Error::set(_p('fill_in_a_valid_discount_value_for_coupon'));
        }

        if($aVals['discount_value'] > 100 && $aVals['discount_type'] == 'percentage' && $aVals['coupon_type'] == 'discount')
        {
            return Phpfox_Error::set(_p('discount_percent_must_be_equal_to_or_less_than_100'));
        }
        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }
        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }
        if (!isset($aVals['privacy_claim'])) {
            $aVals['privacy_claim'] = 0;
        }

   

        $sTitle = $oFilter->clean($aVals['title'], 255);

        $bHasAttachments = false;//(!empty($aVals['attachment']) && Phpfox::getUserParam('fundraising.can_attach_on_fundraising'));

        $iStartTime = Phpfox::getLib('date')->mktime(0, 0, 0, $aVals['start_time_month'], $aVals['start_time_day'], $aVals['start_time_year']);
        $iEndTime = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['end_time_month'], $aVals['end_time_day'], $aVals['end_time_year']);
        $iExpireTime = isset($aVals['unlimit_time']) ? null : Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['expire_time_month'], $aVals['expire_time_day'], $aVals['expire_time_year']);

        $oImage = Phpfox::getLib('image');
        $oFile = Phpfox::getLib('file');
        $bHasImage = false;
        if (isset($_FILES['image']['name']) && ($_FILES['image']['name'] != ''))
        {
            $aImage = $oFile->load('image', array('jpg','gif','png'), (Phpfox::getParam('coupon.max_upload_image_size') === 0 ? null : (Phpfox::getParam('coupon.max_upload_image_size') / 1024)));

            if ($aImage === false)
            {
                return Phpfox_Error::set(_p('please_select_an_image_to_upload'));
            }
            $bHasImage = true;
        }

        //check for time
        if ($iStartTime < PHPFOX_TIME) {
            $iStartTime = PHPFOX_TIME;
            //return Phpfox_Error::set(_p('please_edit_coupon_start_date_before_update_status'));
        }

        if ($iEndTime < $iStartTime) {
            return Phpfox_Error::set(_p('please_edit_coupon_end_date_bigger_than_start_date'));
        }

        if($iExpireTime)
        {
            if ($iExpireTime < $iEndTime) {
                return Phpfox_Error::set(_p('please_edit_coupon_expire_date_bigger_than_end_date'));
            }
        }

        if(!isset($aVals['unlimit_quantity']))
        {
            if(!is_numeric ($aVals['quantity']))
            {
                return Phpfox_Error::set(_p('please_enter_number_for_quantity'));
            }
            elseif( $aVals['quantity'] < 0)
            {
                return Phpfox_Error::set(_p('please_edit_quantity_more_than_zero'));
            }
        }

        if(empty($aVals['auto_generate']) && !$this->CheckCouponCodeFormat($aVals))
        {
            return Phpfox_Error::set(_p('coupon_code_must_be_1_30_characters'));
        }
        
        #verify website
        if(!empty($aVals['site_url']))
        {
            $url_pattern = '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i';
            if(!preg_match($url_pattern, $aVals['site_url']))
            {
                return Phpfox_Error::set(_p('site_url_format_is_not_valid'));
            }
        }
        
        $aInsert = array(
            'title' => $sTitle,
            'user_id' => Phpfox::getUserId(),
            'module_id' => (isset($aVals['module_id']) ? $aVals['module_id'] : 'coupon'),
            'item_id' => (isset($aVals['item_id']) ? $aVals['item_id'] : '0'),
            'time_stamp' => PHPFOX_TIME,
            'start_time' => $iStartTime,
            'end_time' => $iEndTime,
            'expire_time' => $iExpireTime,
            'site_url' => empty($aVals['site_url']) ? NULL : $aVals['site_url'],
            'discount_type' => ($aVals['coupon_type'] == 'discount') ? $aVals['discount_type'] : 'special_price',
            'discount_value' => ($aVals['coupon_type'] == 'discount') ? $aVals['discount_value'] : '-1',
            'discount_currency' => ( ($aVals['coupon_type'] == 'discount') && $aVals['discount_type'] == 'price') ? $aVals['discount_currency'] : NULL,
            'special_price_value' => ($aVals['coupon_type'] == 'special_price') ? $aVals['special_price_value'] : NULL,
            'special_price_currency' =>  ($aVals['coupon_type'] == 'special_price') ? $aVals['special_price_currency'] : NULL,
            'location_venue' => (isset($aVals['location_venue']) ? $aVals['location_venue'] : NULL),
            'address' => (isset($aVals['address']) ? $aVals['address'] : NULL),
            'city' => (empty($aVals['city']) ? NULL : $oFilter->clean($aVals['city'], 255)),
            'postal_code' => (empty($aVals['postal_code']) ? NULL : Phpfox::getLib('parse.input')->clean($aVals['postal_code'], 20)),
            'country_iso' => $aVals['country_iso'],
            'country_child_id' => isset($aVals['country_child_id']) ? $aVals['country_child_id'] : NULL,
            'gmap' => serialize($aVals['gmap']),
            'quantity' => (isset($aVals['unlimit_quantity'])) ? NULL : $aVals['quantity'],
            'code_setting' => (isset($aVals['auto_generate'])) ? NULL : htmlspecialchars($aVals['code_setting']),
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'privacy_claim' => (isset($aVals['privacy_claim']) ? $aVals['privacy_claim'] : '0'),
            'is_approved' => 0,//will check user setting and approve later
            'is_featured' => (isset($aVals['feature_coupon'])) ? 1 : 0,//will check is really feature later
            'is_draft' => 1,
            'is_show_map' => (isset($aVals['is_show_map'])) ? 1 : 0,
            'status' => Phpfox::getService('coupon')->getStatusCode('draft'), //will check status later
            'category_id' => end($this->_aCategories),
            'print_option' => serialize($aVals['print_option'])
        );

        if(isset($aVals['draft']) || isset($aVals['draft_update'])) {
            $aInsert['is_draft'] = 1;
            $aInsert['status'] = Phpfox::getService('coupon')->getStatusCode('draft');
        }
        else
        {
            // this campaign is needed to be published, so we need to do more thing
            $this->_bIsPublished = true;
        }

        $iCouponId = $this->database()->insert(Phpfox::getT('coupon'), $aInsert);

        //isert image for coupon
        $aCouponImage = $this->database()->select('image_path, server_id')
            ->from(Phpfox::getT('coupon'), 'c')
            ->where('coupon_id = '.$iCouponId)
            ->execute('getSlaveRow');

        if (!empty($aVals['temp_file'])) {
            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
            if (!empty($aFile)) {
                $aCouponImage['image_path'] = "coupon/" . $aFile['path'];
                $aCouponImage['server_id'] = $aFile['server_id'];
                Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
                $this->database()->update($this->_sTable,array('server_id' => $aCouponImage['server_id'], 'image_path'=> $aCouponImage['image_path']),'coupon_id='.$iCouponId);
            }
        }

        if(!empty($aVals['custom']))
            Phpfox::getService('coupon.custom.process')->addValue($aVals['custom'],$iCouponId);
        
        $this->addCategoriesForCoupon($iCouponId);

        //update image here , use coupon id to make filename
        if ($bHasImage)
        {
            $this->upload($iCouponId, $aVals, $oFile, $oImage);
        }

        $sDescription = $oFilter->clean($aVals['description']);
        $sDescription_parse = $oFilter->prepare($aVals['description']);

        $sTermAndCondition = $oFilter->clean($aVals['term_condition']);
        $sTermAndCondition_parse = $oFilter->prepare($aVals['term_condition']);

        $aInsertText = array(
            'coupon_id' => $iCouponId,
            'description' => $sDescription,
            'description_parsed' => $sDescription_parse,
            'term_condition' => $sTermAndCondition,
            'term_condition_parsed' => $sTermAndCondition_parse,
        );

        $this->database()->insert(Phpfox::getT('coupon_text'), $aInsertText);

        // If we uploaded any attachments make sure we update the 'item_id'
        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iCouponId);
        }

        $aEmail = Phpfox::getService('coupon.mail')->getEmailMessageFromTemplate(Phpfox::getService('coupon.mail')->getTypesCode('createcouponsuccessful_owner'), $iCouponId);

        Phpfox::getService('coupon.mail.send')->send($aEmail['subject'], $aEmail['message'], Phpfox::getService('coupon')->getOwnerEmail($aInsert['user_id']));

        #Custom privacy
        if ($aVals['privacy'] == '4')
        {
            Phpfox::getService('privacy.process')->add('coupon', $iCouponId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
        }

        if (Phpfox::VERSION >= '3.7.0' && Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && !empty($aVals['description']))
        {
            Phpfox::getService('tag.process')->add('coupon', $iCouponId, Phpfox::getUserId(), $aVals['description'], true);
        }

        if($this->_bIsPublished)
        {
            $sUrl = $this->pay($iCouponId);
            if($sUrl === false)
            {
                // publish and/or feature
                if(isset($aVals['feature_coupon']))
                {
                    // publish and feature
                    $this->publishForPaymentIsZero($iCouponId);
                    $this->feature($iCouponId, 1);
                } else 
                {
                    // publish 
                    $this->publishForPaymentIsZero($iCouponId);
                }
            
                Phpfox::getLib('url')->send(Phpfox::getLib('url')->permalink('coupon.detail', $iCouponId, $sTitle));
            } else 
            {
            	
                Phpfox::getLib('url')->send(Phpfox::getLib('url')->permalink('coupon.payment', $iCouponId));
            }
        }

        return $iCouponId;


    }

    public function update($iCouponId, $aVals)
    {
        $oFilter = Phpfox::getLib('parse.input');

        // check if the user entered a forbidden word
        Phpfox::getService('ban')->checkAutomaticBan($aVals['description'] . ' ' . $aVals['title']. ' ' . $aVals['term_condition'] . ' ' . $aVals['location_venue']);

        // Check if links in titles
        if (!Phpfox::getLib('validator')->check($aVals['title'], array('url'))) {
            return Phpfox_Error::set(_p('we_do_not_allow_links_in_titles'));
        }

        if(!$this->getCategoriesFromForm($aVals))
            return Phpfox_Error::set(_p('provide_a_category_this_coupon_will_belong_to'));

        if( ( $aVals['discount_value'] == '' || !is_numeric($aVals['discount_value'] ) )   && ( $aVals['special_price_value'] == '' || !is_numeric($aVals['special_price_value']) ) ) {
            return Phpfox_Error::set(_p('fill_in_a_discount_value_for_coupon'));
        }
        
        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }
        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }
        if (!isset($aVals['privacy_donate'])) {
            $aVals['privacy_donate'] = 0;
        }

        $sTitle = $oFilter->clean($aVals['title'], 255);

        $bHasAttachments = false; // (!empty($aVals['attachment']) && Phpfox::getUserParam('fundraising.can_attach_on_fundraising'));

        $iStartTime = Phpfox::getLib('date')->mktime(0, 0, 0, $aVals['start_time_month'], $aVals['start_time_day'], $aVals['start_time_year']);
        $iEndTime = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['end_time_month'], $aVals['end_time_day'], $aVals['end_time_year']);
        $iExpireTime = isset($aVals['unlimit_time']) ? null : Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['expire_time_month'], $aVals['expire_time_day'], $aVals['expire_time_year']);

        $bHasImage = false;
        if (isset($_FILES['image']['name']) && ($_FILES['image']['name'] != ''))
        {
            $oImage = Phpfox::getLib('image');
            $oFile = Phpfox::getLib('file');
            $aImage = $oFile->load('image', array('jpg','gif','png'));
            if ($aImage === false)
            {
                return Phpfox_Error::set(_p('please_select_an_image_to_upload'));
            }
            $bHasImage = true;
        }

        //update image for coupon
        $aCouponImage = $this->database()->select('image_path, server_id')
            ->from(Phpfox::getT('coupon'), 'c')
            ->where('coupon_id = '.$iCouponId)
            ->execute('getSlaveRow');

        if (!empty($aVals['temp_file'])) {
            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
            if (!empty($aFile)) {
                $aCouponImage['image_path'] = "coupon/" . $aFile['path'];
                $aCouponImage['server_id'] = $aFile['server_id'];
                Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
                $this->database()->update($this->_sTable,array('server_id' => $aCouponImage['server_id'], 'image_path'=> $aCouponImage['image_path']),'coupon_id='.$iCouponId);
            }
        }

        //check for time
        if ($iStartTime < PHPFOX_TIME) {
            $iStartTime = PHPFOX_TIME;
            //return Phpfox_Error::set(_p('please_edit_coupon_start_date_before_update_status'));
        }
        if ($iEndTime < PHPFOX_TIME) {
            return Phpfox_Error::set(_p('please_edit_coupon_end_date_before_update_status'));
        }
        if ($iEndTime < $iStartTime) {
            return Phpfox_Error::set(_p('please_edit_coupon_end_date_bigger_than_start_date'));
        }
        if($iExpireTime)
        {
            if ($iExpireTime < PHPFOX_TIME) {
                return Phpfox_Error::set(_p('please_edit_coupon_expire_date_before_update_status'));
            }
            if ($iExpireTime < $iEndTime) {
                return Phpfox_Error::set(_p('please_edit_coupon_expire_date_bigger_than_end_date'));
            }
        }

        if(!empty($aVals['quantity']) && $aVals['quantity'] <= 0)
        {
            return Phpfox_Error::set(_p('please_edit_quantity_more_than_zero'));
        }

        if(empty($aVals['auto_generate']) && !$this->CheckCouponCodeFormat($aVals))
        {
            return Phpfox_Error::set(_p('coupon_code_must_be_1_30_characters'));
        }
        
        #verify website
        if(!empty($aVals['site_url']))
        {
            $url_pattern = '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i';
            if(!preg_match($url_pattern, $aVals['site_url']))
            {
                return Phpfox_Error::set(_p('site_url_format_is_not_valid'));
            }
        }

        $aUpdate = array(
            'title' => $sTitle,
            'time_stamp' => PHPFOX_TIME,
            'start_time' => $iStartTime,
            'end_time' => $iEndTime,
            'expire_time' => $iExpireTime,
            'site_url' => empty($aVals['site_url']) ? NULL : $aVals['site_url'],
            'discount_type' => ($aVals['coupon_type'] == 'discount') ? $aVals['discount_type'] : 'special_price',
            'discount_value' => ($aVals['coupon_type'] == 'discount') ? $aVals['discount_value'] : '-1',
            'discount_currency' => ( ($aVals['coupon_type'] == 'discount') && $aVals['discount_type'] == 'price') ? $aVals['discount_currency'] : NULL,
            'special_price_value' => ($aVals['coupon_type'] == 'special_price') ? $aVals['special_price_value'] : NULL,
            'special_price_currency' =>  ($aVals['coupon_type'] == 'special_price') ? $aVals['special_price_currency'] : NULL,
            'location_venue' => (isset($aVals['location_venue']) ? $aVals['location_venue'] : NULL),
            'address' => (isset($aVals['address']) ? $aVals['address'] : NULL),
            'city' => (empty($aVals['city']) ? NULL : $oFilter->clean($aVals['city'], 255)),
            'postal_code' => (empty($aVals['postal_code']) ? NULL : Phpfox::getLib('parse.input')->clean($aVals['postal_code'], 20)),
            'country_iso' => $aVals['country_iso'],
            'country_child_id' => isset($aVals['country_child_id']) ? $aVals['country_child_id'] : NULL,
            'gmap' => serialize($aVals['gmap']),
            'quantity' => (isset($aVals['unlimit_quantity'])) ? NULL : $aVals['quantity'],
            'code_setting' => (isset($aVals['auto_generate'])) ? NULL : htmlspecialchars($aVals['code_setting']),
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'privacy_claim' => (isset($aVals['privacy_claim']) ? $aVals['privacy_claim'] : '0'),
            'category_id' => end($this->_aCategories),
            'is_featured' => (isset($aVals['feature_coupon'])) ? 1 : 0,//will check is really feature later
            'is_show_map' => (isset($aVals['is_show_map'])) ? 1 : 0,
            'print_option' => serialize($aVals['print_option'])
        );

        if(isset($aVals['draft_publish']) && $aVals['draft_publish']) {
            $this->_bIsPublished = true;
        }

        $this->database()->update($this->_sTable, $aUpdate, 'coupon_id = ' . $iCouponId);

        $this->addCategoriesForCoupon($iCouponId);

        $aCallback = (!empty($aVals['module_id']) ? Phpfox::callback('coupon.addCoupon', $iCouponId) : null);


        $sDescription = $oFilter->clean($aVals['description']);
        $sDescription_parse = $oFilter->prepare($aVals['description']);

        $sTermAndCondition = $oFilter->clean($aVals['term_condition']);
        $sTermAndCondition_parse = $oFilter->prepare($aVals['term_condition']);

        $aUpdateText = array(
            'description' => $sDescription,
            'description_parsed' => $sDescription_parse,
            'term_condition' => $sTermAndCondition,
            'term_condition_parsed' => $sTermAndCondition_parse,
        );


        $this->database()->update(Phpfox::getT('coupon_text'), $aUpdateText, 'coupon_id = ' . $iCouponId);
        if(isset($aVals['custom'])) {
            Phpfox::getService('coupon.custom.process')->updateValue($aVals['custom'], $iCouponId);
        }



        //update image here , use coupon id to make filename
        if ($bHasImage)
        {
            $this->upload($iCouponId, $aVals, $oFile, $oImage);
        }

        // If we uploaded any attachments make sure we update the 'item_id'
        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iCouponId);
        }

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('coupon',  $iCouponId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int) $aVals['privacy_comment'] : 0)) : null);

        if (Phpfox::VERSION >= '3.7.0' && Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support') && !empty($aVals['description']))
        {
            $aCoupon = $this->database()->select('c.coupon_id, c.user_id')
                ->from(Phpfox::getT('coupon'), 'c')
                ->where('c.coupon_id = ' . $iCouponId)
                ->execute('getSlaveRow');       

            if(isset($aCoupon['coupon_id'])){
                Phpfox::getService('tag.process')->update('coupon', $aCoupon['coupon_id'], $aCoupon['user_id'], $aVals['description'], true);
            }
        }        

        // in case campaign is published this time
        // it only occurs once for each campaign
        if($this->_bIsPublished)
        {
            // don't worry about duplicate cause when it happens, no feed ever created
            if($aVals['status'] == Phpfox::getService('coupon')->getStatusCode('denied'))
            {
                $iTransactionId = Phpfox::getService('coupon.transaction')->getTransactionIdByCouponId($iCouponId);
                $this->publish($iTransactionId);
                Phpfox::getLib('url')->send(Phpfox::getLib('url')->permalink('coupon.detail', $iCouponId, $aVals['title']));
            }
            else
            {
                $sUrl = $this->pay($iCouponId);
                if($sUrl === false)
	            {
	                // publish and/or feature
	                if(isset($aVals['feature_coupon']))
	                {
	                    // publish and feature
	                    $this->publishForPaymentIsZero($iCouponId);
	                    $this->feature($iCouponId, 1);
	                } else 
	                {
	                    // publish 
	                    $this->publishForPaymentIsZero($iCouponId);
	                }
	            
	                Phpfox::getLib('url')->send(Phpfox::getLib('url')->permalink('coupon.detail', $iCouponId, $sTitle));
	            } else 
	            {
	            	
	                Phpfox::getLib('url')->send(Phpfox::getLib('url')->permalink('coupon.payment', $iCouponId));
	            }
            }
        }

        #Custom privacy
        if (Phpfox::isModule('privacy'))
        {
            if ($aVals['privacy'] == '4')
            {
                Phpfox::getService('privacy.process')->update('coupon', $iCouponId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
            }
            else 
            {
                Phpfox::getService('privacy.process')->delete('coupon', $iCouponId);
            }           
        }

        // $this->cache()->remove(array('user/' . Phpfox::getUserId(), 'blog_browse'), 'substr');

        $this->cache()->remove('coupon', 'substr');
        
        if(isset($aVals['update'])) 
        {
            $this->doRefreshStatus($iCouponId);
        }
                

        return $iCouponId;
    }

    public function doRefreshStatus($iCouponId)
    {
        if(!$iCouponId)
        {
            return FALSE;
        }
        // Get related coupon
        $aCoupon = $this->database()->select('*')->from($this->_sTable,'c')->where("coupon_id = {$iCouponId}")->execute('getRow');
        $iStatus = Phpfox::getService('coupon')->getRefreshStatus($iCouponId);
        if(!$aCoupon || $iStatus === false)
        {
            return FALSE;
        }
        
        if($aCoupon['status'] != $iStatus)
        {
            //  remove status close if status is refresh 
            if($aCoupon['status'] == Phpfox::getService('coupon')->getStatusCode('closed') && $iStatus != Phpfox::getService('coupon')->getStatusCode('closed'))
            {
                $this->database()->update($this->_sTable, array( 'is_closed' => 0 ), 'coupon_id = ' . $iCouponId);
            }
            
            $aUpdate = array( 'status' => $iStatus );
            if($iStatus == Phpfox::getService('coupon')->getStatusCode('closed'))
            {
                $this->close($iCouponId);
            }elseif($iStatus == Phpfox::getService('coupon')->getStatusCode('running'))
            {
                $this->run($iCouponId);
            } else {
                $this->database()->update($this->_sTable, $aUpdate, 'coupon_id = ' . $iCouponId);
            }           
        } 
    }
    
    /**
     * Delete Coupon
     * @author TienNPL
     * @param int $iCouponId is coupon id need to delete
     */
    public function delete($iCouponId)
    {
        $this->database()->update($this->_sTable, array('is_removed' => 1),"coupon_id = {$iCouponId}");
		
		if (Phpfox::isModule('feed')) {
			Phpfox::getService('feed.process')->delete('coupon', $iCouponId);
		}
		
        $aCoupon = Phpfox::getService('coupon')->getCouponById($iCouponId);
        Phpfox::getService('user.activity')->update($aCoupon['user_id'], 'coupon', '-');
        return TRUE;
    }
    
    /**
     * Pause Coupon
     * @author TienNPL
     * @param int $iCouponId is coupon id need to delete
     */
    public function pause($iCouponId)
    {
        $oCoupon =  Phpfox::getService('coupon');
        $this->database()->update($this->_sTable, array('status' => 4),"coupon_id = {$iCouponId}");
        
        $iOwnerId = $oCoupon->getCouponOwnerId($iCouponId);
        if($iOwnerId)
        {
            // Add notification
            Phpfox::getService("notification.process")->add("coupon_pause",$iCouponId, $iOwnerId);
        }
        return TRUE;
    }
    
    /**
     * Resume Coupon
     * @author TienNPL
     * @param int $iCouponId is coupon id need to resume
     */
    public function resume($iCouponId)
    {
        $oCoupon =  Phpfox::getService('coupon');
        $iCurrentStatus = Phpfox::getService('coupon')->checkCurrentStatus($iCouponId, TRUE);
        if($iCurrentStatus)
        {
            $this->database()->update($this->_sTable, array('status' => $iCurrentStatus),"coupon_id = {$iCouponId}");
        }

        $iOwnerId = $oCoupon->getCouponOwnerId($iCouponId);
        if($iOwnerId)
        {
            // Add notification
            Phpfox::getService("notification.process")->add("coupon_resume",$iCouponId, $iOwnerId);
        }
        return TRUE;
    }
    
    /**
     * Close Coupon
     * @author TienNPL
     * @param int $iCouponId is coupon id need to close
     */
    public function close($iCouponId)
    {
        $oCoupon =  Phpfox::getService('coupon');
        if($iCouponId)
        {
            $this->database()->update($this->_sTable, array('status' => Phpfox::getService('coupon')->getStatusCode('closed'),'is_closed' => 1),"coupon_id = {$iCouponId}");
        }
        $iOwnerId = $oCoupon->getCouponOwnerId($iCouponId);
        if($iOwnerId)
        {
            // Add notification
            Phpfox::getService("notification.process")->add("coupon_close",$iCouponId, $iOwnerId);
            
            // Add notification for claimers
            $aClaimerIds = $oCoupon->getClaimerIds($iCouponId);
            
            if($aClaimerIds)
            {
                foreach($aClaimerIds as $aClaimerId)
                {
                    Phpfox::getService("notification.process")->add("coupon_close", $iCouponId, $aClaimerId['user_id'], Phpfox::getUserId());
                }
            } 
            
            // Add notification for followers
            $aFollowerIds = $oCoupon->getFollowerIds($iCouponId);
            if($aFollowerIds)
            {
                foreach($aFollowerIds as $aFollowerId)
                {
                    Phpfox::getService("notification.process")->add("coupon_close", $iCouponId, $aFollowerId['user_id'], Phpfox::getUserId());
                }
            } 
            
            // Push mail to queue
            $aOwnerEmail =  array($oCoupon->getOwnerEmail($iOwnerId));
            Phpfox::getService('coupon.helper')->pushMailToQueue($iCouponId, 'couponclosed_owner', $aOwnerEmail);
        }
        return TRUE;
    }
    
    /**
     * Run Coupon
     * @author TienNPL
     * @param int $iCouponId is coupon id need to start running
     */
    public function run($iCouponId)
    {
        $oCoupon =  Phpfox::getService('coupon');
        if($iCouponId)
        {
            $this->database()->update($this->_sTable, array('status' => Phpfox::getService('coupon')->getStatusCode('running')),"coupon_id = {$iCouponId}");
        }
        
        $iOwnerId = $oCoupon->getCouponOwnerId($iCouponId);
        if($iOwnerId)
        {
            // Add notification
            Phpfox::getService("notification.process")->add("coupon_run",$iCouponId, $iOwnerId);
            
            // Add notification for followers
            $aFollowerIds = $oCoupon->getFollowerIds($iCouponId);
            if($aFollowerIds)
            {
                foreach($aFollowerIds as $aFollowerId)
                {
                    Phpfox::getService("notification.process")->add("coupon_run", $iCouponId, $aFollowerId['user_id'], Phpfox::getUserId());
                }
            } 
        }
        
        // Push mail to queue to send to owner
        $aOwnerEmail =  array($oCoupon->getOwnerEmail($iOwnerId));
        Phpfox::getService('coupon.helper')->pushMailToQueue($iCouponId, 'startrunningcoupon_owner', $aOwnerEmail, Phpfox::getUserId());
        
        return TRUE;
    }
    /**
     * Close Coupon
     * @author TienNPL
     * @param int $iCouponId is coupon id need to resume
     */
    public function deny($iCouponId)
    {
        $oCoupon = Phpfox::getService('coupon');
        
        if($iCouponId)
        {
            $this->database()->update($this->_sTable, array('status' => 8,'is_draft' => 1),"coupon_id = {$iCouponId}");
        }
        
        $iOwnerId = $oCoupon->getCouponOwnerId($iCouponId);
        if($iOwnerId)
        {
            // Add notification
            Phpfox::getService("notification.process")->add("coupon_deny",$iCouponId, $iOwnerId);
        }
            
        return TRUE;
    }
    /**
     * Approve Coupon
     * @author TienNPL
     * @param int $iCouponId is coupon id need to approve
     */
    public function approve($iCouponId)
    {
        $oCoupon = Phpfox::getService('coupon');
        $iCurrentStatus = Phpfox::getService('coupon')->checkCurrentStatus($iCouponId);
        if($iCurrentStatus)
        {
            $this->database()->update($this->_sTable, array('is_approved' => 1,'status' => $iCurrentStatus),"coupon_id = {$iCouponId}");
        }
        
        $aCoupon = Phpfox::getService('coupon')->quickGetCouponById($iCouponId);
        // Add Feed
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('coupon', $aCoupon['coupon_id'], $aCoupon['privacy'], (isset($aCoupon['privacy_comment']) ? (int) $aCoupon['privacy_comment'] : 0), 0, $aCoupon['user_id']) : null);
        
        // Add notification and Send Mail
        $iOwnerId = $oCoupon->getCouponOwnerId($iCouponId);
        if($iOwnerId)
        {
            // Add notification
            Phpfox::getService("notification.process")->add("coupon_approve",$iCouponId, $iOwnerId);

            // Add count
            Phpfox::getService('user.activity')->update($iOwnerId, 'coupon');

            // Push mail to queue
            $aOwnerEmail =  array($oCoupon->getOwnerEmail($iOwnerId));
            Phpfox::getService('coupon.helper')->pushMailToQueue($iCouponId, 'couponapproved_owner', $aOwnerEmail);
        }

        (($sPlugin = Phpfox_Plugin::get('coupon.service_coupon_process_approve_end')) ? eval($sPlugin) : false);
        
        return TRUE;
    }
    
    /**
     * Add favorite
     * @author TienNPL
     */
    public function addFavorite($iItemId = 0)
    {
        if($iItemId)
        {
            $iCount = $this->database()->select('COUNT(*)')
                    ->from(phpfox::getT('coupon_favorite'))
                    ->where("coupon_id = {$iItemId} AND user_id = ".Phpfox::getUserId())
                    ->execute('getSlaveField');
                    
            if($iCount)
            {
                return false;
            }
            
            $iId = $this->database()->insert(phpfox::getT('coupon_favorite'), array(
                    'coupon_id' => (int) $iItemId,
                    'user_id' => Phpfox::getUserId(),
                    'time_stamp' => PHPFOX_TIME,
                )
            );
            
            $iOwnerId = Phpfox::getService("coupon")->getCouponOwnerId($iItemId);
            if($iOwnerId)
            {
                // Add notification
                Phpfox::getService("notification.process")->add("coupon_favorite",$iItemId, $iOwnerId);
                
                // Add notification for followers
                $aFollowerIds = Phpfox::getService("coupon")->getFollowerIds($iItemId);
                if($aFollowerIds)
                {
                    foreach($aFollowerIds as $aFollowerId)
                    {
                        Phpfox::getService("notification.process")->add("coupon_favorite", $iItemId, $aFollowerId['user_id']);
                    }
                }
            }
        
            (($sPlugin = Phpfox_Plugin::get('coupon.service_process_addfavorite_end')) ? eval($sPlugin) : false);
            
            return $iId;
        }
        return false;
    }
    
    /**
     * Add Follow
     * @author TienNPL
     */
    public function addFollow($iItemId)
    {
        $iId = $this->database()->insert(phpfox::getT('coupon_follow'), array(
                    'coupon_id' => $iItemId,
                    'user_id' => Phpfox::getUserId(),
                    'time_stamp' => PHPFOX_TIME,
                )
        );
        return $iId;
    }
    
    /**
     * Delete favorite
     * @author TienNPL
     */
    public function deleteFavorite($iId)
    {
        $this->database()->delete(phpfox::getT('coupon_favorite'), "favorite_id = {$iId} AND user_id = " . Phpfox::getUserId());
    }
    
    /**
     * Delete favorite
     * @author TienNPL
     */
    public function deleteFollow($iId)
    {
        $this->database()->delete(phpfox::getT('coupon_follow'), "follow_id = {$iId} AND user_id = " . Phpfox::getUserId());
    }
    
    public function inviteFriends($aVals, $aCoupon)
    {
        $this->sentInvite($aVals,$aCoupon);

        return $aCoupon['coupon_id'];
    }
    
    /**
     * Send Email and Notification for friends
     * @author TienNPL
     */
    public function sentInvite($aVals, $aCoupon)
    {
        $oParseInput = Phpfox::getLib('parse.input');
        
        // Get invited friend and email if have
        if (isset($aVals['emails']) || isset($aVals['invite']))
        {
            $aInvites = $this->database()->select('invited_user_id, invited_email')
                ->from(Phpfox::getT('coupon_invite'))
                ->where('coupon_id = ' . (int) $aCoupon['coupon_id'])
                ->execute('getRows');

            $aInvited = array();
            foreach ($aInvites as $aInvite)
            {
                $aInvited[(empty($aInvite['invited_email']) ? 'user' : 'email')][(empty($aInvite['invited_email']) ? $aInvite['invited_user_id'] : $aInvite['invited_email'])] = TRUE;
            }
        }
        // Coupon link
       $sLink = Phpfox::getLib('url')->permalink('coupon.detail', $aCoupon['coupon_id'], $aCoupon['title']);
        
       // Email Message
       if (!empty($aVals['personal_message']))
        {
            $sMessage = $aVals['personal_message'];
        }
        else
        {
            //in case user leave message box empty
          $sMessage = _p('full_name_invited_you_to_the_title', array(
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'title' => $oParseInput->clean($aCoupon['title'], 255),
                    'link' => $sLink
                )
            );
        }
        
        // Email Subject
        if (!empty($aVals['subject']))
        {
            $sSubject = $aVals['subject'];
        }
        else
        {
            //in case user leave subject box empty
           $sSubject = _p('full_name_invited_you_to_the_coupon_title', array(
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'title' => $oParseInput->clean($aCoupon['title'], 255),
                )
            );
        }

        $sSubject = Phpfox::getService('coupon.mail')->parseTemplate($sSubject, $aCoupon, $iInviteId = Phpfox::getUserId());
        $sMessage = Phpfox::getService('coupon.mail')->parseTemplate($sMessage, $aCoupon, $iInviteId = Phpfox::getUserId());

        $aCustomMesssage = array(
            'subject' => $sSubject,
            'message' => $sMessage
        );
                    

        if (isset($aVals['emails']))
        {
                $aEmails = explode(',', $aVals['emails']);

                $aCachedEmails = array();
                foreach ($aEmails as $sEmail)
                {
                    $sEmail = trim($sEmail);
                    if (!Phpfox::getLib('mail')->checkEmail($sEmail))
                    {
                        continue;
                    }

                    if(isset($aCachedEmails[$sEmail]) && $aCachedEmails[$sEmail] == true)
                    {
                        continue;
                    }

                    $bResult = Phpfox::getService('coupon.mail.process')->sendEmailTo($sType = 0, $aCoupon['coupon_id'], $aReceivers = $sEmail, $aCustomMesssage);
                    if ($bResult)
                    {
                        $this->database()->insert(Phpfox::getT('coupon_invite'), array(
                                'coupon_id' => $aCoupon['coupon_id'],
                                'inviting_user_id' =>  Phpfox::getUserId(),
                                'invited_email' => $sEmail,
                                'time_stamp' => PHPFOX_TIME
                            )
                        );
                    }
                }
        }

        if (isset($aVals['invite']) && is_array($aVals['invite']))
        {
            $sUserIds = '';
            foreach ($aVals['invite'] as $iUserId)
            {
                if (!is_numeric($iUserId))
                {
                    continue;
                }
                $sUserIds .= $iUserId . ',';
            }
            $sUserIds = rtrim($sUserIds, ',');

            $aUsers = $this->database()->select('user_id, email, language_id, full_name')
                ->from(Phpfox::getT('user'))
                ->where('user_id IN(' . $sUserIds . ')')
                ->execute('getSlaveRows');

            foreach ($aUsers as $aUser)
            {
              
                $bResult = Phpfox::getService('coupon.mail.process')->sendEmailTo($sType = 0, $aCoupon['coupon_id'], $aReceivers = $aUser['user_id'], $aCustomMesssage);

                if ($bResult)
                {
                    $iInviteId = $this->database()->insert(Phpfox::getT('coupon_invite'), array(
                            'coupon_id' => $aCoupon['coupon_id'],
                            'inviting_user_id' =>  Phpfox::getUserId(),
                            'invited_user_id' => $aUser['user_id'],
                            'time_stamp' => PHPFOX_TIME
                        )
                    );
                }
                (Phpfox::isModule('request') ? Phpfox::getService('request.process')->add('coupon_invited', $aCoupon['coupon_id'], $aUser['user_id']) : null);
            }
        }
    }

    //add categories for coupon
    public function addCategoriesForCoupon($iCouponId)
    {
        if (isset($this->_aCategories) && count($this->_aCategories))
        {
            $this->database()->delete(Phpfox::getT('coupon_category_data'), 'coupon_id = ' . (int) $iCouponId);

            foreach ($this->_aCategories as $iCategoryId)
            {
                $this->database()->insert(Phpfox::getT('coupon_category_data'), array('coupon_id' => $iCouponId, 'category_id' => $iCategoryId));
            }
        }
    }

    /**
     * just publish after the payment complete and callback for this
     * @by : datlv
     * @param $iTransactionId
     * @return bool
     */
    public function publish($iTransactionId)
    {
        if(!$iTransactionId)
            return FALSE;

        $aTransaction = Phpfox::getService('coupon.transaction')->getTransactionById($iTransactionId);

        $aInvoice = unserialize($aTransaction['invoice']);
        $bAutoApprove = (bool)$aInvoice['auto_approved'];
        $aCoupon = Phpfox::getService('coupon')->getCouponById($aTransaction['coupon_id']);

        $aUpdate = array(
            'is_draft' => 0,
            'is_featured' => $aInvoice['is_featured'],
        );

        if ($bAutoApprove|| $aCoupon['module_id'] == 'pages') {
            $aUpdate['is_approved'] = 1;
            $aUpdate['status'] = Phpfox::getService('coupon')->getStatusCode('running');
        }
        else
        {
            $aUpdate['is_approved'] = 0;
            $aUpdate['status'] = Phpfox::getService('coupon')->getStatusCode('pending');
        }

        if($aUpdate['is_approved'])
        {
            $aCallback = ((!empty($aCoupon['module_id']) && $aCoupon['module_id'] != 'coupon') ? Phpfox::getService('coupon')->getCouponAddCallback($aCoupon['item_id'],$aCoupon['module_id']) : null);
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback($aCallback)->allowGuest()->add('coupon', $aCoupon['coupon_id'], $aCoupon['privacy'], (isset($aCoupon['privacy_comment']) ? (int) $aCoupon['privacy_comment'] : 0), (isset($aCoupon['item_id']) ? (int) $aCoupon['item_id'] : 0), $aCoupon['user_id']) : null);
            
            $this->database()->update(Phpfox::getT('feed'),array('user_id' => $aCoupon['user_id']), "type_id = 'coupon' AND item_id = {$aCoupon['coupon_id']}");
            
            Phpfox::getService('user.activity')->update($aCoupon['user_id'], 'coupon');
            
            $aEmail = Phpfox::getService('coupon.mail')->getEmailMessageFromTemplate(Phpfox::getService('coupon.mail')->getTypesCode('couponapproved_owner'), $aCoupon['coupon_id']);

            Phpfox::getService('coupon.mail.send')->send($aEmail['subject'], $aEmail['message'], Phpfox::getService('coupon')->getOwnerEmail($aCoupon['user_id']));

        }

        $this->database()->update($this->_sTable, $aUpdate,'coupon_id = ' . $aCoupon['coupon_id']);
    
        (($sPlugin = Phpfox_Plugin::get('coupon.service_process_publish_end')) ? eval($sPlugin) : false);
    
        return TRUE;
    }

    public function publishForPaymentIsZero($iCouponId) 
    {
            if(!$iCouponId)
            {
                return FALSE;               
            }
        
            $bAutoApprove = (bool)Phpfox::getUserParam('coupon.auto_approve_after_publish');
            $aCoupon = Phpfox::getService('coupon')->getCouponById($iCouponId);

            $aUpdate = array(
                'is_draft' => 0,
                'is_featured' => $aCoupon['is_featured'],
            );
        
            if ($bAutoApprove|| $aCoupon['module_id'] == 'pages') {
                $aUpdate['is_approved'] = 1;
                $aUpdate['status'] = Phpfox::getService('coupon')->getStatusCode('running');
            }
            else
            {
                $aUpdate['is_approved'] = 0;
                $aUpdate['status'] = Phpfox::getService('coupon')->getStatusCode('pending');
            }
        
            if($aUpdate['is_approved'])
            {
                $aCallback = ((!empty($aCoupon['module_id']) && $aCoupon['module_id'] != 'coupon') ? Phpfox::getService('coupon')->getCouponAddCallback($aCoupon['item_id'],$aCoupon['module_id']) : null);
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback($aCallback)->allowGuest()->add('coupon', $aCoupon['coupon_id'], $aCoupon['privacy'], (isset($aCoupon['privacy_comment']) ? (int) $aCoupon['privacy_comment'] : 0), (isset($aCoupon['item_id']) ? (int) $aCoupon['item_id'] : 0), $aCoupon['user_id']) : null);

            $this->database()->update(Phpfox::getT('feed'),array('user_id' => $aCoupon['user_id']), "type_id = 'coupon' AND item_id = {$aCoupon['coupon_id']}");
        
                Phpfox::getService('user.activity')->update($aCoupon['user_id'], 'coupon');
    
            $aEmail = Phpfox::getService('coupon.mail')->getEmailMessageFromTemplate(Phpfox::getService('coupon.mail')->getTypesCode('couponapproved_owner'), $aCoupon['coupon_id']);

            Phpfox::getService('coupon.mail.send')->send($aEmail['subject'], $aEmail['message'], Phpfox::getService('coupon')->getOwnerEmail($aCoupon['user_id']));
            }
    
            $this->database()->update($this->_sTable, $aUpdate,'coupon_id = ' . $aCoupon['coupon_id']);
        
        (($sPlugin = Phpfox_Plugin::get('coupon.service_process_publish_end')) ? eval($sPlugin) : false);
        
            return TRUE;
    }
	
	public function pay($iCouponId, $iPayType = 1)
    {
        $oTransaction = Phpfox::getService('coupon.transaction');
        $aCoupon = Phpfox::getService('coupon')->getCouponById($iCouponId);
        $sGateway = 'paypal';
        $sUrl = urlencode(Phpfox::getLib('url')->permalink('coupon.detail', $aCoupon['coupon_id'], $aCoupon['title']));

        switch($iPayType)
        {
            case 1:
            if((int)$this->_iPublishFee > 0 || ($aCoupon['is_featured'] && (int)$this->_iFeatureFee > 0))
            {
            	return true;    
            } else 
            {
                return false;   
                        
            }
                break;
            case 2:
            if((int)$this->_iFeatureFee > 0)
            {
            	return true;
            } else 
            {
                return false;                               
            }
        }

        return true;
    }

    /**
     * redirect to paypal
     * @by : datlv
     * @param $iCouponId
     * @return bool
     */
    public function startPayment($iCouponId, $iPayType = 1)
    {
        $oTransaction = Phpfox::getService('coupon.transaction');
        $aCoupon = Phpfox::getService('coupon')->getCouponById($iCouponId);
        $sGateway = 'paypal';
        $sUrl = urlencode(Phpfox::getLib('url')->permalink('coupon.detail', $aCoupon['coupon_id'], $aCoupon['title']));
        switch($iPayType)
        {
            case 1:
            if((int)$this->_iPublishFee > 0 || ($aCoupon['is_featured'] && (int)$this->_iFeatureFee > 0))
            {
                    $aInvoice = array(
                        'is_featured' => $aCoupon['is_featured'],
                        'full_name' => Phpfox::isUser() ? '' : Phpfox::getUserField('fullname'),
                        'email_address' => Phpfox::isUser() ? '' : Phpfox::getUserField('email'),
                        'amount' => $aCoupon['is_featured'] ? $this->_iPublishFee + $this->_iFeatureFee : $this->_iPublishFee,
                        'auto_approved' => Phpfox::getUserParam('coupon.auto_approve_after_publish'),
                        'can_approved' => Phpfox::getUserParam('coupon.can_approve_coupon'),
                        'pay_type' => $iPayType,
                    );
                
            } else 
            {
                return false;   
                        
            }
                break;
            case 2:
            if((int)$this->_iFeatureFee > 0)
            {
                    $aInvoice = array(
                        'is_featured' => $aCoupon['is_featured'],
                        'full_name' => Phpfox::isUser() ? '' : Phpfox::getUserField('fullname'),
                        'email_address' => Phpfox::isUser() ? '' : Phpfox::getUserField('email'),
                        'amount' => $this->_iFeatureFee,
                        'pay_type' => $iPayType,
                    );
            } else 
            {
                return false;                               
            }
        }

        if($iPayType == 1 && $aCoupon['is_featured'])
            $iPayType = 3;
    

        $aInsert = array(
            'invoice' => serialize($aInvoice),
            'coupon_id' => $aCoupon['coupon_id'],
            'time_stamp' => PHPFOX_TIME,
            'status' => $oTransaction->getStatusCode('initialized'),
            'amount' => $aInvoice['amount'],
            'currency' => $this->_unitCurrencyFee,
            'user_id' => $aCoupon['user_id'],
            'payment_type' => $iPayType,
        );

        $iTransactionId = Phpfox::getService('coupon.transaction.process')->add($aInsert);

		$sCorePath = Phpfox::getParam('core.path');
        $sCorePath = str_replace("index.php".PHPFOX_DS, "", $sCorePath);
        $sCorePath .= 'PF.Base'.PHPFOX_DS;
		
		$item_name = ($aCoupon['is_featured']) ? _p('publish_featured_coupon') : (($iPayType == 2) ? _p('feature_coupon') : _p('publish_coupon'));
		$aParams = array(
			'item_number' 	=> 'coupon|' . $iTransactionId,
            'currency_code' => $this->_unitCurrencyFee,
            'amount' 		=> $aInsert['amount'],
            'item_name' 	=> $item_name,                                
            'return' 		=> $sCorePath . 'module/coupon/static/complete.php?sLocation=' . $sUrl,
            'recurring' 	=> '',
            'recurring_cost' 	=> '',
            'alternative_cost' 	=> '',
            'alternative_recurring_cost' 	=> ''
		);
		
		return $aParams;
    }

    /**
     * process to upload image for coupon
     * @by : datlv
     * @param $iCouponId
     * @param $aVals
     * @param $oFile
     * @param $oImage
     */
    public function upload($iCouponId, $aVals, $oFile, $oImage)
    {
        $sFileName = $this->database()->select('image_path')->from($this->_sTable)->where('coupon_id = ' . $iCouponId)->execute('getSlaveField');

        // calculate space used
        if (!empty($sFileName))
        {
            // check if the file exists and get its size
            if (file_exists(Phpfox::getParam('core.dir_pic'). 'coupon/' . sprintf($sFileName, '')))
            {
                $oFile->unlink(Phpfox::getParam('core.dir_pic') . 'coupon/' . sprintf($sFileName, ''));
                $aSize = array(100, 200, 400);
                foreach($aSize as $iSize)
                {
                    $oFile->unlink(Phpfox::getParam('core.dir_pic') . 'coupon/' . sprintf($sFileName, '_' . $iSize));
                    $oFile->unlink(Phpfox::getParam('core.dir_pic') . 'coupon/' . sprintf($sFileName, '_' . $iSize . '_square'));
                }
            }
        }

        if (is_bool($iCouponId)) $iCouponId = (int)$aVals['coupon_id'];

        $sFileName = $oFile->upload('image', Phpfox::getParam('core.dir_pic') . 'coupon/', $iCouponId);
        // update the coupon
        $this->database()->update($this->_sTable, array('image_path' => 'coupon/' . $sFileName), 'coupon_id = ' . $iCouponId);
        // now the thumbnails
        $aSizes = array(100, 200, 400);
        foreach ($aSizes as $iSize)
        {
            $oImage->createThumbnail(Phpfox::getParam('core.dir_pic') . "coupon/" . sprintf($sFileName, ''), Phpfox::getParam('core.dir_pic') . "coupon/" . sprintf($sFileName, '_' . $iSize), $iSize, $iSize);
            $oImage->createThumbnail(Phpfox::getParam('core.dir_pic') . "coupon/" . sprintf($sFileName, ''), Phpfox::getParam('core.dir_pic') . "coupon/" . sprintf($sFileName, '_' . $iSize . '_square'), $iSize, $iSize, false);
        }

        $this->database()->update($this->_sTable, array('server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')), 'coupon_id = ' . (int) $iCouponId);
    }

    /** 
     * Add Claim into system
     * @author TienNPL
     * @param <string> claim code
     * @return <int> claim id
     */
    public function addClaim($iCouponId, $sCode)
    {
        //Init
        $aInsert = array(
            'coupon_id' => $iCouponId,
            'user_id'   => Phpfox::getUserId(),
            'time_stamp'=> PHPFOX_TIME,
            'code'      => $sCode
        );
        
        // Process
        $iId = $this->database()->insert(Phpfox::getT('coupon_claim'), $aInsert);
        
        // Update counter
        $this->database()->updateCounter('coupon','total_claim','coupon_id',$iCouponId);
        
        $oCoupon = Phpfox::getService("coupon");
        
        $iOwnerId = $oCoupon->getCouponOwnerId($iCouponId);
        if($iOwnerId)
        {
            // Add notification to owner
            Phpfox::getService("notification.process")->add("coupon_claim", $iCouponId, $iOwnerId);
            
            // Add notification for followers
            $aFollowerIds = Phpfox::getService("coupon")->getFollowerIds($iCouponId);
            if($aFollowerIds)
            {
                foreach($aFollowerIds as $aFollowerId)
                {
                    Phpfox::getService("notification.process")->add("coupon_claim", $iCouponId, $aFollowerId['user_id']);
                }
            }
        }
            
        // Push mail to queue to send to owner
        $aOwnerEmail =  array($oCoupon->getOwnerEmail($iOwnerId));
        Phpfox::getService('coupon.helper')->pushMailToQueue($iCouponId, 'couponclaimed_owner', $aOwnerEmail, Phpfox::getUserId());
        
        // Push mail to queue to send to claimer
        $aClaimer = Phpfox::getService('user')->getUser(Phpfox::getUserId());
        if($aClaimer)
        {
            $aClaimerEmail = array($aClaimer['email']);
            Phpfox::getService('coupon.helper')->pushMailToQueue($iCouponId, 'couponclaimed_claimer', $aClaimerEmail, Phpfox::getUserId());
        }
        // Check quantity and total claim
        $aCoupon = $this->database()->select('coupon_id,quantity,total_claim')->from($this->_sTable)->where("coupon_id = {$iCouponId}")->execute("getSlaveRow");
        
        if($aCoupon['quantity'] > 0)
        {
            if($aCoupon['total_claim'] >= $aCoupon['quantity'])
            {
                $this->close($aCoupon['coupon_id']);
            }
        }
        return $iId;
    }
    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */

    public function CheckAllCouponsStatus($sCustomeCond = '')
    {
        $sStatusCheck = '(' . Phpfox::getService('coupon')->getStatusCode('running') . ',' . Phpfox::getService('coupon')->getStatusCode('endingsoon') . ',' . Phpfox::getService('coupon')->getStatusCode('upcoming') . ')';
        $sCond = 'status IN ' . $sStatusCheck;

        if(!empty($sCustomeCond))
            $sCond .= $sCustomeCond;

        $aRows = $this->database()->select('*')->from($this->_sTable)->where($sCond)->execute('getRows');

        if(!$aRows)
            return false;

        foreach($aRows as $aRow)
        {
            $iStatus = Phpfox::getService('coupon')->checkCurrentStatus($aRow['coupon_id']);

            if($aRow['status'] != $iStatus)
            {
                $aUpdate = array( 'status' => $iStatus );
                if($iStatus == Phpfox::getService('coupon')->getStatusCode('closed'))
                    $this->close($aRow['coupon_id']);
                elseif($iStatus == Phpfox::getService('coupon')->getStatusCode('running'))
                    $this->run($aRow['coupon_id']);
                else
                    $this->database()->update($this->_sTable, $aUpdate, 'coupon_id = ' . $aRow['coupon_id']);

            }
        }
    }

    public function CheckCouponCodeFormat($aVals)
    {
        /*$sCharset = "/[\\'^£$%&*()}{@#~?><>,|=_+¬-]/";*/
        
        if(strlen($aVals['code_setting']) < 1 || strlen($aVals['code_setting']) > 30)
        {
            return false;
        }

        return true;
    }
    
    public function updateTotalView($iCouponId)
    {
        $this->database()->updateCounter('coupon','total_view','coupon_id',$iCouponId);
    }
    
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('coupon.service_process__call'))
        {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}

?>
