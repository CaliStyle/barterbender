<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/25/16
 * Time: 11:13 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Service_Product_Process extends Phpfox_Service
{
    private $_aProductCategories = array();
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ecommerce_product');
    }

    public function featureProduct($iProductId, $bIsFeatured)
    {
        $aItem = Phpfox::getService('ynsocialstore.product')->getProductById($iProductId, true);
        if($bIsFeatured == 0)
            Phpfox::getService("notification.process")->add("ynsocialstore_adminfeatureproduct",$iProductId, $aItem['user_id'] , Phpfox::getUserId());
        else
            Phpfox::getService("notification.process")->add("ynsocialstore_adminunfeatureproduct",$iProductId, $aItem['user_id'] , Phpfox::getUserId());

        if ($bIsFeatured) {
            $this->database()->update($this->_sTable, array('feature_end_time' => 0, 'feature_start_time' => 0), "product_id = {$iProductId}");
        } else {
            $this->database()->update($this->_sTable, array('feature_end_time' => 1, 'feature_start_time' => PHPFOX_TIME), "product_id = {$iProductId}");
        }
    }

    public function delete($iProductId)
    {
        $aRow = $this->database()->select('item_id, product_status')
            ->from($this->_sTable)
            ->where('product_id = '. $iProductId)
            ->execute('getRow');

        $this->database()->update($this->_sTable, array('product_status' => 'deleted'), "product_id = {$iProductId}");

        // Delete category data of this product
        $this->database()->delete(Phpfox::getT('ecommerce_category_data'), "product_id = {$iProductId} AND product_type = 'ynsocialstore_product'");

        // Delete this product in wish list
        $this->database()->delete(Phpfox::getT('ecommerce_product_ynstore_wishlist'), "product_id = {$iProductId}");

        if ($aRow['product_status'] == 'running')
        {
            // Update total product
            $this->database()->updateCounter('ynstore_store', 'total_products', 'store_id', $aRow['item_id'], true);
        }

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('ynsocialstore_product', (int)$iProductId) : null);
    }

    public function closeProduct($iProductId, $bIsAutoClose = false)
    {
        $aProduct = Phpfox::getService('ynsocialstore.product')->getProductById($iProductId);
        $this->database()->updateCounter('ynstore_store', 'total_products', 'store_id', $aProduct['item_id'], true);
        $this->database()->update($this->_sTable, array('product_status' => 'paused'), "product_id = {$iProductId}");
        if ($bIsAutoClose)
        {
            Phpfox::getService("notification.process")->add("ynsocialstore_closedProduct", $aProduct['product_id'], $aProduct['user_id'], $aProduct['user_id'], true);
        }
    }

    public function reopenProduct($iProductId)
    {
        $aProduct = Phpfox::getService('ynsocialstore.product')->getProductById($iProductId);
        $aSubscribers = Phpfox::getService('ynsocialstore.product')->getAllSubscriberOfProduc($iProductId);

        if(count($aSubscribers))
        {
            $sLink = Phpfox_Url::instance()->permalink('ynsocialstore.product',$aProduct['product_id'],$aProduct['name']);
            $sSubject = _p('product_product_name_is_now_reopen_to_buy',['product_name'=> $aProduct['name']]);
            $sText = _p('product_product_name_is_now_reopen_and_allow_to_buy_let_s_come_and_get_it_please_follow_this_link_link',['product_name' => $aProduct['name'],'link' => $sLink]);

            foreach($aSubscribers as $aSubscriber)
            {
                $aVal = array(
                    'email_message' => Phpfox::getLib('parse.input')->prepare($sText),
                    'email_subject' => $sSubject,
                    'product_id' => $iProductId,
                    'receivers' => serialize($aSubscriber['email']),
                    'is_sent' => 0,
                    'time_stamp' => PHPFOX_TIME
                );
                Phpfox::getService('ecommerce.mail.process')->saveEmailToQueue($aVal);
                $this->database()->update(Phpfox::getT('ecommerce_product_ynstore_subscribers'),['is_send' => 1],'subcriber_id ='.$aSubscriber['subcriber_id']);
            }
        }
        $this->database()->updateCounter('ynstore_store', 'total_products', 'store_id', $aProduct['item_id']);
        $this->database()->update($this->_sTable, array('product_status' => 'running'), "product_id = {$iProductId}");
    }

    public function denyProduct($iProductId)
    {
        $aProduct = Phpfox::getService('ynsocialstore.product')->getProductById($iProductId);
        Phpfox::getService("notification.process")->add("ynsocialstore_denyproduct",$iProductId, $aProduct['user_id'], Phpfox::getUserId());
        //Send mail
        $sSiteName = Phpfox::getParam('core.site_title');
        $sLink = Phpfox_Url::instance()->permalink('ynsocialstore.product',$aProduct['product_id'],$aProduct['name']);
        $sSubject = _p('your_product_in_site_name_site_is_denied',['site_name'=> $sSiteName]);
        $sText = _p('your_product_product_name_is_denied_by_sender_for_more_detail_please_view_this_link_link',['product_name' => $aProduct['name'],'sender' => Phpfox::getService('ynsocialstore')->getUserFullName(Phpfox::getUserId()),'link' => $sLink]);
        Phpfox::getService('ynsocialstore.process')->sendMail($aProduct['user_id'],$sText,$sSubject);

        $this->database()->update($this->_sTable, array('product_status' => 'denied'), "product_id = {$iProductId}");
    }

    public function approveProduct($iProductId)
    {
        $this->database()->update($this->_sTable, array('product_status' => 'running'), "product_id = {$iProductId}");
        Phpfox::getService('ecommerce.process')->approveProduct($iProductId,null,'ynsocialstore_product',true);
    }

    public function deleteMultiple($aDeleteIds)
    {
        foreach ($aDeleteIds as $iDeleteId) {
            if (is_numeric($iDeleteId)) {
                $this->delete($iDeleteId);
            }
        }

    }

    public function addProduct($aVals)
    {
        $oFilter = Phpfox::getLib('parse.input');
        // check if the user entered a forbidden word
        Phpfox::getService('ban')->checkAutomaticBan($aVals['name']);
        $aUOMs = Phpfox::getService('ecommerce.uom')->getAll();

        if (!count($aUOMs)) {
            return Phpfox_Error::set(_p('please_input_uom_unit_in_admincp'));
        }
        else{
            $aStore =  Phpfox::getService('ynsocialstore')->getStoreById($aVals['store_id']);
            if(!$aStore || count($aStore) == 0)
            {
                return Phpfox_Error::set(_p('unable_to_find_the_store_you_are_looking_for'));
            }
            else{
                $iCheck = Phpfox::getService('ynsocialstore.permission')->canCreateProduct($aStore);
                if ($iCheck == 0) {
                    return Phpfox_Error::set(_p('you_do_not_have_permission_to_create_product_in_this_store'));
                } elseif ($iCheck == 1) {
                    return Phpfox_Error::set(_p('you_have_reached_your_creating_product_limit_with_current_your_store_package_please_upgrade_to_other_package'));
                }
            }

        }
        if (!$this->getCategoriesFromForm($aVals)) {
            return Phpfox_Error::set(_p('provide_a_category_this_item_will_belong_to'));
        }
        $aVals['categories'] = $this->_aProductCategories;
        if((int)$aVals['discount_value'] > 0 && $aVals['discount_type'] == 'amount' && $aVals['discount_value'] > $aVals['product_price'])
        {
            return Phpfox_Error::set(_p('discount_price_must_be_less_than_or_equal_to_product_price'));
        }
        if((int)$aVals['discount_value'] > 0 && $aVals['discount_type'] == 'percentage' && (int)$aVals['discount_value'] > 100)
        {
            return Phpfox_Error::set(_p('discount_percentage_must_be_less_than_or_equal_to_100_percent'));
        }
        if(isset($aVals['enable_inventory']) && (int)$aVals['enable_inventory'] == 1 && $aVals['product_type'] == 'physical' && (int)$aVals['min_order'] > (int)$aVals['max_order'] && $aVals['max_order'] > 0)
        {
            return Phpfox_Error::set(_p('minimum_order_quantity_must_be_less_than_or_equal_to_maximum_order_quantity'));
        }
        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }

        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }

        if (!isset($aVals['privacy_photo'])) {
            $aVals['privacy_photo'] = 0;
        }

        if (!isset($aVals['privacy_video'])) {
            $aVals['privacy_video'] = 0;
        }
        $aVals['name'] = $oFilter->clean($aVals['name'], 255);
        if(!isset($aVals['discount_timeless'])) {
            $iStartTime = Phpfox::getLib('date')->mktime(0, 0, 0, $aVals['start_time_month'], $aVals['start_time_day'], $aVals['start_time_year']);
            $iEndTime = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['end_time_month'], $aVals['end_time_day'], $aVals['end_time_year']);

            if ($iEndTime < $iStartTime) {
                return Phpfox_Error::set(_p('please_edit_discount_end_date_after_start_date'));
            }


        }
        $aVals['start_time'] = 0;
        $aVals['end_time'] = 0;
        if (!Phpfox_Error::isPassed()) {
            return false;
        }
        $aVals['quantity'] = ((int)$aVals['product_quantity_main'] > 0 && $aVals['enable_inventory']) ? $aVals['product_quantity_main'] : NULL;
        $aVals['module_id'] = 'ynsocialstore';
        $aVals['item_id'] = $aVals['store_id'];
        $aVals['shipping'] = '';
        if ($aVals['product_type'] == 'digital')
            $aVals['uom'] = 0;
        $iProductId = Phpfox::getService('ecommerce.process')->add($aVals, 'ynsocialstore_product');

        if (!Phpfox_Error::isPassed()) {
            return false;
        }

        $aVals['discount_percentage'] = $aVals['discount_price'] = 0;
        if($aVals['discount_type'] == 'amount' && (float)$aVals['product_price'] > 0)
        {
            if($aVals['discount_value'] < 0.01) {
                $aVals['discount_value'] = 0;
                $aVals['discount_percentage'] = 0;
            }
            else {
                $aVals['discount_percentage'] = ($aVals['discount_value']/$aVals['product_price']) * 100;
                if($aVals['discount_percentage'] < 0.01) {
                    $aVals['discount_percentage'] = 0;
                }
            }

        }
        elseif($aVals['discount_type'] == 'percentage'){
            if($aVals['discount_value'] < 0.01) {
                $aVals['discount_value'] = 0;
                $aVals['discount_price'] = 0;
            }
            else {
                $aVals['discount_price'] = (float)(($aVals['product_price']*$aVals['discount_value'])/100);
                if($aVals['discount_price'] < 0.01) {
                    $aVals['discount_price'] = 0;
                }
            }
        }

        $aInsert = array(
            'product_id' => $iProductId,
            'product_type' => $aVals['product_type'],
            'enable_inventory' => isset($aVals['enable_inventory']) ? $aVals['enable_inventory'] : 0,
            'min_order' => (isset($aVals['enable_inventory']) && $aVals['enable_inventory'] && (int)$aVals['min_order'] > 0) ? (int)$aVals['min_order'] : 0,
            'max_order' => (isset($aVals['enable_inventory']) && $aVals['enable_inventory'] && (int)$aVals['max_order'] > 0) ? (int)$aVals['max_order'] : 0,
            'link' => !empty($aVals['link_download']) ? $aVals['link_download'] : NULL,
            'discount_type' => isset($aVals['discount_type']) ? $aVals['discount_type'] : 'amount',
            'discount_price' => ($aVals['discount_value'] > 0 && $aVals['discount_type'] == 'amount') ? $aVals['discount_value'] : $aVals['discount_price'],
            'discount_percentage' =>($aVals['discount_value'] > 0 && $aVals['discount_type'] == 'percentage') ? $aVals['discount_value'] : $aVals['discount_percentage'],
            'discount_start_date' => isset($iStartTime) ? $iStartTime : 0,
            'discount_end_date' => isset($iEndTime) ? $iEndTime : 0,
            'discount_timeless' => isset($aVals['discount_timeless']) ? 1 : 0,
            'auto_close' => isset($aVals['auto_close']) ? $aVals['auto_close'] : 0,
        );
        if ($aVals['privacy'] == '4')
        {
            Phpfox::getService('privacy.process')->add('ynsocialstore_product', $iProductId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
        }
        $this->database()->insert(Phpfox::getT('ecommerce_product_ynstore'), $aInsert);
        return $iProductId;
    }
    public function getCategoriesFromForm($aVals)
    {
        if (isset($aVals['category']) && count($aVals['category'])) {
            if (empty($aVals['category'][0])) {
                return false;
            } else if (!is_array($aVals['category'])) {
                $this->_aProductCategories[] = $aVals['category'];
            } else {
                foreach ($aVals['category'] as $aCategory) {

                    foreach ($aCategory as $iCategory) {
                        if (empty($iCategory)) {
                            continue;
                        }

                        if (!is_numeric($iCategory)) {
                            continue;
                        }

                        $this->_aProductCategories[] = $iCategory;
                    }
                }
            }
            return true;
        }
    }
    public function updateProduct($iProductId, $aVals)
    {
        $oFilter = Phpfox::getLib('parse.input');
        // check if the user entered a forbidden word
        Phpfox::getService('ban')->checkAutomaticBan($aVals['name']);
        $aProduct = Phpfox::getService('ynsocialstore.product')->getProductById($iProductId);
        $aUOMs = Phpfox::getService('ecommerce.uom')->getAll();

        if (!count($aUOMs)) {
            return Phpfox_Error::set(_p('please_input_uom_unit_in_admincp'));
        }
        else{
            $aStore =  Phpfox::getService('ynsocialstore')->getStoreById($aVals['store_id']);
            if(!$aStore || count($aStore) == 0)
            {
                return Phpfox_Error::set(_p('unable_to_find_the_store_you_are_looking_for'));
            }
            else{
                $aEditProduct = Phpfox::getService('ynsocialstore.product')->getProductForEdit($iProductId);
                if(!Phpfox::getService('ynsocialstore.permission')->canEditProduct(false,$aEditProduct['user_id']))
                {
                    return Phpfox_Error::set(_p('you_do_not_have_permission_to_edit_this_product'));
                }
            }

        }
        if (!$this->getCategoriesFromForm($aVals)) {
            return Phpfox_Error::set(_p('provide_a_category_this_item_will_belong_to'));
        }
        $aVals['categories'] = $this->_aProductCategories;
        if((int)$aVals['discount_value'] > 0 && $aVals['discount_type'] == 'amount' && $aVals['discount_value'] > $aVals['product_price'])
        {
            return Phpfox_Error::set(_p('discount_price_must_be_less_than_or_equal_to_product_price'));
        }
        if((int)$aVals['discount_value'] > 0 && $aVals['discount_type'] == 'percentage' && (int)$aVals['discount_value'] > 100)
        {
            return Phpfox_Error::set(_p('discount_percentage_must_be_less_than_or_equal_to_100_percent'));
        }
        if(isset($aVals['enable_inventory']) && (int)$aVals['enable_inventory'] == 1 && $aVals['product_type'] == 'physical' && (int)$aVals['min_order'] > (int)$aVals['max_order'] && $aVals['max_order'] > 0)
        {
            return Phpfox_Error::set(_p('minimum_order_quantity_must_be_less_than_or_equal_to_maximum_order_quantity'));
        }
        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }

        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }

        if (!isset($aVals['privacy_photo'])) {
            $aVals['privacy_photo'] = 0;
        }

        if (!isset($aVals['privacy_video'])) {
            $aVals['privacy_video'] = 0;
        }
        $aVals['name'] = $oFilter->clean($aVals['name'], 255);
        if(!isset($aVals['discount_timeless'])) {
            $iStartTime = Phpfox::getLib('date')->mktime(0, 0, 0, $aVals['start_time_month'], $aVals['start_time_day'], $aVals['start_time_year']);
            $iEndTime = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['end_time_month'], $aVals['end_time_day'], $aVals['end_time_year']);

            if ($iEndTime < $iStartTime) {
                return Phpfox_Error::set(_p('please_edit_discount_end_date_after_start_date'));
            }


        }
        $aVals['start_time'] = 0;
        $aVals['end_time'] = 0;
        if (!Phpfox_Error::isPassed()) {
            return false;
        }
        $aVals['quantity_remain'] = $aVals['quantity'] = ((int)$aVals['product_quantity_main'] > 0 && $aVals['enable_inventory']) ? $aVals['product_quantity_main'] : NULL;
        $aVals['module_id'] = 'ynsocialstore';
        $aVals['item_id'] = $aVals['store_id'];
        $aVals['shipping'] = '';
        if ($aVals['product_type'] == 'digital')
            $aVals['uom'] = 0;

        /*insert into product ecommerce table*/
        Phpfox::getService('ecommerce.process')->update($aVals, $iProductId, 'ynsocialstore_product');
        if (!Phpfox_Error::isPassed()) {
            return false;
        }
        $aVals['discount_percentage'] = $aVals['discount_price'] = 0;
        if($aVals['discount_type'] == 'amount' && (float)$aVals['product_price'] > 0)
        {
            if($aVals['discount_value'] < 0.01) {
                $aVals['discount_value'] = 0;
                $aVals['discount_percentage'] = 0;
            }
            else {
                $aVals['discount_percentage'] = ($aVals['discount_value']/$aVals['product_price']) * 100;
                if($aVals['discount_percentage'] < 0.01) {
                    $aVals['discount_percentage'] = 0;
                }
            }

        }
        elseif($aVals['discount_type'] == 'percentage'){
            if($aVals['discount_value'] < 0.01) {
                $aVals['discount_value'] = 0;
                $aVals['discount_price'] = 0;
            }
            else {
                $aVals['discount_price'] = (float)(($aVals['product_price']*$aVals['discount_value'])/100);
                if($aVals['discount_price'] < 0.01) {
                    $aVals['discount_price'] = 0;
                }
            }
        }
        $aInsert = array(
            'product_type' => $aVals['product_type'],
            'enable_inventory' => isset($aVals['enable_inventory']) ? $aVals['enable_inventory'] : 0,
            'min_order' => (isset($aVals['enable_inventory']) && $aVals['enable_inventory'] && (int)$aVals['min_order'] > 0) ? (int)$aVals['min_order'] : 0,
            'max_order' => (isset($aVals['enable_inventory']) && $aVals['enable_inventory'] && (int)$aVals['max_order'] > 0) ? (int)$aVals['max_order'] : 0,
            'link' => !empty($aVals['link_download']) ? $aVals['link_download'] : NULL,
            'discount_type' => isset($aVals['discount_type']) ? $aVals['discount_type'] : 'amount',
            'discount_price' => ($aVals['discount_value'] > 0 && $aVals['discount_type'] == 'amount') ? $aVals['discount_value'] : $aVals['discount_price'],
            'discount_percentage' =>($aVals['discount_value'] > 0 && $aVals['discount_type'] == 'percentage') ? $aVals['discount_value'] : $aVals['discount_percentage'],
            'discount_start_date' => isset($iStartTime) ? $iStartTime : 0,
            'discount_end_date' => isset($iEndTime) ? $iEndTime : 0,
            'discount_timeless' => isset($aVals['discount_timeless']) ? 1 : 0,
            'auto_close' => isset($aVals['auto_close']) ? $aVals['auto_close'] : 0,
        );
        if (Phpfox::isModule('privacy'))
        {
            if ($aVals['privacy'] == '4')
            {
                Phpfox::getService('privacy.process')->update('ynsocialstore_product', $iProductId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
            }
            else
            {
                Phpfox::getService('privacy.process')->delete('ynsocialstore_product', $iProductId);
            }
        }
        $this->database()->update(Phpfox::getT('ecommerce_product_ynstore'), $aInsert,'product_id ='.$iProductId);
        return $iProductId;
    }
    public function setCookieRecentViewProduct($iProductId)
    {
        $expired_time_seconds = 2628000;
        $sRecentlyViewed = Phpfox::getCookie('ynsocialstore_recently_viewed_product');
        if (empty($sRecentlyViewed)) {
            Phpfox::setCookie('ynsocialstore_recently_viewed_product', $iProductId, $expired_time_seconds);
        } else {
            $aRecentlyViewed = explode(',', $sRecentlyViewed);
            if (!in_array($iProductId, $aRecentlyViewed)) {
                array_unshift($aRecentlyViewed,$iProductId);
            } else {
                if (($key = array_search($iProductId, $aRecentlyViewed)) !== false) {
                    unset($aRecentlyViewed[$key]);
                    array_unshift($aRecentlyViewed,$iProductId);
                }
            }

            if (is_array($aRecentlyViewed)) {
                Phpfox::setCookie('ynsocialstore_recently_viewed_product',implode(',', $aRecentlyViewed), $expired_time_seconds);
            }
        }
        return true;
    }
    public function updateTotalView($iProductId)
    {
        $this->database()->updateCounter('ecommerce_product', 'total_view', 'product_id', $iProductId);
    }

    public function updateFeatureDay($iProductId, $iFeatureDays)
    {
        $this->database()->update(Phpfox::getT('ecommerce_product'), array('feature_day' => $iFeatureDays), 'product_creating_type = \'ynsocialstore_product\' AND product_id = '.$iProductId);
    }

    public function uploadImageAttribute($iAttributeId)
    {
        $oFile = Phpfox_File::instance();
        $oImage = Phpfox_Image::instance();
        $sPicStorage = Phpfox::getParam('core.dir_pic') . 'ynsocialstore/';

        if (!is_dir($sPicStorage)) {
            @mkdir($sPicStorage, 0777, 1);
            @chmod($sPicStorage, 0777);
        }

        $iSize = 90;

        $sFileName = $this->database()->select('image_path')->from(Phpfox::getT('ecommerce_product_attribute'))->where('attribute_id = ' . $iAttributeId)->execute('getSlaveField');

        // calculate space used
        if (!empty($sFileName))
        {
            // check if the file exists and get its size
            if (file_exists(Phpfox::getParam('core.dir_pic'). 'ynsocialstore/' . sprintf($sFileName, '')))
            {
                $oFile->unlink(Phpfox::getParam('core.dir_pic') . 'ynsocialstore/' . sprintf($sFileName, '_' . $iSize . '_square'));
            }
        }

        $aImage = $oFile->load('image', array('jpg', 'gif', 'png'));
        if (!Phpfox_Error::isPassed()) {
            return false;
        }

        if ($aImage === false) {
            return Phpfox_Error::set(_p('please_select_an_image_to_upload'));
        }
        $sFileName = $oFile->upload('image', Phpfox::getParam('core.dir_pic') . 'ynsocialstore/', $iAttributeId.rand());



        $oImage->createThumbnail($sPicStorage . sprintf($sFileName, ''), $sPicStorage . sprintf($sFileName, '_' . $iSize . '_square'), $iSize, $iSize, false);

        // update the product
        $this->database()->update(Phpfox::getT('ecommerce_product_attribute')
            , array(
                'image_path' => 'ynsocialstore/' . $sFileName,
                'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
            )
            , 'attribute_id = ' . $iAttributeId);
        @unlink($sPicStorage . sprintf($sFileName, ''));
    }

    public function saveElementAttribute($aVals, $iAttributeId = 0)
    {
        $aInsert = array(
            'product_id' => $aVals['product_id'],
            'title' => $aVals['name'],
            'quantity' => $aVals['remain'],
            'remain' => $aVals['remain'],
            'price' => $aVals['price'],
            'color' => $aVals['color'],
        );

        if($aVals['type'])
        {
            $aInsert['image_path'] = '';

        } else {
            $aInsert['color'] = '';
        }

        if ($iAttributeId)
        {
            $this->database()->update(Phpfox::getT('ecommerce_product_attribute'), $aInsert,'attribute_id ='.$iAttributeId);
        }
        else
        {
            $iAttributeId = $this->database()->insert(Phpfox::getT('ecommerce_product_attribute'), $aInsert);
        }

        if (!empty($aVals['image']) && $aVals['image']['error'] == 0)
        {
            $this->uploadImageAttribute($iAttributeId);
        }

        return $iAttributeId;
    }

    public function deleteElementAttr($iAttributeId)
    {
        $this->database()->update(Phpfox::getT('ecommerce_product_attribute'), array('is_deleted' => 1), "attribute_id = {$iAttributeId}");
    }

    public function saveAttribute($aVals)
    {
        $aInsert = array(
            'attribute_style' => $aVals['style'],
            'attribute_name' => $aVals['title'],
        );
        return $this->database()->update(Phpfox::getT('ecommerce_product_ynstore'), $aInsert,'product_id ='.$aVals['product_id']);
    }

    public function removeMainPhoto($iProductId)
    {
        return $this->database()->update(Phpfox::getT('ecommerce_product'), array('logo_path' => NULL),'product_id ='.$iProductId);
    }
    public function addProductSubscriber($aVal,$iProducId)
    {
        if(empty($aVal['email_inform']))
        {
            Phpfox_Error::set(_p('Please input an valid email.'));
            return false;
        }
        if($this->checkIsSubmitedEmail($aVal['email_inform']))
        {
            Phpfox_Error::set(_p('This email already submitted to this product.'));
            return false;
        }
        $aInsert = [
            'product_id' => (int)$iProducId,
            'email' => $aVal['email_inform'],
            'is_send' => 0,
            'time_stamp' => PHPFOX_TIME
        ];
        $iId = $this->database()->insert(Phpfox::getT('ecommerce_product_ynstore_subscribers'),$aInsert);
        return $iId;
    }
    public function checkIsSubmitedEmail($sEmail)
    {
        return $this->database()->select('subcriber_id')
                    ->from(Phpfox::getT('ecommerce_product_ynstore_subscribers'))
                    ->where('is_send = 0 AND email = "'.$sEmail.'"')
                    ->execute('getRow');
    }

}