<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/4/16
 * Time: 6:54 PM
 */
class Ynsocialstore_Service_Process extends Phpfox_Service
{

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynstore_store');
    }
    /**
     * @param $aVals
     * @param $sType
     * @return bool
     */
    public function activateFieldComparison($aVals, $sType)
    {
        $checkedField = isset($aVals['comparison_field']) ? $aVals['comparison_field'] : array();

        $this->database()->update(Phpfox::getT('ynstore_comparison'), array('enable' => 0), 'comparison_id <> 0 AND for_type = \''.$sType.'\'');

        if (count($checkedField) > 0) {
            $this->database()->update(Phpfox::getT('ynstore_comparison'), array('enable' => 1), 'comparison_id IN (' . implode(",", $checkedField) . ') AND for_type = \''.$sType.'\'');
        }

        return true;
    }

    /**
     * @param $iStoreId
     */
    public function delete($iStoreId)
    {
        $aStore = $this->database()->select('*')
            ->from($this->_sTable)
            ->where("store_id =" . (int)$iStoreId)
            ->execute('getRow');
        if(!$aStore)
            return true;
        if (!Phpfox::getService('ynsocialstore.permission')->canDeleteStore(false,$aStore['user_id'])) {
            return false;
        }
        if (!$aStore) {
            return false;
        }
        if ($aStore['total_orders'] == 0) {
            $oFile = Phpfox_File::instance();
            $sPicStorage = Phpfox::getParam('core.dir_pic') . 'ynsocialstore/';
            if (!empty($aStore['logo_path'])) {
                if (file_exists($sPicStorage . sprintf($aStore['logo_path'], '_480_square'))) {
                    $oFile->unlink($sPicStorage . sprintf($aStore['logo_path'], '_480_square'));
                }
                if (file_exists($sPicStorage . sprintf($aStore['logo_path'], '_90'))) {
                    $oFile->unlink($sPicStorage . sprintf($aStore['logo_path'], '_90'));
                }
                if (file_exists($sPicStorage . sprintf($aStore['logo_path'], '_140'))) {
                    $oFile->unlink($sPicStorage . sprintf($aStore['logo_path'], '_140'));
                }

            }
            if (!empty($aStore['cover_path'])) {
                if (file_exists($sPicStorage . sprintf($aStore['logo_path'], '_1024'))) {
                    $oFile->unlink($sPicStorage . sprintf($aStore['logo_path'], '_1024'));
                }
                if (file_exists($sPicStorage . sprintf($aStore['logo_path'], '_480'))) {
                    $oFile->unlink($sPicStorage . sprintf($aStore['logo_path'], '_480'));
                }
            }
            $this->database()->delete(Phpfox::getT('ynstore_store_favorite'), "store_id = " . (int)$iStoreId);
            $this->database()->delete(Phpfox::getT('ynstore_store_review'), "store_id = " . (int)$iStoreId);
            $this->database()->delete(Phpfox::getT('ynstore_store_following'), "store_id = " . (int)$iStoreId);
            $this->database()->delete(Phpfox::getT('ynstore_store_location'), "store_id = " . (int)$iStoreId);
            $this->database()->delete(Phpfox::getT('ynstore_store_infomation'), "store_id = " . (int)$iStoreId);
            $this->database()->delete(Phpfox::getT('ynstore_store_faq'), "store_id = " . (int)$iStoreId);
            if (!empty($aStore['categories'])) {
                $aCategories = json_decode($aStore['categories']);
                $this->database()->delete(Phpfox::getT('ecommerce_category_data'), 'product_id = ' . (int)$iStoreId . ' AND product_type ="store"');
                foreach ($aCategories as $key => $iCategory) {
                    $this->database()->updateCounter('ecommerce_category', 'used', 'category_id', $iCategory, true);
                }

            }
            $this->database()->updateCounter('ynstore_store_package', 'used', 'package_id', $aStore['package_id'], true);
            $this->database()->delete($this->_sTable, "store_id = " . (int)$iStoreId);
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('ynsocialstore_store', (int)$iStoreId) : null);

        }
        else
        {
            $this->database()->update($this->_sTable, array('status' => 'deleted'), "store_id = {$iStoreId}");
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('ynsocialstore_store', (int)$iStoreId) : null);

        }


    }


    /**
     * @param $iStoreId
     * @return bool
     */
    public function denyStore($iStoreId)
    {
        $aItem = Phpfox::getService('ynsocialstore')->getStoreById($iStoreId, true);
        Phpfox::getService("notification.process")->add("ynsocialstore_denystore",$iStoreId, $aItem['user_id'], Phpfox::getUserId());
        //Send mail
        $sSiteName = Phpfox::getParam('core.site_title');
        $sLink = Phpfox_Url::instance()->permalink('ynsocialstore.store',$aItem['store_id'],$aItem['name']);
        $sSubject = _p('your_store_in_site_name_site_is_denied',['site_name'=> $sSiteName]);
        $sText = _p('your_store_store_name_is_denied_by_sender_for_more_detail_please_view_this_link_link',['store_name' => $aItem['name'],'sender' => Phpfox::getService('ynsocialstore')->getUserFullName(Phpfox::getUserId()),'link' => $sLink]);
        $this->sendMail($aItem['user_id'],$sText,$sSubject);

        //update
        return $this->database()->update($this->_sTable, array('status' => 'denied'), "store_id = {$iStoreId}");
    }


    /**
     * @param $iStoreId
     * @param $bIsApproved
     */
    public function approveStore($iStoreId)
    {
        //TODO: add feed, notification
        $this->database()->update($this->_sTable, array('status' => 'public'), "store_id = {$iStoreId}");
        $this->approveStoreByPackage($iStoreId,true);
    }

    public function featureStore($iStoreId, $bIsFeatured)
    {
        $aItem = Phpfox::getService('ynsocialstore')->getStoreById($iStoreId, true);
        if($bIsFeatured == 0)
            Phpfox::getService("notification.process")->add("ynsocialstore_adminfeature",$iStoreId, $aItem['user_id'] , Phpfox::getUserId());
        else
            Phpfox::getService("notification.process")->add("ynsocialstore_adminunfeature",$iStoreId, $aItem['user_id'] , Phpfox::getUserId());
        $this->database()->update($this->_sTable, array('is_featured' => ($bIsFeatured == 1) ? 0 : 1,'feature_end_time' => 1), "store_id = {$iStoreId}");
    }

    public function reopenStore($iStoreId)
    {
        $aFollowers = Phpfox::getService('ynsocialstore.following')->getAllFollowingByStoreId($iStoreId);
        if($aFollowers) {
            $aItem = Phpfox::getService('ynsocialstore')->getStoreById($iStoreId, true);
            $sSiteName = Phpfox::getParam('core.site_title');
            $sLink = Phpfox_Url::instance()->permalink('ynsocialstore.store', $aItem['store_id'], $aItem['name']);
            $sSubject = _p('store_store_name_in_site_name_site_is_now_reopened', ['store_name' => $aItem['name'],'site_name' => $sSiteName]);
            $sText = _p('store_store_name_in_site_name_site_is_now_reopened_for_more_detail_please_view_this_link_link', ['store_name' => $aItem['name'], 'site_name' => $sSiteName, 'link' => $sLink]);

            foreach ($aFollowers as $aFollower) {
                Phpfox::getService("notification.process")->add("ynsocialstore_reopenedstore", $iStoreId, $aFollower['user_id'], Phpfox::getUserId());
            }
        }
        $this->database()->update($this->_sTable, array('status' => 'public'), "store_id = {$iStoreId}");
    }

    public function closeStore($iStoreId)
    {

        $aFollowers = Phpfox::getService('ynsocialstore.following')->getAllFollowingByStoreId($iStoreId);

        if($aFollowers) {
            $aItem = Phpfox::getService('ynsocialstore')->getStoreById($iStoreId, true);
            $sSiteName = Phpfox::getParam('core.site_title');
            $sLink = Phpfox_Url::instance()->permalink('ynsocialstore.store', $aItem['store_id'], $aItem['name']);
            $sSubject = _p('store_store_name_in_site_name_site_is_now_temporarily_closed', ['store_name' => $aItem['name'],'site_name' => $sSiteName]);
            $sText = _p('store_store_name_in_site_name_site_is_now_temporarily_closed_for_more_detail_please_view_this_link_link', ['store_name' => $aItem['name'], 'site_name' => $sSiteName, 'link' => $sLink]);

            foreach ($aFollowers as $aFollower) {
                $this->sendMail($aFollower['user_id'], $sText, $sSubject);
                Phpfox::getService("notification.process")->add("ynsocialstore_closedstore", $iStoreId, $aFollower['user_id'], Phpfox::getUserId());
            }
        }
        $this->database()->update($this->_sTable, array('status' => 'closed'), "store_id = {$iStoreId}");
    }
    public function addStore($aVals)
    {
        $oFilter = Phpfox::getLib('parse.input');
        // check if the user entered a forbidden word
        Phpfox::getService('ban')->checkAutomaticBan($aVals['name']);
        if (!isset($aVals['category']) || count($aVals['category']) < 1) {
            return Phpfox_Error::set(_p('provide_a_category_this_item_will_belong_to'));
        }
        if(!isset($aVals['package_id']) || $aVals['package_id'] < 1)
        {
            return Phpfox_Error::set(_p('Package is not valid'));
        }
        else
        {
            $aPackage =  Phpfox::getService('ynsocialstore.package')->getById($aVals['package_id']);
            if(!$aPackage || count($aPackage) == 0)
            {
                return Phpfox_Error::set(_p('Can\'t find the package you are choosing'));
            }
        }
        foreach ($aVals['addinfo_title'] as $key => $val) {
            if (strlen(trim($aVals['addinfo_title'][$key])) > 150 || strlen(trim($aVals['addinfo_content'][$key])) > 150) {
                return Phpfox_Error::set(_p('additional_information').': '._p('please_enter_no_more_than_0_characters',['0'=> '150']));
            }
        }
        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }
        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }

        $oFile = Phpfox::getLib('file');


        $aInsert = array(
            'user_id' => Phpfox::getUserId(),
            'theme_id' => empty($aVals['theme']) ? 1 : $aVals['theme'],
            'name' => $oFilter->clean($aVals['name'], 255),
            'time_stamp' => PHPFOX_TIME,
            'module_id' => (isset($aVals['module_id']) ? $aVals['module_id'] : 'ynsocialstore'),
            'item_id' => (isset($aVals['item_id']) ? $aVals['item_id'] : '0'),
            'status' => 'draft', //will check status later
            'description' => (isset($aVals['description']) ? $oFilter->clean($aVals['description']) : ''),
            'description_parse' => (isset($aVals['description']) ? $oFilter->prepare($aVals['description']) : ''),
            'short_description' => (isset($aVals['short_description']) ? $oFilter->clean($aVals['short_description']) : ''),
            'country_iso' => (isset($aVals['country_iso']) ? $aVals['country_iso'] : ''),
            'country_child_id' => (isset($aVals['country_child_id']) ? $aVals['country_child_id'] : 0),
            'email' => (isset($aVals['email']) ? $aVals['email'] : ''),
            'city' => (isset($aVals['city']) ? $oFilter->clean(strip_tags($aVals['city'])) : ''),
            'province' => (isset($aVals['province']) ? $oFilter->clean(strip_tags($aVals['province'])) : ''),
            'postal_code' => (isset($aVals['zip_code']) ? $aVals['zip_code'] : ''),
            'feature_day' => 0,
            'feature_fee' => (int)$aPackage['feature_store_fee'],
            'feature_end_time' => 0, //will check later
            'privacy' => $aVals['privacy'] ,
            'categories' => json_encode($aVals['category']),
            'business_type' =>(isset($aVals['business_type'])) ? $aVals['business_type']: '0',
            'established_year' => (isset($aVals['established_year']) && (int)$aVals['established_year'] > 0) ? $aVals['established_year'] : 0,
            'ship_payment_info' => (isset($aVals['ship_payment_info']) && !empty($aVals['ship_payment_info'])) ? $oFilter->clean($aVals['ship_payment_info']) : '',
            'return_policy' => (isset($aVals['return_policy']) && !empty($aVals['ship_payment_info'])) ? $oFilter->clean($aVals['return_policy']) : '',
            'buyer_protection' => (isset($aVals['buyer_protection']) && !empty($aVals['buyer_protection'])) ? $oFilter->clean($aVals['buyer_protection']) : '',
            'tax' => (isset($aVals['tax']) && (int)$aVals['tax'] > 0) ? $aVals['tax'] : 0,
            'package_id' => (isset($aVals['package_id'])) ? $aVals['package_id'] : 0,
        );

        if (isset($aVals['package_id']) && (int)$aVals['package_id'] > 0) {

        }
        else{
            $aInsert = array_merge($aInsert, array(
                'start_time' => 0,
                'expire_time' => 0,
            ));
        }

        // Process upload
        $this->_processUploadForm($aVals, $aInsert);

        $iStoreId = $this->database()->insert($this->_sTable, $aInsert);

        // insert location 
        if (isset($aVals['location_fulladdress']) && count($aVals['location_fulladdress']) > 0) {
            foreach ($aVals['location_fulladdress'] as $key => $val) {
                if (strlen(trim($aVals['location_address'][$key])) != '') {
                    if (strlen(trim($aVals['location_title'][$key])) == 0) {
                        $aVals['location_title'][$key] = $aVals['location_address'][$key];
                    }
                    $aInsertLocation = array(
                        'store_id' => $iStoreId,
                        'title' => $oFilter->clean(strip_tags($aVals['location_title'][$key])),
                        'address' => $aVals['location_address'][$key],
                        'longitude' => $aVals['location_address_lng'][$key],
                        'latitude' => $aVals['location_address_lat'][$key],
                        'location' => $aVals['location'][$key],
                    );

                    $this->database()->insert(Phpfox::getT('ynstore_store_location'), $aInsertLocation);
                }
            }
        }
        // insert phone
        if (isset($aVals['phone']) && count($aVals['phone']) > 0) {
            foreach ($aVals['phone'] as $key => $val) {
                if (strlen(trim($val)) > 0) {
                    $aInsertPhone = array(
                        'store_id' => $iStoreId,
                        'type' => 'phone',
                        'info' => $oFilter->clean(strip_tags($val)),
                    );
                    $this->database()->insert(Phpfox::getT('ynstore_store_infomation'), $aInsertPhone);
                }
            }
        }
        // insert fax
        if (isset($aVals['fax']) && count($aVals['fax']) > 0) {
            foreach ($aVals['fax'] as $key => $val) {
                if (strlen(trim($val)) > 0) {
                    $aInsertFax = array(
                        'store_id' => $iStoreId,
                        'type' => 'fax',
                        'info' => $oFilter->clean(strip_tags($val)),
                    );
                    $this->database()->insert(Phpfox::getT('ynstore_store_infomation'), $aInsertFax);
                }
            }
        }
        // insert web
        if (isset($aVals['web_address']) && count($aVals['web_address']) > 0) {
            foreach ($aVals['web_address'] as $key => $val) {
                if (strlen(trim($val)) > 0) {
                    $aInsertWeb = array(
                        'store_id' => $iStoreId,
                        'type' => 'website',
                        'info' => $oFilter->clean(strip_tags($val)),
                    );
                    $this->database()->insert(Phpfox::getT('ynstore_store_infomation'), $aInsertWeb);
                }
            }
        }
        // insert addition information 
        if (isset($aVals['addinfo_title']) && count($aVals['addinfo_title']) > 0) {
            foreach ($aVals['addinfo_title'] as $key => $val) {
                if (strlen(trim($aVals['addinfo_title'][$key])) > 0) {

                    $aInsertInfo = array(
                        'store_id' => $iStoreId,
                        'title' => substr($oFilter->clean(strip_tags($aVals['addinfo_title'][$key])),0,149),
                        'info' => substr($oFilter->clean($aVals['addinfo_content'][$key]),0,149),
                        'type' => 'addinfo'
                    );

                    $this->database()->insert(Phpfox::getT('ynstore_store_infomation'), $aInsertInfo);
                }
            }
        }

        // insert category
        if (isset($aVals['category']) && count($aVals['category']))
        {

            foreach ($aVals['category'] as $key => $iCategoryId)
            {
                $data = array('product_id' => $iStoreId, 'category_id' => $iCategoryId,'product_type' => 'store');
                $data['is_main'] = 1;
                $this->database()->insert(Phpfox::getT('ecommerce_category_data'), $data);
                $this->database()->updateCounter('ecommerce_category','used','category_id',$iCategoryId);
            }
        }
        if ($aVals['privacy'] == '4')
        {
            Phpfox::getService('privacy.process')->add('ynsocialstore_store', $iStoreId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
        }

        $this->database()->updateCounter('ynstore_store_package','used','package_id',$aVals['package_id']);
        return $iStoreId;
    }
    public function updateStore($iStoreId, $aVals)
    {
        $oFilter = Phpfox::getLib('parse.input');
        if(isset($aVals['addinfo_title'])) {
            foreach ($aVals['addinfo_title'] as $key => $val) {
                if (strlen(trim($aVals['addinfo_title'][$key])) > 150 || strlen(trim($aVals['addinfo_content'][$key])) > 150) {
                    return Phpfox_Error::set(_p('additional_information') . ': ' . _p('please_enter_no_more_than_0_characters', ['0' => '150']));
                }
            }
        }
        // check if the user entered a forbidden word
        Phpfox::getService('ban')->checkAutomaticBan($aVals['name']);
        if (!isset($aVals['category']) || count($aVals['category']) < 1) {
            return Phpfox_Error::set(_p('provide_a_category_this_item_will_belong_to'));
        }
        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }
        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }  
        $oFile = Phpfox::getLib('file');
        //[1 = DRAFT | 2 = PENDING | 3 = PUBLISH | 4 = DENIED | 5 = CLOSED | 6 = DELETED]
        $aUpdate = array(
            'theme_id' => empty($aVals['theme']) ? 1 : $aVals['theme'],
            'name' => $oFilter->clean($aVals['name'], 255),
            'time_update' => PHPFOX_TIME,
            'description' => (isset($aVals['description']) ? $oFilter->clean($aVals['description']) : ''),
            'description_parse' => (isset($aVals['description']) ? $oFilter->prepare($aVals['description']) : ''),
            'short_description' => (isset($aVals['short_description']) ? $oFilter->clean($aVals['short_description']) : ''),
            'country_iso' => (isset($aVals['country_iso']) ? $aVals['country_iso'] : ''),
            'country_child_id' => (isset($aVals['country_child_id']) ? $aVals['country_child_id'] : 0),
            'email' => (isset($aVals['email']) ? $aVals['email'] : ''),
            'city' => (isset($aVals['city']) ? $oFilter->clean(strip_tags($aVals['city'])) : ''),
            'province' => (isset($aVals['province']) ? $oFilter->clean(strip_tags($aVals['province'])) : ''),
            'postal_code' => (isset($aVals['zip_code']) ? $aVals['zip_code'] : ''),
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'categories' => json_encode($aVals['category']),
            'business_type' =>(isset($aVals['business_type'])) ? $aVals['business_type']: '0',
            'established_year' => (isset($aVals['established_year']) && (int)$aVals['established_year'] > 0) ? $aVals['established_year'] : 0,
            'ship_payment_info' => (isset($aVals['ship_payment_info']) && !empty($aVals['ship_payment_info'])) ? $oFilter->clean($aVals['ship_payment_info']) : '',
            'return_policy' => (isset($aVals['return_policy']) && !empty($aVals['ship_payment_info'])) ? $oFilter->clean($aVals['return_policy']) : '',
            'buyer_protection' => (isset($aVals['buyer_protection']) && !empty($aVals['buyer_protection'])) ? $oFilter->clean($aVals['buyer_protection']) : '',
            'tax' => (isset($aVals['tax']) && (int)$aVals['tax'] > 0) ? $aVals['tax'] : 0,
        );

        // Process upload form
        $this->_processUploadForm($aVals, $aUpdate);

        $this->database()->update($this->_sTable, $aUpdate,'store_id ='.$iStoreId);

        // insert location 
        if (isset($aVals['location_fulladdress']) && count($aVals['location_fulladdress']) > 0) {
            $this->database()->delete(Phpfox::getT('ynstore_store_location'), 'store_id = ' . (int)$iStoreId);
            foreach ($aVals['location_fulladdress'] as $key => $val) {
                if (strlen(trim($aVals['location_address'][$key])) != '') {
                    if (strlen(trim($aVals['location_title'][$key])) == 0) {
                        $aVals['location_title'][$key] = $aVals['location_address'][$key];
                    }
                    $aInsertLocation = array(
                        'store_id' => $iStoreId,
                        'title' => $oFilter->clean(strip_tags($aVals['location_title'][$key])),
                        'address' => $aVals['location_address'][$key],
                        'longitude' => $aVals['location_address_lng'][$key],
                        'latitude' => $aVals['location_address_lat'][$key],
                        'location' => $aVals['location'][$key],
                    );

                    $this->database()->insert(Phpfox::getT('ynstore_store_location'), $aInsertLocation);
                }
            }
        }
        $this->database()->delete(Phpfox::getT('ynstore_store_infomation'), 'store_id = ' . (int)$iStoreId);
        // update phone
        if (isset($aVals['phone']) && count($aVals['phone']) > 0) {
            foreach ($aVals['phone'] as $key => $val) {
                if (strlen(trim($val)) > 0) {
                    $aInsertPhone = array(
                        'store_id' => $iStoreId,
                        'type' => 'phone',
                        'info' => $oFilter->clean(strip_tags($val)),
                    );
                    $this->database()->insert(Phpfox::getT('ynstore_store_infomation'), $aInsertPhone);
                }
            }
        }
        // update fax
        if (isset($aVals['fax']) && count($aVals['fax']) > 0) {
            foreach ($aVals['fax'] as $key => $val) {
                if (strlen(trim($val)) > 0) {
                    $aInsertFax = array(
                        'store_id' => $iStoreId,
                        'type' => 'fax',
                        'info' => $oFilter->clean(strip_tags($val)),
                    );
                    $this->database()->insert(Phpfox::getT('ynstore_store_infomation'), $aInsertFax);
                }
            }
        }
        // update web
        if (isset($aVals['web_address']) && count($aVals['web_address']) > 0) {
            foreach ($aVals['web_address'] as $key => $val) {
                if (strlen(trim($val)) > 0) {
                    $aInsertWeb = array(
                        'store_id' => $iStoreId,
                        'type' => 'website',
                        'info' => $oFilter->clean(strip_tags($val)),
                    );
                    $this->database()->insert(Phpfox::getT('ynstore_store_infomation'), $aInsertWeb);
                }
            }
        }
        // update addition information 
        if (isset($aVals['addinfo_title']) && count($aVals['addinfo_title']) > 0) {
            foreach ($aVals['addinfo_title'] as $key => $val) {
                if (strlen(trim($aVals['addinfo_title'][$key])) > 0) {

                    $aInsertInfo = array(
                        'store_id' => $iStoreId,
                        'title' => substr($oFilter->clean(strip_tags($aVals['addinfo_title'][$key])),0,149),
                        'info' => substr($oFilter->clean($aVals['addinfo_content'][$key]),0,149),
                        'type' => 'addinfo'
                    );

                    $this->database()->insert(Phpfox::getT('ynstore_store_infomation'), $aInsertInfo);
                }
            }
        }
        // update category
        if (isset($aVals['category']) && count($aVals['category']))
        {
            $aStore = Phpfox::getService('ynsocialstore')->getQuickStoreById($iStoreId);
            $this->database()->delete(Phpfox::getT('ecommerce_category_data'), 'product_id = ' . (int) $iStoreId.' AND product_type ="store"');
            if(!empty($aStore['categories']))
            {
                $aCategories = json_decode($aStore['categories']);
                foreach ($aCategories as $key => $iCategory) {
                    $this->database()->updateCounter('ecommerce_category','used','category_id',$iCategory,true);
                }            
            }

            foreach ($aVals['category'] as $key => $iCategoryId)
            {
                $data = array('product_id' => $iStoreId, 'category_id' => $iCategoryId,'product_type' => 'store');
                $data['is_main'] = 1;
                $this->database()->insert(Phpfox::getT('ecommerce_category_data'), $data);
                $this->database()->updateCounter('ecommerce_category','used','category_id',$iCategoryId);
            }
        }
        //update privacy
        if (Phpfox::isModule('privacy'))
        {
            if ($aVals['privacy'] == '4')
            {
                Phpfox::getService('privacy.process')->update('ynsocialstore_store', $iStoreId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
            }
            else
            {
                Phpfox::getService('privacy.process')->delete('ynsocialstore_store', $iStoreId);
            }
        }

        return $iStoreId;
    }

    public function updatePositionCoverStorePhoto($sPosition, $iStoreId)
    {
        $this->database()->update(Phpfox::getT('ynstore_store'), array('position_top' => $sPosition), 'store_id = ' . (int) $iStoreId);
        return true;
    }
    public function addInvoice($iId, $sCurrency, $sCost, $sType = 'store', $data = array(), $sItemType = 'auction')
    {
        $iInvoiceId = $this->database()->insert(Phpfox::getT('ecommerce_invoice'), array(
                'item_id' => $iId,
                'type' => $sType,
                'item_type' => $sItemType,
                'user_id' => Phpfox::getUserId(),
                'currency_id' => $sCurrency,
                'price' => $sCost,
                'time_stamp' => PHPFOX_TIME,
                'invoice_data' => json_encode($data),
                'pay_type' => trim($data['pay_type'], '|'),
            )

        );

        return $iInvoiceId;
    }
    public function updateStoreStatus($iStoreId, $iStatus)
    {
        $this->database()->update(Phpfox::getT('ynstore_store')
            , array('status' => $iStatus), 'store_id = ' . $iStoreId);
    }
    public function approveStoreByPackage($iStoreId, $isAdmin = false, $aItem = null)
    {
        if ($aItem === null) {
            $aItem = Phpfox::getService('ynsocialstore')->getStoreById($iStoreId, true);
        }
        $aPackage = Phpfox::getService('ynsocialstore.package')->getById($aItem['package_id']);
        // create feed
        if (!Phpfox::getService('ynsocialstore.helper')->isHavingFeed('ynsocialstore_store', $iStoreId)) {
            if (isset($aItem['module_id']) && $aItem['module_id'] != 'ynsocialstore' && Phpfox::isModule($aItem['module_id']) && Phpfox::hasCallback($aItem['module_id'], 'getFeedDetails'))
            {
                $iPageId = $aItem['item_id'];
                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback(Phpfox::callback($aItem['module_id'] . '.getFeedDetails', $aItem['item_id']))->add('ynsocialstore_store', $iStoreId, $aItem['privacy'], (isset($aItem['privacy_comment']) ? (int) $aItem['privacy_comment'] : 0), $aItem['item_id']) : null);
            }
            else{
                if(!Phpfox::isUser() && !defined('PHPFOX_FEED_NO_CHECK')) {
                    define('PHPFOX_FEED_NO_CHECK', true);
                }
                ((Phpfox::isModule('feed') && !defined('PHPFOX_SKIP_FEED_ENTRY')) ? $iFeedId = Phpfox::getService('feed.process')->add('ynsocialstore_store', $iStoreId, $aItem['privacy'], 0, 0, $aItem['user_id']) : null);
            }
            if(!$isAdmin) {
                // plugin call
                (($sPlugin = Phpfox_Plugin::get('ynsocialstore.service_process_approveStore__end')) ? eval($sPlugin) : false);
            }
            // Update user activity
            Phpfox::getService('user.activity')->update($aItem['user_id'], 'ynsocialstore_store');
        }
        //Noti owner
        Phpfox::getService("notification.process")->add("ynsocialstore_approvestore",$iStoreId, $aItem['user_id'], Phpfox::getUserId());

        //Send mail
        if($aItem['user_id'] != Phpfox::getUserId()) {
            $sSiteName = Phpfox::getParam('core.site_title');
            $sLink = Phpfox_Url::instance()->permalink('ynsocialstore.store', $aItem['store_id'], $aItem['name']);
            $sSubject = _p('your_store_in_site_name_site_is_approved', ['site_name' => $sSiteName]);
            $sText = _p('your_store_store_name_is_approved_by_sender_for_more_detail_please_view_this_link_link', ['store_name' => $aItem['name'], 'sender' => Phpfox::getService('ynsocialstore')->getUserFullName(Phpfox::getUserId()), 'link' => $sLink]);
            $this->sendMail($aItem['user_id'], $sText, $sSubject);
        }
        // update package start/end time
        $start_time = PHPFOX_TIME;
        if ($aPackage['expire_number'] == 0) {
           //never expire
            $end_time = 4294967295;
        }
        else{
            $end_time = $start_time + $aPackage['expire_number'] * 86400; //24*3600
        }
        $this->updateStorePackageTime($aItem['store_id'], $start_time, $end_time);

        //update feature time
        $end_feature_time = $start_time + $aItem['feature_day'] * 86400; //30*7*24*3600

        $this->updateStoreFeatureTime($aItem['store_id'], $end_feature_time, $aItem['feature_day']);
        $this->database()->update($this->_sTable,['is_reminded' => 0],'store_id ='. $aItem['store_id']);
        //TODO send notification to owner
        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.service_process_approvestore_end')) ? eval($sPlugin) : false);
    }
    public function updateStorePackageTime($iStoreId, $iStartTime, $iEndTime)
    {
        $this->database()->update($this->_sTable
            , array(
                'start_time' => (int)$iStartTime,
                'expire_time' => (int)$iEndTime,

            )
            , 'store_id = ' . $iStoreId
        );
    }
    public function updateStoreFeatureTime($iStoreId, $iEndTime, $feature_days = 0)
    {

        $this->database()->update($this->_sTable
            , array(
                'feature_end_time' => (int)$iEndTime,
                'feature_day' => (int)$feature_days,
                'is_featured' => ($iEndTime > PHPFOX_TIME) ? 1 : 0,
            ),
            'store_id = ' . $iStoreId);
    }
    public function prePareDataForMap(&$aStores)
    {
        $data = array();
        if (count($aStores)) {
            foreach ($aStores as $key => $aStore) {
                if ($aStore['latitude'] != '' && $aStore['longitude'] != '') {
                    
                    $keyLatLog = implode(",", array($aStore['latitude'], $aStore['longitude']));
                    $aSto = array();
                    $aSto['title'] = $aStore['name'];
                    $aSto['location'] = $aStore['title'];
                    $aSto['location_address'] = $aStore['address'];
                    $aSto['rating'] = $aStore['rating'];
                    $aSto['reviews'] = $aStore['total_review'];
                    $aSto['latitude'] = $aStore['latitude'];
                    $aSto['longitude'] = $aStore['longitude'];
                    $aSto['url_image'] = $aStore['url_image'];
                    $aSto['url_detail'] = Phpfox::getLib('url')->permalink('ynsocialstore.store', $aStore['store_id'], $aStore['name']);
                    $data[$keyLatLog][] = $aSto;
                }
            }
        }
        return $data;
    }

    public function setCoverPhoto($iStoreId, $iPhotoId)
    {
        // check that this photo belongs to this page
        $aCheck = $this->database()->select('photo_id, destination')->from(Phpfox::getT('photo'))->where('module_id = "ynsocialstore" AND group_id = ' . (int)$iStoreId . ' AND photo_id = ' . (int)$iPhotoId)->execute('getSlaveRow');

        $new_destination = Phpfox::getParam('core.dir_pic') . 'ynsocialstore/' . sprintf($aCheck['destination'], '_1024');
        $new_destination_480 = Phpfox::getParam('core.dir_pic') . 'ynsocialstore/' . sprintf($aCheck['destination'],'_480');

        if (!Phpfox_File::instance()->copy(Phpfox::getParam('photo.dir_photo') . sprintf($aCheck['destination'],
                '_1024'), $new_destination)) {
            return false;
        }

        if (file_exists($new_destination)) {
            // create suffix 480
            Phpfox_Image::instance()->createThumbnail($new_destination, $new_destination_480, 480, 1024);
            $this->database()->update(Phpfox::getT('ynstore_store'),
                array('position_top' => 0, 'cover_path' => $aCheck['destination']), 'store_id = ' . (int)$iStoreId);
            return true;
        }

        return Phpfox_Error::set(_p('the_photo_does_not_belong_to_this_store'));
    }

    public function updatePackageForStore($package_id, $iStoreId)
    {

        $aPackageStore = Phpfox::getService('ynsocialstore.package')->getById($package_id);

        $this->database()->update($this->_sTable
            , array('package_id' => $aPackageStore['package_id'] ),'store_id = ' . (int)$iStoreId);

        return true;
    }

    public function deleteMultiple($aDeleteIds)
    {
        foreach ($aDeleteIds as $iDeleteId) {
            if (is_numeric($iDeleteId)) {
                $this->delete($iDeleteId);
            }
        }

    }

    public function saveFAQForStore($answer, $question, $iStoreId, $iFaqId = '',$isHide)
    {

        $oFilter = Phpfox::getLib('parse.input');

        if ($iFaqId == '') {

            $data_id = $this->database()->insert(Phpfox::getT('ynstore_store_faq'), array(
                'store_id' => $iStoreId,
                'is_active' => empty($isHide) ? 1 : 0,
                'question' => $oFilter->clean($question),
                'answer' => $oFilter->clean($answer),
                'time_stamp' => PHPFOX_TIME,
            ));

        } else {
            $this->database()->update(Phpfox::getT('ynstore_store_faq'), array(
                'is_active' => empty($isHide) ? 1 : 0,
                'question' => $oFilter->clean($question),
                'answer' => $oFilter->clean($answer),
            ),
                                      'faq_id = ' . (int)$iFaqId
            );
        }

        return true;
    }
    public function deleteFaq($iFaqId)
    {
        $this->database()->delete(Phpfox::getT('ynstore_store_faq'), 'faq_id = ' . (int)$iFaqId);

    }
    public function sendMail($iUserId,$sText,$sSubject)
    {
        $aOwnerEmail =  array(Phpfox::getService('ynsocialstore')->getOwnerEmail($iUserId));
        return Phpfox::getLib('mail')->to($aOwnerEmail)
                        ->subject($sSubject)
                        ->message($sText)
                        ->send();
    }

    public function updateTotalView($iStoreId)
    {
        $this->database()->updateCounter('ynstore_store', 'total_view', 'store_id', $iStoreId);
    }

    public function updateReNewNotificationBefore($iStoreId, $sNumberDays)
    {
        $this->database()->update(Phpfox::getT('ynstore_store'), array(
            'renew_before' => $sNumberDays,
        ),
            'store_id = ' . (int)$iStoreId
        );
    }

    public function updateTotalOrder($iStoreId)
    {
        $this->database()->updateCounter('ynstore_store', 'total_orders', 'store_id', $iStoreId);
    }

    private function _processUploadForm($aVals, &$aInsert)
    {
        if (!empty($aVals['logo_path']) && (!empty($aVals['temp_file_logo']) || !empty($aVals['remove_logo']))) {
            if ($this->_deleteImage($aVals['logo_path'], 'ynsocialstore_store_logo', $aVals['server_id'])) {
                $aInsert['logo_path'] = null;
                $aInsert['server_id'] = 0;
            }
        }

        if (!empty($aVals['cover_path']) && (!empty($aVals['temp_file_cover']) || !empty($aVals['remove_cover']))) {
            if ($this->_deleteImage($aVals['cover_path'], 'ynsocialstore_store_cover', $aVals['cover_server_id'])) {
                $aInsert['cover_path'] = null;
                $aInsert['cover_server_id'] = 0;
            }
        }

        if (!empty($aVals['temp_file_logo'])) {
            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file_logo']);
            if (!empty($aFile)) {
                $aInsert['logo_path'] = $aFile['path'];
                $aInsert['server_id'] = $aFile['server_id'];
                Phpfox::getService('core.temp-file')->delete($aVals['temp_file_logo']);
            }
        }

        if (!empty($aVals['temp_file_cover'])) {
            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file_cover']);
            if (!empty($aFile)) {
                $aInsert['cover_path'] = $aFile['path'];
                $aInsert['cover_server_id'] = $aFile['server_id'];
                Phpfox::getService('core.temp-file')->delete($aVals['temp_file_cover']);
            }
        }
    }

    private function _deleteImage($sName, $sType, $iServerId = 0)
    {
        $aParams = Phpfox::callback($sType . '.getUploadParams');
        $aParams['type'] = $sType;
        $aParams['path'] = $sName;
        $aParams['server_id'] = $iServerId;

        return Phpfox::getService('user.file')->remove($aParams);
    }

    public function upload($ItemId, $fieldImage, $type, $sFile = 'image',$isCover = false)
    {

        if (!in_array($type, array('store', 'product'))) {
            return false;
        }

        $oFile = Phpfox_File::instance();
        $oImage = Phpfox_Image::instance();
        $sPicStorage = Phpfox::getParam('core.dir_pic') . 'ynsocialstore/';

        $sStoreImage = $this->database()->select($fieldImage)
            ->from(Phpfox::getT('ynstore_store'))
            ->where('store_id = ' . (int) $ItemId)
            ->execute('getSlaveField');

        if (!empty($sStoreImage)) {
            if (file_exists($sPicStorage . sprintf($sStoreImage, '_480_square')) && $fieldImage =='logo_path') {
                $oFile->unlink($sPicStorage . sprintf($sStoreImage,'_480_square'));
            }
            if (file_exists($sPicStorage . sprintf($sStoreImage, '_140')) && $fieldImage =='logo_path') {
                $oFile->unlink($sPicStorage . sprintf($sStoreImage,'_140'));
            }
            if (file_exists($sPicStorage . sprintf($sStoreImage, '_90')) && $fieldImage =='logo_path') {
                $oFile->unlink($sPicStorage . sprintf($sStoreImage,'_90'));
            }
            if (file_exists($sPicStorage . sprintf($sStoreImage,'_1024')) && $fieldImage =='cover_path') {
                $oFile->unlink($sPicStorage . sprintf($sStoreImage,'_1024'));
            }
            if (file_exists($sPicStorage . sprintf($sStoreImage,'_480')) && $fieldImage =='cover_path') {
                $oFile->unlink($sPicStorage . sprintf($sStoreImage,'_480'));
            }
        }

        if (!is_dir($sPicStorage)) {
            @mkdir($sPicStorage, 0777, 1);
            @chmod($sPicStorage, 0777);
        }

        $sFileName = $oFile->upload($sFile, $sPicStorage, $fieldImage.rand());


        if($fieldImage =='logo_path')
        {
            $oImage->createThumbnail($sPicStorage . sprintf($sFileName, ''), $sPicStorage . sprintf($sFileName, '_480_square'), 480, 480, false);
            $oImage->createThumbnail($sPicStorage . sprintf($sFileName, ''), $sPicStorage . sprintf($sFileName, '_480'), 480, 480, false);
            $oImage->createThumbnail($sPicStorage . sprintf($sFileName, ''), $sPicStorage . sprintf($sFileName, '_90'), 90, 90, false);
            $oImage->createThumbnail($sPicStorage . sprintf($sFileName, ''), $sPicStorage . sprintf($sFileName, '_140'), 140, 140, false);
        }
        else
        {
            $oImage->createThumbnail($sPicStorage . sprintf($sFileName, ''), $sPicStorage . sprintf($sFileName, '_480'), 480, 1024);
            $oImage->createThumbnail($sPicStorage . sprintf($sFileName, ''), $sPicStorage . sprintf($sFileName, '_1024'), 1024, 1024);
        }
        @unlink($sPicStorage . sprintf($sFileName, ''));
        if($isCover)
        {
            $this->database()->update(Phpfox::getT('ynstore_store'), array(
                'store_id' => $ItemId,
                $fieldImage => $sFileName,
                'cover_server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
            ), 'store_id = '.$ItemId);
        }
        else{
            $this->database()->update(Phpfox::getT('ynstore_store'), array(
                'store_id' => $ItemId,
                $fieldImage => $sFileName,
                'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
            ), 'store_id = '.$ItemId);
        }
    }
}