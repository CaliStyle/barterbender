<?php

defined('PHPFOX') or exit('NO DICE!');


class Directory_Service_Process extends Phpfox_Service
{
    private $_aBusinessCategories = array();

    public function __construct()
    {

    }

    public function addDirectoryFeed($aVals)
    {
        $id = $this->database()->insert(Phpfox::getT('directory_feed'), array(
            'privacy' => $aVals['privacy'],
            'privacy_comment' => $aVals['privacy_comment'],
            'type_id' => $aVals['type_id'],
            'user_id' => $aVals['user_id'],
            'parent_user_id' => $aVals['parent_user_id'],
            'item_id' => $aVals['item_id'],
            'time_stamp' => PHPFOX_TIME,
            'parent_feed_id' => $aVals['parent_feed_id'],
            'parent_module_id' => $aVals['parent_module_id'],
            'time_update' => PHPFOX_TIME,
        ));

        return $id;
    }

    public function addCheckinhere($iBusinessId, $iUserId)
    {
        $id = $this->database()->insert(Phpfox::getT('directory_checkinhere'), array(
            'business_id' => $iBusinessId,
            'user_id' => $iUserId,
            'timestamp' => PHPFOX_TIME,
        ));

        return $id;
    }

    public function updateOwner($iBusinessId, $iUserId)
    {

        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);

        $this->database()->update(Phpfox::getT('directory_business'), array('user_id' => $iUserId), "business_id = {$iBusinessId}");

        // get business data
        $iOldOwnerId = $aBusiness['user_id'];
        $iNewOwnerId = $iUserId;
        // get id of group admin and member

        $aRoleAdmin = Phpfox::getService('directory')->getRoleIdByBusinessId($iBusinessId, 'admin');
        $aRoleMember = Phpfox::getService('directory')->getRoleIdByBusinessId($iBusinessId, 'member');
        //update old owner
        $this->updateUserMemberRole($iBusinessId, $iNewOwnerId, $aRoleAdmin['role_id']);
        //update new owner
        $this->updateUserMemberRole($iBusinessId, $iOldOwnerId, $aRoleMember['role_id']);

        // old owner id --> group member
        // new owner id --> group

        return TRUE;
    }

    public function denyBusiness($iBusinessId)
    {
        $this->database()->update(Phpfox::getT('directory_business'), array('business_status' => Phpfox::getService('directory.helper')->getConst('business.status.denied')), "business_id = {$iBusinessId}");
        return TRUE;
    }

    public function approveClaimRequest($iBusinessId)
    {
        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
        if (isset($aBusiness['business_id']) == false) {
            return false;
        }

        $this->database()->update(Phpfox::getT('directory_business')
            , array(
                'business_status' => Phpfox::getService('directory.helper')->getConst('business.status.draft'),
            ),
            'business_id = ' . (int)$iBusinessId);

        // add default data
        // with claiming business, system will add when claiming request is approved 
        $this->addDefaultRole($iBusinessId);
        $this->addDefaultMouldes($iBusinessId, $aBusiness);
        $this->addDefaultInfoContactUs($iBusinessId);
        $this->addPermissionForAdmin($iBusinessId, $aBusiness);

        $this->database()->update(Phpfox::getT('directory_business'), array('time_approved' => PHPFOX_TIME), 'business_id = ' . (int)$iBusinessId);

        // send notification to owner 
        Phpfox::getService('notification.process')->add('directory_approve_claimrequest', $iBusinessId, $aBusiness['user_id'], Phpfox::getUserId());

        return true;
    }

    public function denyClaimRequest($iBusinessId)
    {
        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
        if (isset($aBusiness['business_id']) == false) {
            return false;
        }

        $this->database()->update(Phpfox::getT('directory_business')
            , array(
                'type' => 'claiming',
                'business_status' => Phpfox::getService('directory.helper')->getConst('business.status.draft'),
                'timestamp_claimrequest' => 0,
                'user_id' => $aBusiness['creator_id'],
            ),
            'business_id = ' . (int)$iBusinessId);

        return true;
    }

    public function updateTypeOfBusiness($iBusinessId, $type = 'business')
    {
        $this->database()->update(Phpfox::getT('directory_business')
            , array(
                'type' => $type,
                'business_status' => Phpfox::getService('directory.helper')->getConst('business.status.pendingclaiming'),
                'timestamp_claimrequest' => PHPFOX_TIME,
                'user_id' => Phpfox::getUserId(),
            ),
            'business_id = ' . (int)$iBusinessId);
    }

    public function updateStatusItemOfModuleInBusiness($iItemId, $sModuleId, $sCoreModuleid, $sStatus = 'inactive')
    {
        $this->database()->update(Phpfox::getT('directory_business_moduledata')
            , array(
                'status' => $sStatus,
            ),
            'item_id = ' . (int)$iItemId . ' AND module_id = ' . $sModuleId . ' AND core_module_id = \'' . $sCoreModuleid . '\'');
    }

    public function addItemOfModuleToBusiness($aVals = array())
    {
        $id = $this->database()->insert(Phpfox::getT('directory_business_moduledata'), array(
            'module_id' => $aVals['module_id'],
            'business_id' => $aVals['business_id'],
            'core_module_id' => $aVals['core_module_id'],
            'item_id' => $aVals['item_id'],
            'status' => $aVals['status'],
        ));

        return $id;
    }

    public function addDefaultMouldes($iBusinessId, $aBusiness = null)
    {
        if (null == $aBusiness) {
            $aBusiness = Phpfox::getService('directory')->getBusinessForEdit($iBusinessId, true);
        }
        if (isset($aBusiness['business_id']) == false) {
            return false;
        }

        list($list, $listIgnore) = Phpfox::getService('directory')->getDefaultModulesInBusiness();

        // filter by package
        if (isset($aBusiness['package_data'])) {
            $package_data = (array)json_decode($aBusiness['package_data']);
            foreach ($list as $keyDefault => $default) {
                $remove = true;
                if(isset($package_data['modules'])) {
                    $modules = (array)$package_data['modules'];
                    foreach ($modules as $keyModule => $module) {
                        if ($module->module_id == $keyDefault) {
                            $remove = false;
                        }
                    }
                }
                if (in_array($keyDefault, $listIgnore) == false && $remove) {
                    unset($list[$keyDefault]);
                }
            }
        }

        // add modules to business 
        foreach ($list as $keyDefault => $default) {
            $module_landing = 0;
            switch ($keyDefault) {
                case '1':
                    $module_phrase = "{phrase var='overview'}";
                    $module_type = 'page';
                    $module_landing = 1;
                    break;
                case '2':
                    $module_phrase = "{phrase var='about_us'}";
                    $module_type = 'page';
                    break;
                case '3':
                    $module_phrase = "{phrase var='activities'}";
                    $module_type = 'page';
                    break;
                case '4':
                    $module_phrase = "{phrase var='members_up'}";
                    $module_type = 'page';
                    break;
                case '5':
                    $module_phrase = "{phrase var='followers_up'}";
                    $module_type = 'page';
                    break;
                case '6':
                    $module_phrase = "{phrase var='reviews'}";
                    $module_type = 'page';
                    break;
                case '7':
                    $module_phrase = "{phrase var='photos'}";
                    $module_type = 'module';
                    break;
                case '8':
                    $module_phrase = "{phrase var='videos'}";
                    $module_type = 'module';
                    break;
                case '9':
                    $module_phrase = "{phrase var='musics'}";
                    $module_type = 'module';
                    break;
                case '10':
                    $module_phrase = "{phrase var='blogs'}";
                    $module_type = 'module';
                    break;
                case '11':
                    $module_phrase = "{phrase var='discussion'}";
                    $module_type = 'module';
                    break;
                case '12':
                    $module_phrase = "{phrase var='polls'}";
                    $module_type = 'module';
                    break;
                case '13':
                    $module_phrase = "{phrase var='coupons'}";
                    $module_type = 'module';
                    break;
                case '14':
                    $module_phrase = "{phrase var='events'}";
                    $module_type = 'module';
                    break;
                case '15':
                    $module_phrase = "{phrase var='jobs'}";
                    $module_type = 'module';
                    break;
                case '16':
                    $module_phrase = "{phrase var='marketplace'}";
                    $module_type = 'module';
                    break;
                case '17':
                    $module_phrase = "{phrase var='faq'}";
                    $module_type = 'page';
                    break;
                case '18':
                    $module_phrase = "{phrase var='contact_us'}";
                    $module_type = 'page';
                    break;
                case '19':
                    $module_phrase = "{phrase var='ultimate_videos'}";
                    $module_type = 'module';
                    break;
                case '20':
                    $module_phrase = "{phrase var='ynblog'}";
                    $module_type = 'module';
                    break;
                case '21':
                    $module_phrase = "{phrase var='videos'}";
                    $module_type = 'module';
                    break;
            }
            $this->database()->insert(Phpfox::getT('directory_business_module'), array(
                'business_id' => $iBusinessId,
                'module_id' => $keyDefault,
                'contentpage' => '',
                'contentpage_parsed' => '',
                'module_phrase' => $module_phrase,
                'module_name' => $default,
                'module_type' => $module_type,
                'module_description' => '',
                'is_show' => 1,
                'module_landing' => $module_landing,
            ));
        }
    }

    public function addDefaultRole($iBusinessId)
    {

        $role_id_admin = $this->database()->insert(Phpfox::getT('directory_business_memberrole'), array(
                'business_id' => $iBusinessId,
                'role_title' => 'Admin',
                'is_default' => 1,
                'type' => 'admin',
            )
        );
        $role_id_member = $this->database()->insert(Phpfox::getT('directory_business_memberrole'), array(
                'business_id' => $iBusinessId,
                'role_title' => 'Member',
                'is_default' => 1,
                'type' => 'member',
            )
        );

        $role_id_guest = $this->database()->insert(Phpfox::getT('directory_business_memberrole'), array(
                'business_id' => $iBusinessId,
                'role_title' => 'Guest',
                'is_default' => 1,
                'type' => 'guest',
            )
        );

        $sql = '';
        $sql .= "
            INSERT INTO `" . Phpfox::getT('directory_business_memberrolesettingdata') . "`(`setting_id`, `role_id`, `status`) VALUES
        ";
        if ((int)$role_id_admin > 0) {
            $setting = Phpfox::getService('directory')->getDefaultBusinessRoleMemberSetting();
            foreach ($setting as $key => $value) {
                $setting[$key] = 'yes';
            }
            foreach ($setting as $key => $value) {
                // $this->addMemberRoleSettingData($role_id_admin, $key, $value);
                $sql .= "({$key}, {$role_id_admin}, '{$value}'),";
            }
        }
        if ((int)$role_id_member > 0) {
            $setting = Phpfox::getService('directory')->getDefaultBusinessRoleMemberSetting();
            foreach ($setting as $key => $value) {
                // $this->addMemberRoleSettingData($role_id_member, $key, $value);
                $sql .= "({$key}, {$role_id_member}, '{$value}'),";
            }
        }
        if ((int)$role_id_guest > 0) {
            $setting = Phpfox::getService('directory')->getDefaultBusinessRoleMemberSetting(true);
            foreach ($setting as $key => $value) {
                // $this->addMemberRoleSettingData($role_id_guest, $key, $value);
                $sql .= "({$key}, {$role_id_guest}, '{$value}'),";
            }
        }

        $sql = trim($sql, ',');
        $sql .= ';';
        $this->database()->query($sql);
    }

    public function addMemberRoleSettingData($role_id, $setting_id, $status)
    {
        return $this->database()->insert(Phpfox::getT('directory_business_memberrolesettingdata'), array(
                'setting_id' => $setting_id,
                'role_id' => $role_id,
                'status' => $status,
            )
        );
    }

    public function updateTotalView($iBusinessId)
    {
        $this->database()->updateCounter('directory_business', 'total_view', 'business_id', $iBusinessId);
    }

    public function approveBusiness($iBusinessId, $aItem = null)
    {
        if ($aItem === null) {
            $aItem = Phpfox::getService('directory')->getBusinessForEdit($iBusinessId, true);
        }

        // create feed
        $aCallback = ((!empty($aItem['module_id']) && $aItem['module_id'] != 'directory') ? Phpfox::getService('directory')->getBusinessAddCallback($aItem['item_id']) : null);
        if (Phpfox::getService('directory.helper')->isHavingFeed('directory', $iBusinessId) == false) {
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback($aCallback)->allowGuest()->add('directory', $aItem['business_id'], $aItem['privacy'], (isset($aItem['privacy_comment']) ? (int)$aItem['privacy_comment'] : 0), (isset($aItem['item_id']) ? (int)$aItem['item_id'] : 0), $aItem['user_id']) : null);

            // Update user activity
            Phpfox::getService('user.activity')->update($aItem['user_id'], 'directory');
        }

        // update package start/end time
        $start_time = PHPFOX_TIME;
        switch ($aItem['package_expire_type']) {
            case 0: //never expire
                $end_time = 4294967295;  // http://dev.mysql.com/doc/refman/5.0/en/integer-types.html
                break;
            case 1: //day
                $end_time = $start_time + $aItem['package_expire_number'] * 86400; //24*3600
                break;
            case 2: //week
                $end_time = $start_time + $aItem['package_expire_number'] * 604800; //7*24*3600
                break;
            case 3: //month
                $end_time = $start_time + $aItem['package_expire_number'] * 2592000; //30*7*24*3600
                break;
        }
        $this->updateBusinessPackageTime($aItem['business_id'], $start_time, $end_time);

        //update feature time
        $end_feature_time = $start_time + $aItem['feature_day'] * 86400; //30*7*24*3600

        $this->updateBusinessFeatureTime($aItem['business_id'], $start_time, $end_feature_time, $aItem['feature_day']);
        // update counter category 

        $this->database()->update(Phpfox::getT('directory_business'), array('time_approved' => PHPFOX_TIME), 'business_id = ' . (int)$iBusinessId);

        $catIds = Phpfox::getService('directory.category')->getCategoryIds($aItem['business_id']);
        $catIds = explode(',', $catIds);
        foreach ($catIds as $key => $value) {
            if ((int)$value > 0) {
                $this->database()->updateCounter('directory_category', 'used', 'category_id', $value);
            }
        }
        $this->cache()->remove('directory_category', 'substr');

        (($sPlugin = Phpfox_Plugin::get('directory.service_process_approvebusiness_end')) ? eval($sPlugin) : false);
    }

    public function updateBusinessFeatureTime($iBusinessId, $iStartTime, $iEndTime, $feature_days = 0)
    {

        $this->database()->update(Phpfox::getT('directory_business')
            , array(
                'feature_start_time' => (int)$iStartTime,
                'feature_end_time' => (int)$iEndTime,
                'feature_day' => (int)$feature_days
            ),
            'business_id = ' . $iBusinessId);
    }

    public function featureBusinessBackEnd($iBusinessId, $feature)
    {
        if ($feature) {
            $this->database()->update(Phpfox::getT('directory_business')
                , array(
                    'feature_start_time' => PHPFOX_TIME,
                    'feature_end_time' => 4294967295
                ),
                'business_id = ' . $iBusinessId);

        } else {
            $this->database()->update(Phpfox::getT('directory_business')
                , array(
                    'feature_start_time' => 0,
                    'feature_end_time' => 0,
                    'feature_day' => 0
                ),
                'business_id = ' . $iBusinessId);
        }

    }

    public function updateBusinessPackageTime($iBusinessId, $iStartTime, $iEndTime)
    {
        $this->database()->update(Phpfox::getT('directory_business')
            , array(
                'package_start_time' => (int)$iStartTime,
                'package_end_time' => (int)$iEndTime,
                'is_send_renewal' => 0,
            )
            , 'business_id = ' . $iBusinessId
        );
    }

    public function updateBusinessStatus($iBusinessId, $iStatus)
    {
        $this->database()->update(Phpfox::getT('directory_business')
            , array('business_status' => (int)$iStatus), 'business_id = ' . $iBusinessId);
    }

    public function addInvoice($iId, $sCurrency, $sCost, $sType = 'business', $data = array())
    {
        $iInvoiceId = $this->database()->insert(Phpfox::getT('directory_invoice'), array(
                'item_id' => $iId,
                'type' => $sType,
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

    public function getCategoriesFromForm($aVals)
    {
        if (isset($aVals['category']) && count($aVals['category'])) {
            if (empty($aVals['category'][0])) {
                return false;
            } else if (!is_array($aVals['category'])) {
                $this->_aBusinessCategories[] = $aVals['category'];
            } else {
                foreach ($aVals['category'] as $aCategory) {

                    foreach ($aCategory as $iCategory) {
                        if (empty($iCategory)) {
                            continue;
                        }

                        if (!is_numeric($iCategory)) {
                            continue;
                        }

                        $this->_aBusinessCategories[] = $iCategory;
                    }
                }
            }
            return true;
        }
    }

    public function addBusiness($aVals)
    {
        $oFilter = Phpfox::getLib('parse.input');
        $aCurrentCurrencies = Phpfox::getService('directory.helper')->getCurrentCurrencies();
        $aGlobalSetting = Phpfox::getService('directory')->getGlobalSetting();

        // check if the user entered a forbidden word
        Phpfox::getService('ban')->checkAutomaticBan($aVals['name']);

        if (!$this->getCategoriesFromForm($aVals)) {
            return Phpfox_Error::set(_p('provide_a_category_this_item_will_belong_to'));
        }

        if (isset($aVals['customfield_user_title']) && count($aVals['customfield_user_title']) > 0) {
            foreach ($aVals['customfield_user_title'] as $key => $val) {
                if (strlen(trim($val)) > 0 && strlen($aVals['customfield_user_content'][$key]) > 255) {
                    return Phpfox_Error::set(_p('Content of {{ customfield_user_title }} is too long', ['customfield_user_title' => $aVals['customfield_user_title'][$key]]));
                }
            }
        }

        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }
        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }

        $sName = $oFilter->clean(strip_tags($aVals['name']), 255);
        $bHasAttachments = false;
        $aInsert = array(
            'creator_id' => Phpfox::getUserId(),
            'user_id' => Phpfox::getUserId(),
            'creating_type' => empty($aVals['type']) ? 'business' : $aVals['type'],
            'type' => empty($aVals['type']) ? 'business' : $aVals['type'],
            'theme_id' => empty($aVals['theme']) ? 1 : $aVals['theme'],
            'name' => $sName,
            'time_stamp' => PHPFOX_TIME,
            'module_id' => (isset($aVals['module_id']) ? $aVals['module_id'] : 'directory'),
            'item_id' => (isset($aVals['item_id']) ? $aVals['item_id'] : '0'),
            'business_status' => ($aVals['type'] == 'claiming' && isset($aVals['draft'])) ? Phpfox::getService('directory.helper')->getConst('business.status.claimingdraft') : Phpfox::getService('directory.helper')->getConst('business.status.draft'), //will check status later
            'short_description' => (isset($aVals['short_description']) ? $oFilter->clean($aVals['short_description']) : ''),
            'short_description_parsed' => (isset($aVals['short_description']) ? $oFilter->prepare($aVals['short_description']) : ''),
            'country_iso' => (isset($aVals['country_iso']) ? $aVals['country_iso'] : ''),
            'country_child_id' => (isset($aVals['country_child_id']) ? $aVals['country_child_id'] : 0),
            'email' => (isset($aVals['email']) ? $aVals['email'] : ''),
            'city' => (isset($aVals['city']) ? $this->cleanTextWithStripTag($aVals['city']) : ''),
            'province' => (isset($aVals['province']) ? $this->cleanTextWithStripTag($aVals['province']) : ''),
            'postal_code' => (isset($aVals['zip_code']) ? $aVals['zip_code'] : ''),
            'size' => (isset($aVals['size']) ? $aVals['size'] : ''),
            'time_zone' => (isset($aVals['time_zone']) ? $aVals['time_zone'] : ''),
            'founder' => (isset($aVals['founder']) ? $this->cleanTextWithStripTag($aVals['founder']) : ''),
            'feature_day' => 0,
            'feature_fee' => (int)$aGlobalSetting[0]['default_feature_fee'],
            'feature_start_time' => 0, //will check later
            'feature_end_time' => 0, //will check later
            'disable_visitinghourtimezone' => (isset($aVals['disable_visitinghourtimezone']) ? 1 : 0),
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'timestamp_claimrequest' => 0,
        );

        $aInsert['package_id'] = 0;
        if (isset($aVals['package_id']) && (int)$aVals['package_id'] > 0) {
            $aPackage = Phpfox::getService('directory.package')->getById((int)$aVals['package_id']);
            if (isset($aPackage['package_id']) == false || (int)$aPackage['active'] != 1) {
                Phpfox::getLib('url')->send($this->url()->makeUrl('directory.businesstype'));
            } else {
                $aInsert = array_merge($aInsert, array(
                    'package_id' => $aPackage['package_id'],
                    'package_name' => $aPackage['name'],
                    'package_expire_number' => $aPackage['expire_number'],
                    'package_expire_type' => $aPackage['expire_type'],
                    'package_fee' => $aPackage['fee'],
                    'package_currency' => $aCurrentCurrencies[0]['currency_id'],
                    'package_max_cover_photo' => $aPackage['max_cover_photo'],
                    'package_start_time' => 0, //will check later
                    'package_end_time' => 0, //will check later
                    'package_data' => json_encode($aPackage)
                ));
            }
        }
        else{
            $aInsert = array_merge($aInsert, array(
                'package_name' => '',
                'package_expire_number' => 0,
                'package_expire_type' => 0,
                'package_fee' => 0,
                'package_currency' => $aCurrentCurrencies[0]['currency_id'],
                'package_max_cover_photo' => 0,
                'package_start_time' => 0,
                'package_end_time' => 0,
                'package_data' => ''
            ));
        }
        // Process upload
        $this->_processUploadForm($aVals, $aInsert);

        // Perform insert
        $iBusinessId = $this->database()->insert(Phpfox::getT('directory_business'), $aInsert);

        // inser text 
        $sDescription = $oFilter->clean($aVals['description']);
        $sDescription_parse = $oFilter->prepare($aVals['description']);
        $aInsertText = array(
            'business_id' => $iBusinessId,
            'description' => $sDescription,
            'description_parsed' => $sDescription_parse,
        );

        $this->database()->insert(Phpfox::getT('directory_business_text'), $aInsertText);

        // If we uploaded any attachments make sure we update the 'item_id'
        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iBusinessId);
        }

        // insert location 
        if (isset($aVals['location_fulladdress']) && count($aVals['location_fulladdress']) > 0) {
            foreach ($aVals['location_fulladdress'] as $key => $val) {
                if (strlen(trim($aVals['location_title'][$key])) == 0) {
                    $aVals['location_title'][$key] = $aVals['location_address'][$key];
                }
                $aInsertLocation = array(
                    'business_id' => $iBusinessId,
                    'location_title' => $this->cleanTextWithStripTag($aVals['location_title'][$key]),
                    'location_address' => $aVals['location_address'][$key],
                    'location_longitude' => $aVals['location_address_lng'][$key],
                    'location_latitude' => $aVals['location_address_lat'][$key],
                );

                $this->database()->insert(Phpfox::getT('directory_business_location'), $aInsertLocation);
            }
        }

        // insert phone
        if (isset($aVals['phone']) && count($aVals['phone']) > 0) {
            foreach ($aVals['phone'] as $key => $val) {
                if (strlen(trim($val)) > 0) {
                    $aInsertPhone = array(
                        'business_id' => $iBusinessId,
                        'phone_number' => $this->cleanTextWithStripTag($val),
                    );

                    $this->database()->insert(Phpfox::getT('directory_business_phone'), $aInsertPhone);
                }
            }
        }

        // insert fax
        if (isset($aVals['fax']) && count($aVals['fax']) > 0) {
            foreach ($aVals['fax'] as $key => $val) {
                if (strlen(trim($val)) > 0) {
                    $aInsertFax = array(
                        'business_id' => $iBusinessId,
                        'fax_number' => $this->cleanTextWithStripTag($val),
                    );

                    $this->database()->insert(Phpfox::getT('directory_business_fax'), $aInsertFax);
                }
            }
        }

        // insert website url
        if (isset($aVals['web_address']) && count($aVals['web_address']) > 0) {
            foreach ($aVals['web_address'] as $key => $val) {
                if (strlen(trim($val)) > 0) {
                    $aInsertWebsite = array(
                        'business_id' => $iBusinessId,
                        'website_text' => $this->cleanTextWithStripTag($val),
                    );

                    $this->database()->insert(Phpfox::getT('directory_business_website'), $aInsertWebsite);
                }
            }
        }

        // insert customfield_user
        if (isset($aVals['customfield_user_title']) && count($aVals['customfield_user_title']) > 0) {
            foreach ($aVals['customfield_user_title'] as $key => $val) {
                if (strlen(trim($val)) > 0) {
                    $aInsertUserCustomField = array(
                        'business_id' => $iBusinessId,
                        'usercustomfield_title' => $this->cleanTextWithStripTag($aVals['customfield_user_title'][$key]),
                        'usercustomfield_content' => $this->cleanTextWithStripTag($aVals['customfield_user_content'][$key]),
                    );

                    $this->database()->insert(Phpfox::getT('directory_business_usercustomfield'), $aInsertUserCustomField);
                }
            }
        }


        // insert category
        $this->addCategoriesForBusiness($iBusinessId, $aVals['maincategory']);
        // insert custom field by category 
        if (isset($aVals['custom']) && count($aVals['custom']) > 0) {
            Phpfox::getService('directory.custom.process')->addValue($aVals['custom'], $iBusinessId);
        }

        // insert tag 
        if (Phpfox::isModule('tag') && isset($aVals['tag_list']) && ((is_array($aVals['tag_list']) && count($aVals['tag_list'])) || (!empty($aVals['tag_list'])))) {
            Phpfox::getService('tag.process')->add('business', $iBusinessId, Phpfox::getUserId(), $aVals['tag_list']);
        }

        // insert visting hour
        if (isset($aVals['visiting_hours_dayofweek_id']) && count($aVals['visiting_hours_dayofweek_id']) > 0) {
            foreach ($aVals['visiting_hours_dayofweek_id'] as $key => $val) {
                $aInsertVisitingHour = array(
                    'business_id' => $iBusinessId,
                    'vistinghour_dayofweek' => $aVals['visiting_hours_dayofweek_id'][$key],
                    'vistinghour_starttime' => $aVals['visiting_hours_hour_starttime'][$key],
                    'vistinghour_endtime' => $aVals['visiting_hours_hour_endtime'][$key],
                );

                $this->database()->insert(Phpfox::getT('directory_business_vistinghour'), $aInsertVisitingHour);
            }
        }

        // add default data
        // with claiming business, system will add when claiming request is approved 
        if ($aVals['type'] == 'business') {
            $this->addDefaultRole($iBusinessId);
            $aInsert['business_id'] = $iBusinessId;
            $this->addDefaultMouldes($iBusinessId, $aInsert);
            $this->addDefaultInfoContactUs($iBusinessId);
            $this->addPermissionForAdmin($iBusinessId, $aInsert);
        }

        // send email to owner 
        if ($aInsert['type'] == 'business') {
            $aUser = Phpfox::getService('user')->getUser(Phpfox::getUserId());
            $language_id = $aUser['language_id'] == null ? 'en' : $aUser['language_id'];
            $email = $aUser['email'];
            $aEmail = Phpfox::getService('directory.mail')->getEmailMessageFromTemplate(4, $language_id, $iBusinessId, Phpfox::getUserId());
            Phpfox::getService('directory.mail.send')->send($aEmail['subject'], $aEmail['message'], $email);
        }

        return $iBusinessId;
    }

    public function updateBusiness($iBusinessId, $aVals)
    {

        $oFilter = Phpfox::getLib('parse.input');

        // check if the user entered a forbidden word
        Phpfox::getService('ban')->checkAutomaticBan($aVals['name']);

        if (!$this->getCategoriesFromForm($aVals)) {
            return Phpfox_Error::set(_p('provide_a_category_this_item_will_belong_to'));
        }

        if (isset($aVals['customfield_user_title']) && count($aVals['customfield_user_title']) > 0) {
            foreach ($aVals['customfield_user_title'] as $key => $val) {
                if (strlen(trim($val)) > 0 && strlen($aVals['customfield_user_content'][$key]) > 255) {
                    return Phpfox_Error::set(_p('Content of {{ customfield_user_title }} is too long', ['customfield_user_title' => $aVals['customfield_user_title'][$key]]));
                }
            }
        }

        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }
        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }

        $sName = $oFilter->clean(strip_tags($aVals['name']), 255);
        $bHasAttachments = false;
        $aEditedBusiness = Phpfox::getService('directory')->getBusinessForEdit($iBusinessId);

        $aUpdate = array(
            'type' => empty($aVals['type']) ? 'business' : $aVals['type'],
            'name' => $sName,
            'time_update' => PHPFOX_TIME,
            'short_description' => (isset($aVals['short_description']) ? $oFilter->clean($aVals['short_description']) : ''),
            'short_description_parsed' => (isset($aVals['short_description']) ? $oFilter->prepare($aVals['short_description']) : ''),
            'country_iso' => (isset($aVals['country_iso']) ? $aVals['country_iso'] : ''),
            'country_child_id' => (isset($aVals['country_child_id']) ? $aVals['country_child_id'] : 0),
            'email' => (isset($aVals['email']) ? $aVals['email'] : ''),
            'city' => (isset($aVals['city']) ? $this->cleanTextWithStripTag($aVals['city']) : ''),
            'province' => (isset($aVals['province']) ? $this->cleanTextWithStripTag($aVals['province']) : ''),
            'postal_code' => (isset($aVals['zip_code']) ? $aVals['zip_code'] : ''),
            'size' => (isset($aVals['size']) ? $aVals['size'] : ''),
            'time_zone' => (isset($aVals['time_zone']) ? $aVals['time_zone'] : ''),
            'dst_check' => (isset($aVals['disable_visitinghourtimezone']) ? 1 : 0),
            'founder' => (isset($aVals['founder']) ? $this->cleanTextWithStripTag($aVals['founder']) : ''),

            'disable_visitinghourtimezone' => (isset($aVals['disable_visitinghourtimezone']) ? 1 : 0),

            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
        );
        if(isset($aVals['publish_claim_draft'])){
            $aUpdate['business_status'] = Phpfox::getService('directory.helper')->getConst('business.status.draft');
        }
        if(isset($aVals['isClaimingDraft']) && $aVals['isClaimingDraft'] == 1){
            $aUpdate['type'] = 'claiming';
        }

        // Process upload
        $this->_processUploadForm($aVals, $aUpdate);

        // Perform update
        $this->database()->update(Phpfox::getT('directory_business'), $aUpdate, 'business_id = ' . $iBusinessId);

        // inser text 
        $sDescription = $oFilter->clean($aVals['description']);
        $sDescription_parse = $oFilter->prepare($aVals['description']);

        $aUpdateText = array(
            'description' => $sDescription,
            'description_parsed' => $sDescription_parse,
        );
        $this->database()->update(Phpfox::getT('directory_business_text'), $aUpdateText, 'business_id = ' . $iBusinessId);

        // If we uploaded any attachments make sure we update the 'item_id'
        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iBusinessId);
        }


        // update location 
        if (isset($aVals['location_fulladdress']) && count($aVals['location_fulladdress']) > 0) {

            $this->database()->delete(Phpfox::getT('directory_business_location'), 'business_id = ' . (int)$iBusinessId);

            foreach ($aVals['location_fulladdress'] as $key => $val) {
                if (strlen(trim($aVals['location_title'][$key])) == 0) {
                    $aVals['location_title'][$key] = $aVals['location_address'][$key];
                }
                $aInsertLocation = array(
                    'business_id' => $iBusinessId,
                    'location_title' => $this->cleanTextWithStripTag($aVals['location_title'][$key]),
                    'location_address' => $aVals['location_address'][$key],
                    'location_longitude' => $aVals['location_address_lng'][$key],
                    'location_latitude' => $aVals['location_address_lat'][$key],
                );

                $this->database()->insert(Phpfox::getT('directory_business_location'), $aInsertLocation);
            }
        }

        // insert phone
        if (isset($aVals['phone']) && count($aVals['phone']) > 0) {

            $this->database()->delete(Phpfox::getT('directory_business_phone'), 'business_id = ' . (int)$iBusinessId);

            foreach ($aVals['phone'] as $key => $val) {
                if (strlen(trim($val)) > 0) {
                    $aInsertPhone = array(
                        'business_id' => $iBusinessId,
                        'phone_number' => $this->cleanTextWithStripTag($val),
                    );

                    $this->database()->insert(Phpfox::getT('directory_business_phone'), $aInsertPhone);
                }
            }
        }

        // insert fax
        if (isset($aVals['fax']) && count($aVals['fax']) > 0) {

            $this->database()->delete(Phpfox::getT('directory_business_fax'), 'business_id = ' . (int)$iBusinessId);

            foreach ($aVals['fax'] as $key => $val) {
                if (strlen(trim($val)) > 0) {
                    $aInsertFax = array(
                        'business_id' => $iBusinessId,
                        'fax_number' => $this->cleanTextWithStripTag($val),
                    );

                    $this->database()->insert(Phpfox::getT('directory_business_fax'), $aInsertFax);
                }
            }
        }

        // insert website url
        if (isset($aVals['web_address']) && count($aVals['web_address']) > 0) {

            $this->database()->delete(Phpfox::getT('directory_business_website'), 'business_id = ' . (int)$iBusinessId);


            foreach ($aVals['web_address'] as $key => $val) {
                if (strlen(trim($val)) > 0) {
                    $aInsertWebsite = array(
                        'business_id' => $iBusinessId,
                        'website_text' => $this->cleanTextWithStripTag($val),
                    );

                    $this->database()->insert(Phpfox::getT('directory_business_website'), $aInsertWebsite);
                }
            }
        }


        // insert customfield_user
        if (isset($aVals['customfield_user_title']) && count($aVals['customfield_user_title']) > 0) {

            $this->database()->delete(Phpfox::getT('directory_business_usercustomfield'), 'business_id = ' . (int)$iBusinessId);

            foreach ($aVals['customfield_user_title'] as $key => $val) {
                if (strlen(trim($val)) > 0) {
                    $aInsertUserCustomField = array(
                        'business_id' => $iBusinessId,
                        'usercustomfield_title' => $this->cleanTextWithStripTag($aVals['customfield_user_title'][$key]),
                        'usercustomfield_content' => $this->cleanTextWithStripTag($aVals['customfield_user_content'][$key]),
                    );

                    $this->database()->insert(Phpfox::getT('directory_business_usercustomfield'), $aInsertUserCustomField);
                }
            }
        }

        // update category
        $this->addCategoriesForBusiness($iBusinessId, $aVals['maincategory']);

        //update count business for each category
        $sCategoryTextRelated = Phpfox::getService('directory.category')->getCategoryIds($iBusinessId);
        if (
            $aEditedBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.approved') ||
            $aEditedBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.running') ||
            $aEditedBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.expired')
        ) {
            $aCategoryInfo = Phpfox::getService('directory')->updateCountBusinessForCategory($sCategoryTextRelated);
        }

        // insert custom field by category 
        if (isset($aVals['custom']) && count($aVals['custom']) > 0) {

            $this->database()->delete(Phpfox::getT('directory_custom_value'), 'business_id = ' . (int)$iBusinessId);

            Phpfox::getService('directory.custom.process')->updateValue($aVals['custom'], $iBusinessId);
        }


        // insert tag 
        if (Phpfox::isModule('tag') && isset($aVals['tag_list']) && ((is_array($aVals['tag_list']) && count($aVals['tag_list'])) || (!empty($aVals['tag_list'])))) {
            Phpfox::getService('tag.process')->update('business', $iBusinessId, Phpfox::getUserId(), $aVals['tag_list']);
        }

        // insert visting hour 
        if (isset($aVals['visiting_hours_dayofweek_id']) && count($aVals['visiting_hours_dayofweek_id']) > 0) {

            $this->database()->delete(Phpfox::getT('directory_business_vistinghour'), 'business_id = ' . (int)$iBusinessId);

            foreach ($aVals['visiting_hours_dayofweek_id'] as $key => $val) {
                $aInsertVisitingHour = array(
                    'business_id' => $iBusinessId,
                    'vistinghour_dayofweek' => $aVals['visiting_hours_dayofweek_id'][$key],
                    'vistinghour_starttime' => $aVals['visiting_hours_hour_starttime'][$key],
                    'vistinghour_endtime' => $aVals['visiting_hours_hour_endtime'][$key],
                );

                $this->database()->insert(Phpfox::getT('directory_business_vistinghour'), $aInsertVisitingHour);
            }
        }

        // send notification to follower(s)
        $aFollowers = Phpfox::getService('directory')->getFollowerIds((int)$iBusinessId);
        foreach ($aFollowers as $keyaFollowers => $valueaFollowers) {
            Phpfox::getService('notification.process')->add('directory_updateinfobusiness', $iBusinessId, $valueaFollowers['user_id'], Phpfox::getUserId());
        }

        return $iBusinessId;
    }

    //add categories for business 
    public function addCategoriesForBusiness($iBusinessId, $iIndexMainCategory = 0)
    {
        if (isset($this->_aBusinessCategories) && count($this->_aBusinessCategories)) {
            $sCategoryTextRelated = Phpfox::getService('directory.category')->getCategoryIds($iBusinessId);

            $this->database()->delete(Phpfox::getT('directory_category_data'), 'business_id = ' . (int)$iBusinessId);

            $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);

            /*update category after delete*/
            if (
                $aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.approved') ||
                $aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.running') ||
                $aBusiness['business_status'] == Phpfox::getService('directory.helper')->getConst('business.status.expired')
            ) {
                Phpfox::getService('directory')->updateCountBusinessForCategory($sCategoryTextRelated);
            }

            foreach ($this->_aBusinessCategories as $key => $iCategoryId) {
                $data = array('business_id' => $iBusinessId, 'category_id' => $iCategoryId);
                if ($key == $iIndexMainCategory) {
                    $data['is_main'] = 1;
                }
                $this->database()->insert(Phpfox::getT('directory_category_data'), $data);
            }

            // Delete main category cache for this item
            $sCacheId = $this->cache()->set('directory_business_main_category');
            $aCache = $this->cache()->get($sCacheId);
            unset($aCache[$iBusinessId]);
            $this->cache()->save($sCacheId, $aCache);
        }
    }

    public function deleteGlobalSetting()
    {
        $this->database()->delete(Phpfox::getT('directory_global_setting'), '1=1');
    }

    public function addGlobalSetting($default_theme_id, $default_feature_fee)
    {
        $id = $this->database()->insert(Phpfox::getT("directory_global_setting"),
            array(
                "default_theme_id" => (int)$default_theme_id,
                "default_feature_fee" => (int)$default_feature_fee
            ));

        return $id;
    }

    public function activateFieldComparison($aVals)
    {

        $checkedField = $aVals['comparison_field'];

        $this->database()->update(Phpfox::getT('directory_comparison'), array('is_active' => 0), 'comparison_id <> 0');

        if (count($checkedField) > 0) {
            $this->database()->update(Phpfox::getT('directory_comparison'), array('is_active' => 1), 'comparison_id IN (' . implode(",", $checkedField) . ')');
        }

        return true;

    }

    public function addBusinessCreator($aUsers)
    {

        foreach ($aUsers as $iUserId) {
            $this->database()->insert(Phpfox::getT("directory_creator"),
                array(
                    "creator_id" => $iUserId,
                    "user_id" => $iUserId
                ));
        }
        return true;

    }

    public function deleteBusinessCreator($iUserId)
    {

        $this->database()->delete(Phpfox::getT('directory_creator'), 'user_id = ' . (int)$iUserId);

        return true;

    }

    public function saveLastingSearch($data)
    {
        if (count($data)) {
            $aSearch = $this->database()->select('dbsh.user_id')
                ->from(Phpfox::getT('directory_business_searchhistory'), 'dbsh')
                ->where('dbsh.user_id = ' . (int)Phpfox::getUserId())
                ->execute('getSlaveRow');

            if (count($aSearch)) {
                $this->database()->update(Phpfox::getT('directory_business_searchhistory'), array('data' => json_encode($data)), 'user_id = ' . Phpfox::getUserId());
            } else {

                $aSearch = array(
                    'user_id' => Phpfox::getUserId(),
                    'data' => json_encode($data),
                );

                $this->database()->insert(Phpfox::getT('directory_business_searchhistory'), $aSearch);
            }

        }


    }

    public function subscribeBusiness($aData)
    {

        if (count($aData) && $aData['email'] != '') {

            $aSearch = $this->database()->select('dbs.email')
                ->from(Phpfox::getT('directory_business_subscribe'), 'dbs')
                ->where('dbs.email = \'' . $aData['email'] . '\'')
                ->execute('getSlaveRow');

            if (count($aSearch)) {
                $this->database()->update(Phpfox::getT('directory_business_subscribe'), array('data' => json_encode($aData)), 'email = \'' . $aData['email'] . '\'');
            } else {

                $aSearch = array(
                    'email' => $aData['email'],
                    'data' => json_encode($aData),
                );

                $this->database()->insert(Phpfox::getT('directory_business_subscribe'), $aSearch);
            }
            return true;
        }
        return false;
    }

    public function addFavorite($iItemId = 0)
    {
        if ($iItemId) {
            $iCount = $this->database()->select('COUNT(*)')
                ->from(phpfox::getT('directory_favorite'))
                ->where("business_id = {$iItemId} AND user_id = " . Phpfox::getUserId())
                ->execute('getSlaveField');

            if ($iCount) {
                return false;
            }

            $iId = $this->database()->insert(phpfox::getT('directory_favorite'), array(
                    'business_id' => (int)$iItemId,
                    'user_id' => Phpfox::getUserId(),
                    'time_stamp' => PHPFOX_TIME,
                )
            );

            (($sPlugin = Phpfox_Plugin::get('directory.service_process_addfavorite_end')) ? eval($sPlugin) : false);
            return $iId;
        }
        return false;
    }


    public function addFollow($iItemId)
    {
        $iId = $this->database()->insert(phpfox::getT('directory_follow'), array(
                'business_id' => $iItemId,
                'user_id' => Phpfox::getUserId(),
                'time_stamp' => PHPFOX_TIME,
            )
        );
        return $iId;
    }

    public function deleteFavorite($iId)
    {
        $aItem = $this->database()->select('l.*')
            ->from(Phpfox::getT('directory_favorite'), 'l')
            ->where('l.favorite_id = ' . (int)$iId)
            ->execute('getSlaveRow');
        if (isset($aItem['business_id'])) {
            $this->database()->delete(phpfox::getT('directory_favorite'), "favorite_id = {$iId} AND user_id = " . Phpfox::getUserId());
            (($sPlugin = Phpfox_Plugin::get('directory.service_process_deletefavorite_end')) ? eval($sPlugin) : false);
        }
    }

    public function deleteFollow($iId)
    {
        $this->database()->delete(phpfox::getT('directory_follow'), "follow_id = {$iId} AND user_id = " . Phpfox::getUserId());
    }

    public function delete($iBusinessId)
    {
        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
        Phpfox::getService('user.activity')->update($aBusiness['user_id'], 'directory', '-');


        /*update count business for category have deleted business*/
        $sCategoryTextRelated = Phpfox::getService('directory.category')->getCategoryIds($iBusinessId);
        $this->database()->delete(phpfox::getT('directory_category_data'), "business_id = " . (int)$iBusinessId);
        $aCategoryInfo = Phpfox::getService('directory')->updateCountBusinessForCategory($sCategoryTextRelated);

        $this->database()->delete(phpfox::getT('directory_custom_value'), "business_id = " . (int)$iBusinessId);
        $this->database()->delete(phpfox::getT('directory_package_data'), "business_id = " . (int)$iBusinessId);
        $this->database()->delete(phpfox::getT('directory_claim'), "business_id = " . (int)$iBusinessId);
        $this->database()->delete(phpfox::getT('directory_email_queue'), "business_id = " . (int)$iBusinessId);
        $this->database()->delete(phpfox::getT('directory_invite'), "business_id = " . (int)$iBusinessId);
        $this->database()->delete(phpfox::getT('directory_follow'), "business_id = " . (int)$iBusinessId);
        $this->database()->delete(phpfox::getT('directory_favorite'), "business_id = " . (int)$iBusinessId);
        $this->database()->delete(phpfox::getT('directory_business_text'), "business_id = " . (int)$iBusinessId);
        $this->database()->delete(phpfox::getT('directory_business_location'), "business_id = " . (int)$iBusinessId);
        $this->database()->delete(phpfox::getT('directory_business_phone'), "business_id = " . (int)$iBusinessId);
        $this->database()->delete(phpfox::getT('directory_business_fax'), "business_id = " . (int)$iBusinessId);
        $this->database()->delete(phpfox::getT('directory_business_website'), "business_id = " . (int)$iBusinessId);
        $this->database()->delete(phpfox::getT('directory_business_vistinghour'), "business_id = " . (int)$iBusinessId);
        $this->database()->delete(phpfox::getT('directory_business_usercustomfield'), "business_id = " . (int)$iBusinessId);
        $this->database()->delete(phpfox::getT('directory_business_announcement'), "business_id = " . (int)$iBusinessId);
        $this->database()->delete(phpfox::getT('directory_business_module'), "business_id = " . (int)$iBusinessId);
        $this->database()->delete(phpfox::getT('directory_business_contactus'), "business_id = " . (int)$iBusinessId);
        $this->database()->delete(phpfox::getT('directory_business_faq'), "business_id = " . (int)$iBusinessId);
        $this->database()->delete(phpfox::getT('directory_business_memberrole'), "business_id = " . (int)$iBusinessId);
        $this->database()->delete(phpfox::getT('directory_business_moduledata'), "business_id = " . (int)$iBusinessId);
        $this->database()->delete(phpfox::getT('directory_review'), "business_id = " . (int)$iBusinessId);
        $this->database()->delete(phpfox::getT('directory_image'), "business_id = " . (int)$iBusinessId);
        //$this->database()->update(Phpfox::getT('directory_business'), array('business_status' => Phpfox::getService('directory.helper')->getConst('business.status.deleted')),"business_id = {$iBusinessId}");
        $this->database()->delete(phpfox::getT('directory_business'), "business_id =  " . (int)$iBusinessId);

        $this->database()->delete(Phpfox::getT('tag'), "category_id = 'business' AND item_id = " . (int)$iBusinessId);
        if (Phpfox::isModule('tag')) {
            $this->database()->delete(Phpfox::getT('tag'), 'item_id = ' . (int)$iBusinessId . ' AND category_id = "business"', 1);
            $this->cache()->remove('tag', 'substr');
        }

        return TRUE;
    }

    public function prePareDataForMap(&$aBusinesses)
    {

        $data = array();
        if (count($aBusinesses)) {
            $aBusinessesCompares = $aBusinesses;
            foreach ($aBusinesses as $key => $aBusiness) {
                if ($aBusiness['location_latitude'] != '' && $aBusiness['location_longitude'] != '') {
                    $keyLatLog = implode(",", array($aBusiness['location_latitude'], $aBusiness['location_longitude']));
                    $aBus = array();
                    $aBus['title'] = $aBusiness['name'];
                    $aBus['location'] = $aBusiness['location_title'];
                    $aBus['location_address'] = $aBusiness['location_address'];
                    $aBus['rating'] = $aBusiness['total_score'] / 2;
                    $aBus['reviews'] = $aBusiness['total_reviews'];
                    $aBus['featured'] = $aBusiness['featured'];
                    $aBus['latitude'] = $aBusiness['location_latitude'];
                    $aBus['longitude'] = $aBusiness['location_longitude'];
                    $aBus['url_image'] = $aBusiness['url_image'];
                    $aBus['url_detail'] = Phpfox::getLib('url')->permalink('directory.detail', $aBusiness['business_id'], $aBusiness['name']);

                    $data[$keyLatLog][] = $aBus;

                    /*check duplicate*/
                    foreach ($aBusinessesCompares as $keycp => $aBusinessesCompare) {
                        if ($key != $keycp) {
                            if ($aBusiness['location_latitude'] == $aBusinessesCompare['location_latitude'] &&
                                $aBusiness['location_longitude'] == $aBusinessesCompare['location_longitude']
                            ) {

                                $aBusSame = array();
                                $aBusSame['title'] = $aBusinessesCompare['name'];
                                $aBusSame['location'] = $aBusinessesCompare['location_title'];
                                $aBusSame['location_address'] = $aBusinessesCompare['location_address'];
                                $aBusSame['rating'] = $aBusinessesCompare['total_rating'];
                                $aBusSame['reviews'] = $aBusinessesCompare['total_reviews'];
                                $aBusSame['featured'] = $aBusinessesCompare['featured'];
                                $aBusSame['latitude'] = $aBusinessesCompare['location_latitude'];
                                $aBusSame['longitude'] = $aBusinessesCompare['location_longitude'];
                                $aBusSame['url_image'] = $aBusinessesCompare['url_image'];
                                $aBusSame['url_detail'] = Phpfox::getLib('url')->permalink('directory.detail', $aBusinessesCompare['business_id'], $aBusinessesCompare['name']);
                                $data[$keyLatLog][] = $aBusSame;

                                unset($aBusinesses[$keycp]);
                                unset($aBusinessesCompares[$keycp]);
                            }
                        }
                    }

                }
            }
        }
        return $data;
    }

    public function updateCoverPhotos($aVals)
    {
        $iBusinessId = $aVals['businessid'];
        $aFiles = $this->processImages($iBusinessId);

        if (isset($aVals['photo-order'])) {
            foreach ($aVals['photo-order'] as $iBus => $iOrder) {
                $this->database()->update(Phpfox::getT('directory_image')
                    , array(
                        'ordering' => (int)$iOrder,
                    ),
                    'image_id = ' . $iBus);
            }
        }

        if (is_array($aFiles)) {
            if ($aFiles['error']) {
                return $aFiles;
            }
            //Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'directory', $aFiles['file_size']);
            $aSql['image_path'] = $aFiles['image_path'];
            $aSql['server_id'] = $aFiles['server_id'];
        }
    }

    public function processImages($iId)
    {
        $aSize = array(100, 120, 200, 400);
        $aType = array('jpg', 'gif', 'png');

        $oImage = Phpfox::getLib('image');
        $oFile = Phpfox::getLib('file');
        $iFileSizes = 0;
        $sDirImage = Phpfox::getParam('core.dir_pic') . 'yndirectory/';

        $aResult = array();
        foreach ($_FILES['image']['error'] as $iKey => $sError) {

            if ($sError == UPLOAD_ERR_OK) {

                if ($aImage = $oFile->load('image[' . $iKey . ']', $aType, (Phpfox::getParam('directory.max_upload_size_photos') === 0 ? null : (Phpfox::getParam('directory.max_upload_size_photos') / 1024)))) {
                    $sFileName = Phpfox::getLib('file')->upload('image[' . $iKey . ']', $sDirImage, $iId);


                    $iFileSize = filesize($sDirImage . sprintf($sFileName, ''));
                    $iFileSizes += $iFileSize;


                    list($width, $height, $type, $attr) = getimagesize($sDirImage . sprintf($sFileName, ''));

                    foreach ($aSize as $iSize) {
                        if ($iSize == 50 || $iSize == 120) {
                            if ($width < $iSize || $height < $iSize) {
                                $this->resizeImage($sFileName, $width > $iSize ? $iSize : $width, $height > $iSize ? $iSize : $height, '_' . $iSize);
                            } else {
                                $this->resizeImage($sFileName, $iSize, $iSize, '_' . $iSize);
                            }
                        } else {
                            $oImage->createThumbnail($sDirImage . sprintf($sFileName, ''), $sDirImage . sprintf($sFileName, '_' . $iSize), $iSize, $iSize);
                        }

                        $iFileSizes += filesize($sDirImage . sprintf($sFileName, '_' . $iSize));
                    }

                    $this->database()->insert(Phpfox::getT('directory_image'), array(
                        'business_id' => $iId,
                        'image_path' => $sFileName,
                        'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
                        'ordering' => 0,
                        'is_profile' => 0,
                        'file_size' => $iFileSize,
                        'extension' => pathinfo($sDirImage . sprintf($sFileName, ''), PATHINFO_EXTENSION),
                        'width' => $width,
                        'height' => $height,
                    ));

                } else {
                    $aResult = array('error' => 1, 'message' => _p('some_photos_you_uploaded_is_invalid_type_or_exceed_limited_size'));
                }
            }
        }


        if ($iFileSizes === 0) {

            return array('error' => 1, 'message' => _p('some_photos_you_uploaded_is_invalid_type_or_exceed_limited_size'));
        }


        if (!count($aResult)) {
            return array(
                'error' => 0,
                'file_size' => $iFileSizes,
                'image_path' => $sFileName,
                'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')
            );
        } else {
            return $aResult;
        }

    }


    /**
     * Resize Image
     * @todo improve performance
     */
    public function resizeImage($sFilePath, $iThumbWidth, $iThumbHeight, $sSubfix)
    {
        $sRealPath = Phpfox::getParam('core.dir_pic') . 'yndirectory' . PHPFOX_DS;

        #Resize to Width/Height
        list($iWidth, $iHeight, $sType, $sAttr) = getimagesize($sRealPath . sprintf($sFilePath, ''));
        $iNewWidth = $iWidth;
        $iNewHeight = $iHeight;
        $fSourceRatio = $iWidth / $iHeight;
        $fThumbRatio = $iThumbWidth / $iThumbHeight;
        if ($fSourceRatio > $fThumbRatio) {
            $iNewHeight = $iThumbHeight;
            $fRatio = $iNewHeight / $iHeight;
            $iNewWidth = $iWidth * $fRatio;
        } else {
            $iNewWidth = $iThumbWidth;
            $fRatio = $iNewWidth / $iWidth;
            $iNewHeight = $iHeight * $fRatio;
        }

        $sDestination = $sRealPath . sprintf($sFilePath, $sSubfix);
        $sTemp1 = $sRealPath . sprintf($sFilePath, $sSubfix . '_temp1');
        $sTemp2 = $sRealPath . sprintf($sFilePath, $sSubfix . '_temp2');
        $sTemp3 = $sRealPath . sprintf($sFilePath, $sSubfix . '_temp3');

        Phpfox::getLib("image")->createThumbnail($sRealPath . sprintf($sFilePath, ""), $sTemp1, $iNewWidth, $iNewHeight, true, false);

        #Crop the resized image
        if ($iNewWidth > $iThumbWidth) {
            $iX = ceil(($iNewWidth - $iThumbWidth) / 2);
            Phpfox::getLib("image")->cropImage($sTemp1, $sTemp2, $iThumbWidth, $iThumbHeight, $iX, 0, $iThumbWidth);
        } else {
            @copy($sTemp1, $sTemp2);
        }

        if ($iNewHeight > $iThumbHeight) {
            $iY = ceil(($iNewHeight - $iThumbHeight) / 2);
            Phpfox::getLib("image")->cropImage($sTemp2, $sTemp3, $iThumbWidth, $iThumbHeight, 0, $iY, $iThumbWidth);
        } else {
            @copy($sTemp2, $sTemp3);
        }

        @copy($sTemp3, $sDestination);
        if (Phpfox::getParam('core.allow_cdn')) {
            Phpfox::getLib('cdn')->put($sDestination);
        }

        @unlink($sTemp1);
        @unlink($sTemp2);
        @unlink($sTemp3);
    }

    public function deleteImage($iImageId)
    {
        $aSuffix = array('', '_100', '_120', '_200', '_400');

        $aImage = $this->database()->select('di.image_id, di.image_path, di.server_id')
            ->from(Phpfox::getT('directory_image'), 'di')
            ->where('di.image_id = ' . $iImageId)
            ->execute('getSlaveRow');

        if (!$aImage) {
            return Phpfox_Error::set(_p('unable_to_find_the_image'));
        }

        $iFileSizes = 0;
        foreach ($aSuffix as $sSize) {
            $sImage = Phpfox::getParam('core.dir_pic') . 'yndirectory/' . sprintf($aImage['image_path'], $sSize);
            if (file_exists($sImage)) {
                $iFileSizes += filesize($sImage);
                @unlink($sImage);
            }
        }

        return $this->database()->delete(Phpfox::getT('directory_image'), 'image_id = ' . $aImage['image_id']);
    }

    public function updateExtraInfoBusinessPage($aVals, $iBusinessId)
    {

        $aInfoShow = array_keys($aVals['page_show']);
        $aInfoShowSQL = implode(",", $aInfoShow);

        $iInfoHomeLanding = $aVals['page_landing'];

        //update show/hide page
        $this->database()->update(Phpfox::getT('directory_business_module'),
            array('is_show' => 0),
            'business_id = ' . $iBusinessId
        );

        if (count($aInfoShow)) {

            $this->database()->update(Phpfox::getT('directory_business_module'),
                array('is_show' => 1),
                "data_id IN (" . $aInfoShowSQL . ")"

            );

        }
        if (!in_array($iInfoHomeLanding, $aInfoShow)) {
            return array('invalid_landing', false);
        }
        $this->database()->update(Phpfox::getT('directory_business_module'),
            array('module_landing' => 0),
            "business_id =" . $iBusinessId
        );
        if ((int)$iInfoHomeLanding) {
            $this->database()->update(Phpfox::getT('directory_business_module'),
                array('module_landing' => 1),
                "data_id =" . $iInfoHomeLanding
            );
        }

        return array('', true);
        //update home-landing        
    }

    public function updateTitlePage($new_title, $data_id)
    {

        $oFilter = Phpfox::getLib('parse.input');
        $new_title = $oFilter->clean($new_title);
        $this->database()->update(
            Phpfox::getT('directory_business_module'),
            array('module_phrase' => $new_title),
            "data_id = " . $data_id
        );
    }

    public function addNewPage($aVals, $iBusinessId)
    {

        $oFilter = Phpfox::getLib('parse.input');

        $data_id = $this->database()->insert(Phpfox::getT('directory_business_module'), array(
            'business_id' => $iBusinessId,
            'module_id' => 0,
            'contentpage' => $oFilter->clean($aVals['contentpage']),
            'contentpage_parsed' => $oFilter->prepare($aVals["contentpage"]),
            'module_phrase' => $oFilter->clean($aVals['page_title']),
            'module_name' => '',
            'module_type' => 'contentpage',
            'module_description' => '',
            'is_show' => 1,
            'module_landing' => 0,
        ));

        return true;
    }

    public function editAboutUs($aVals, $iBusinessId)
    {

        $oFilter = Phpfox::getLib('parse.input');

        $this->database()->update(
            Phpfox::getT('directory_business_module'),
            array(
                'contentpage' => $oFilter->clean($aVals['contentpage']),
                'contentpage_parsed' => $oFilter->prepare($aVals["contentpage"]),
            ),
            " business_id = " . $iBusinessId . " AND module_name ='aboutus'"
        );
        return true;
    }

    public function editCustomPage($aVals, $iCustomPageId)
    {

        $oFilter = Phpfox::getLib('parse.input');

        $this->database()->update(
            Phpfox::getT('directory_business_module'),
            array(
                'module_phrase' => $oFilter->clean($aVals['page_title']),
                'contentpage' => $oFilter->clean($aVals['contentpage']),
                'contentpage_parsed' => $oFilter->prepare($aVals["contentpage"]),
            ),
            " data_id = " . $iCustomPageId
        );
        return true;
    }

    public function saveFAQForBusiness($answer, $question, $iBusinessId, $iFaqId = '')
    {

        $oFilter = Phpfox::getLib('parse.input');

        if ($iFaqId == '') {

            $data_id = $this->database()->insert(Phpfox::getT('directory_business_faq'), array(
                'business_id' => $iBusinessId,
                'parent_id' => 0,
                'is_active' => 1,
                'question' => $oFilter->clean($question),
                'question_parsed' => $oFilter->prepare($question),
                'answer' => $oFilter->clean($answer),
                'answer_parsed' => $oFilter->prepare($answer),
                'time_stamp' => PHPFOX_TIME,
                'used' => 0,
            ));

        } else {
            $this->database()->update(Phpfox::getT('directory_business_faq'), array(
                'question' => $oFilter->clean($question),
                'question_parsed' => $oFilter->prepare($question),
                'answer' => $oFilter->clean($answer),
                'answer_parsed' => $oFilter->prepare($answer),
            ),
                'faq_id = ' . (int)$iFaqId
            );
        }

        return true;
    }

    public function deleteFaq($iFaqId)
    {
        $this->database()->delete(Phpfox::getT('directory_business_faq'), 'faq_id = ' . (int)$iFaqId);

    }

    public function editContactUs($aVals, $iBusinessId)
    {

        $oFilter = Phpfox::getLib('parse.input');

        $aContactUs = $this->getContactUsByBusinessId($iBusinessId);

        $aDataReceiver = array();

        foreach ($aVals['email_receiver'] as $key => $aEmail) {
            if ($aEmail != '') {
                $aDataReceiver[$key]['email'] = $aEmail;
                $aDataReceiver[$key]['department'] = $aVals['department_receiver'][$key];
            }
        }

        if (count($aContactUs)) {

            $this->database()->update(
                Phpfox::getT('directory_business_contactus'),
                array(
                    'description' => $oFilter->clean($aVals['contact_description']),
                    'email_enable' => isset($aVals['email_enable']) ? 1 : 0,
                    'email_require' => isset($aVals['email_require']) ? 1 : 0,
                    'receiver_enable' => isset($aVals['receiver_enable']) ? 1 : 0,
                    'receiver_require' => isset($aVals['receiver_require']) ? 1 : 0,
                    'title_enable' => isset($aVals['title_enable']) ? 1 : 0,
                    'title_require' => isset($aVals['title_require']) ? 1 : 0,
                    'content_enable' => isset($aVals['content_enable']) ? 1 : 0,
                    'content_require' => isset($aVals['content_require']) ? 1 : 0,
                    'receiver_data' => json_encode($aDataReceiver)
                ),
                " business_id = " . (int)$iBusinessId
            );

        } else {
            $data_id = $this->database()->insert(
                Phpfox::getT('directory_business_contactus'), array(
                'business_id' => $iBusinessId,
                'description' => $oFilter->clean($aVals['contact_description']),
                'email_enable' => isset($aVals['email_enable']) ? 1 : 0,
                'email_require' => 1,
                'receiver_enable' => 1,
                'receiver_require' => 1,
                'title_enable' => 1,
                'title_require' => 1,
                'content_enable' => 1,
                'content_require' => 1,
                'receiver_data' => json_encode($aDataReceiver)
            ));
        }
        return true;
    }

    public function getContactUsByBusinessId($iBusinessId)
    {
        $aContactUs = $this->database()->select('dbc.*')
            ->from(Phpfox::getT('directory_business_contactus'), 'dbc')
            ->where('dbc.business_id = ' . $iBusinessId)
            ->execute('getSlaveRow');
        return $aContactUs;
    }

    public function addDefaultInfoContactUs($iBusinessId)
    {
        $data_id = $this->database()->insert(
            Phpfox::getT('directory_business_contactus'), array(
            'business_id' => $iBusinessId,
            'description' => '',
            'email_enable' => 1,
            'email_require' => 1,
            'receiver_enable' => 1,
            'receiver_require' => 1,
            'title_enable' => 1,
            'title_require' => 1,
            'content_enable' => 1,
            'content_require' => 1,
            'receiver_data' => ''
        ));
        return $data_id;
    }

    public function deleteCustomPage($iCustomPageId)
    {
        $this->database()->delete(Phpfox::getT('directory_business_module'), 'data_id = ' . (int)$iCustomPageId);
        return true;
    }

    public function addMemberRole($sRoleTitle, $iBusinessId, $iRoleId)
    {

        $oFilter = Phpfox::getLib('parse.input');
        $sRoleTitle = $oFilter->clean($sRoleTitle);

        if ((int)$iRoleId) {
            $this->database()->update(
                Phpfox::getT('directory_business_memberrole'),
                array('role_title' => $sRoleTitle),
                "role_id = " . $iRoleId
            );
        } else {
            $role_id = $this->database()->insert(
                Phpfox::getT('directory_business_memberrole'),
                array(
                    'role_title' => $sRoleTitle,
                    'business_id' => $iBusinessId,
                    'is_default' => 0,
                    'type' => 'member',
                ));


            if ((int)$role_id > 0) {
                $setting = Phpfox::getService('directory')->getDefaultBusinessRoleMemberSetting();
                foreach ($setting as $key => $value) {
                    Phpfox::getService('directory.process')->addMemberRoleSettingData($role_id, $key, $value);
                }
            }

        }
    }

    public function deleteMemberRole($iRoleId, $iBusinessId)
    {
        // update all users of this role to default role
        $roleDefaults = Phpfox::getService('directory')->getRoleByBusinessId($iBusinessId, 'member');

        $aRows = $this->database()->select('e.*')
            ->from(Phpfox::getT('directory_business_userroledata'), 'e')
            ->join(Phpfox::getT('directory_business_memberrole'), 'bmr', ' ( bmr.role_id = e.role_id AND bmr.business_id = ' . (int)$iBusinessId . ' ) ')
            ->where('e.role_id = ' . (int)$iRoleId)
            ->execute('getSlaveRows');

        foreach ($aRows as $aRow) {
            $this->database()->update(
                Phpfox::getT('directory_business_userroledata'),
                array('role_id' => $roleDefaults[0]['role_id']),
                "data_id = " . (int)$aRow['data_id']
            );
        }
        $this->database()->delete(Phpfox::getT('directory_business_memberrole'), 'role_id = ' . (int)$iRoleId);
        $this->database()->delete(Phpfox::getT('directory_business_memberrolesettingdata'), 'role_id = ' . (int)$iRoleId);
        return true;
    }

    public function updateMemberRoleSetting($aVals)
    {
        $role_id = $aVals['role_id'];

        foreach ($aVals[$role_id] as $keySetting => $value) {
            $value = $value ? "yes" : "no";
            $this->database()->update(
                Phpfox::getT('directory_business_memberrolesettingdata'),
                array('status' => $value),
                "role_id = " . (int)$role_id . " AND setting_id = " . (int)$keySetting
            );
        }

    }

    public function addNewAnnouncements($aVals)
    {

        $oFilter = Phpfox::getLib('parse.input');

        $data_id = $this->database()->insert(Phpfox::getT('directory_business_announcement'), array(
            'business_id' => $aVals['business_id'],
            'announcement_title' => $oFilter->clean($aVals['announcement_title']),
            'announcement_content' => $oFilter->clean($aVals['announcement_content']),
            'announcement_content_parse' => $oFilter->prepare($aVals["announcement_content"]),
            'timestamp' => PHPFOX_TIME,
        ));

        return $data_id;
    }

    public function editNewAnnouncements($aVals)
    {

        $oFilter = Phpfox::getLib('parse.input');

        /*print_r($aVals);
        die;*/
        $data_id = $this->database()->update(Phpfox::getT('directory_business_announcement'), array(
            'business_id' => $aVals['business_id'],
            'announcement_title' => $oFilter->clean($aVals['announcement_title']),
            'announcement_content' => $oFilter->clean($aVals['announcement_content']),
            'announcement_content_parse' => $oFilter->prepare($aVals["announcement_content"]),
        ),
            'announcement_id = ' . $aVals['idpost']
        );

        return $data_id;
    }

    public function deleteAnnouncement($iAnnouncementId)
    {
        $this->database()->delete(Phpfox::getT('directory_business_announcement'), 'announcement_id = ' . (int)$iAnnouncementId);
        return true;
    }

    public function updateThemeForBusiness($aVals)
    {
        $this->database()->update(Phpfox::getT('directory_business')
            , array(
                'theme_id' => $aVals['theme'],
            ),
            'business_id = ' . (int)$aVals['business_id']);

        return true;

    }

    public function updatePackageForBusiness($package_id, $iBusinessId)
    {

        // NO need to update setting support, because we do not have relationship business with setting support, just update new package data in business
        // NEED to update module support
        //      . remove module
        //      . add moduels 
        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
        $aPackageBusiness = Phpfox::getService('directory.package')->getById($package_id);
        $aOldModuleView = Phpfox::getService('directory')->getModuleViewInBusiness($iBusinessId, $aBusiness);
        $moduleSupport = Phpfox::getService('directory.helper')->getListNameOfBlockInDetailLinkBusiness();

        // delete old module support 
        foreach ($aOldModuleView as $keyaOldModuleView => $valueaOldModuleView) {
            if (in_array($keyaOldModuleView, $moduleSupport)) {
                $isOldItemInNewItem = false;
                foreach ($aPackageBusiness['modules'] as $keymodules => $valuemodules) {
                    if ($valuemodules['module_name'] == $keyaOldModuleView) {
                        $isOldItemInNewItem = true;
                        break;
                    }
                }
                if ($isOldItemInNewItem == false) {
                    $this->database()->delete(Phpfox::getT('directory_business_module'), 'data_id = ' . (int)$valueaOldModuleView['data_id']);
                }
            }
        }

        // add new module support
        foreach ($aPackageBusiness['modules'] as $keymodules => $valuemodules) {
            $isNewItemInOldItem = false;
            foreach ($aOldModuleView as $keyaOldModuleView => $valueaOldModuleView) {
                if (in_array($keyaOldModuleView, $moduleSupport)) {
                    if ($valuemodules['module_name'] == $keyaOldModuleView) {
                        $isNewItemInOldItem = true;
                        break;
                    }
                }
            }
            if ($isNewItemInOldItem == false) {
                $this->database()->insert(Phpfox::getT('directory_business_module'), array(
                    'business_id' => $iBusinessId,
                    'module_id' => $valuemodules['module_id'],
                    'contentpage' => '',
                    'contentpage_parsed' => '',
                    'module_phrase' => $valuemodules['module_phrase'],
                    'module_name' => $valuemodules['module_name'],
                    'module_type' => $valuemodules['module_type'],
                    'module_description' => '',
                    'is_show' => 1,
                    'module_landing' => $valuemodules['module_landing'],
                ));
            }
        }

        $this->database()->update(Phpfox::getT('directory_business')
            , array(
                'package_id' => $aPackageBusiness['package_id'],
                'package_name' => $aPackageBusiness['name'],
                'package_expire_number' => $aPackageBusiness['expire_number'],
                'package_expire_type' => $aPackageBusiness['expire_type'],
                'package_fee' => $aPackageBusiness['fee'],
                'package_currency' => $aPackageBusiness['currency'],
                'package_max_cover_photo' => $aPackageBusiness['max_cover_photo'],
                'package_data' => json_encode($aPackageBusiness),
            ),
            'business_id = ' . (int)$iBusinessId);

        $this->updateDefaultThemeIdForBusiness($aPackageBusiness['themes'][0]['theme_id'], $iBusinessId);

        return true;

    }


    public function updateRenewalNotificationForBusiness($iBusinessId, $renewal_notification)
    {

        $this->database()->update(Phpfox::getT('directory_business')
            , array(
                'renewal_type' => $renewal_notification,
            ),
            'business_id = ' . (int)$iBusinessId);

        return true;

    }

    public function updateDefaultThemeIdForBusiness($iBusinessId, $iThemeId)
    {
        $this->database()->update(Phpfox::getT('directory_business')
            , array(
                'theme_id' => $iThemeId,
            ),
            'business_id = ' . (int)$iBusinessId);

        return true;
    }

    public function addPermissionForAdmin($iBusinessId, $aBusiness = null)
    {
        $roleAdmin = Phpfox::getService('directory')->getMemberRolesByBusinessIdWithType($iBusinessId, 'admin');
        if (null == $aBusiness) {
            $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
        }

        if (isset($roleAdmin['role_id']) && isset($aBusiness['user_id'])) {
            $this->database()->insert(Phpfox::getT('directory_business_userroledata'), array(
                'user_id' => $aBusiness['user_id'],
                'role_id' => $roleAdmin['role_id'],
                'time_stamp' => PHPFOX_TIME
            ));
        }
    }

    public function markAsRead($iAnnouncementId)
    {
        $this->database()->insert(Phpfox::getT('directory_business_announcement_hide'), array(
            'announcement_id' => $iAnnouncementId,
            'user_id' => Phpfox::getUserId(),
        ));
        return true;
    }

    public function updateUserMemberRole($iBusinessId, $iUserId, $iRoleId)
    {

        $aRow = Phpfox::getService('directory')->getUserMemberRole($iUserId, $iBusinessId);
        if (count($aRow)) {
            $this->database()->update(
                Phpfox::getT('directory_business_userroledata'),
                array('role_id' => $iRoleId),
                "data_id = " . (int)$aRow['data_id']
            );
            return true;
        } else {
            $this->database()->insert(Phpfox::getT('directory_business_userroledata'), array(
                'user_id' => $iUserId,
                'role_id' => $iRoleId,
                'time_stamp' => PHPFOX_TIME
            ));
            return true;
        }

    }

    public function deleteUserMemberRole($iBusinessId, $iUserId)
    {
        $aRow = $this->database()->select('e.*')
            ->from(Phpfox::getT('directory_business_userroledata'), 'e')
            ->join(Phpfox::getT('directory_business_memberrole'), 'mbr', 'mbr.role_id = e.role_id')
            ->where('e.user_id = ' . (int)$iUserId . ' AND mbr.business_id = ' . (int)$iBusinessId)
            ->execute('getSlaveRow');

        $this->database()->delete(Phpfox::getT('directory_business_userroledata'), 'data_id = ' . (int)$aRow['data_id']);
        return true;
    }

    public function addReviewForBusiness($iBusinessId, $sTitle, $sContent, $iRating)
    {

        $oFilter = Phpfox::getLib('parse.input');

        $this->database()->insert(Phpfox::getT('directory_review'), array(
            'business_id' => $iBusinessId,
            'user_id' => Phpfox::getUserId(),
            'timestamp' => PHPFOX_TIME,
            'rating' => $iRating,
            'title' => $oFilter->clean($sTitle),
            'content' => $oFilter->clean($sContent),
        ));

        /*update total business id*/
        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
        $iTotalReviews = Phpfox::getService('directory')->getCountReviewOfBusiness($iBusinessId);


        $iTotalReviews = $iTotalReviews;
        $iTotalRating = $aBusiness['total_rating'] + $iRating;

        $iScore = round(($iTotalRating) / ($iTotalReviews));

        $this->database()->update(Phpfox::getT('directory_business')
            , array(
                'total_rating' => $iTotalRating,
                'total_score' => $iScore,
                'total_review' => $iTotalReviews
            ),
            'business_id = ' . (int)$iBusinessId);

    }

    public function editReviewForBusiness($iBusinessId, $sTitle, $sContent, $iRating)
    {

        $oFilter = Phpfox::getLib('parse.input');

        $this->database()->update(Phpfox::getT('directory_review'), array(
            'timestamp' => PHPFOX_TIME,
            'rating' => $iRating,
            'title' => $oFilter->clean($sTitle),
            'content' => $oFilter->clean($sContent),
        ),
            'business_id = ' . (int)$iBusinessId . ' AND user_id = ' . Phpfox::getUserId()
        );

        /*update total business id*/
        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
        $iTotalReviews = Phpfox::getService('directory')->getCountReviewOfBusiness($iBusinessId);
        $iTotalRating = Phpfox::getService('directory')->getCountRatingOfBusiness($iBusinessId);

        $iScore = round(($iTotalRating) / ($iTotalReviews));

        $this->database()->update(Phpfox::getT('directory_business')
            , array(
                'total_rating' => $iTotalRating,
                'total_score' => $iScore,
                'total_review' => $iTotalReviews
            ),
            'business_id = ' . (int)$iBusinessId);

    }

    public function cleanTextWithStripTag($sText)
    {
        $oFilter = Phpfox::getLib('parse.input');

        return $oFilter->clean(strip_tags($sText));

    }

    public function inviteFriends($aVals, $aBusiness)
    {
        $this->sentInvite($aVals, $aBusiness);

        return $aBusiness['business_id'];
    }

    /**
     * Send Email and Notification for friends
     * @author TienNPL
     */
    public function sentInvite($aVals, $aBusiness)
    {
        $oParseInput = Phpfox::getLib('parse.input');

        // Get invited friend and email if have
        if (isset($aVals['emails']) || isset($aVals['invite'])) {
            $aInvites = $this->database()->select('invited_user_id, invited_email')
                ->from(Phpfox::getT('directory_invite'))
                ->where('business_id = ' . (int)$aBusiness['business_id'])
                ->execute('getRows');

            $aInvited = array();
            foreach ($aInvites as $aInvite) {
                $aInvited[(empty($aInvite['invited_email']) ? 'user' : 'email')][(empty($aInvite['invited_email']) ? $aInvite['invited_user_id'] : $aInvite['invited_email'])] = TRUE;
            }
        }
        // Business link
        $sLink = Phpfox::getLib('url')->permalink('directory.detail', $aBusiness['business_id'], $aBusiness['name']);

        // Email Message
        if (!empty($aVals['personal_message'])) {
            $sMessage = $aVals['personal_message'];
        } else {
            //in case user leave message box empty
            $sMessage = _p('full_name_invited_you_to_the_title', array(
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'title' => $oParseInput->clean($aBusiness['name'], 255),
                    'link' => $sLink
                )
            );
        }

        // Email Subject
        if (!empty($aVals['subject'])) {
            $sSubject = $aVals['subject'];
        } else {
            //in case user leave subject box empty
            $sSubject = _p('full_name_invited_you_to_the_business_title', array(
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'title' => $oParseInput->clean($aBusiness['name'], 255),
                )
            );
        }

        $sSubject = Phpfox::getService('directory.mail')->parseTemplate($sSubject, array($aBusiness), $iInviteId = Phpfox::getUserId(), 'owner');
        $sMessage = Phpfox::getService('directory.mail')->parseTemplate($sMessage, array($aBusiness), $iInviteId = Phpfox::getUserId(), 'owner');
        $aCustomMesssage = array(
            'subject' => $sSubject,
            'message' => $sMessage
        );


        if (isset($aVals['emails'])) {
            $aEmails = explode(',', $aVals['emails']);

            $aCachedEmails = array();
            foreach ($aEmails as $sEmail) {
                $sEmail = trim($sEmail);
                if (!Phpfox::getLib('mail')->checkEmail($sEmail)) {
                    continue;
                }

                if (isset($aCachedEmails[$sEmail]) && $aCachedEmails[$sEmail] == true) {
                    continue;
                }

                $bResult = Phpfox::getService('directory.mail.process')->sendEmailTo($sType = 0, $aBusiness['business_id'], $aReceivers = $sEmail, $aCustomMesssage);
                if ($bResult) {
                    $this->database()->insert(Phpfox::getT('directory_invite'), array(
                            'business_id' => $aBusiness['business_id'],
                            'inviting_user_id' => Phpfox::getUserId(),
                            'invited_email' => $sEmail,
                            'time_stamp' => PHPFOX_TIME
                        )
                    );
                }
            }
        }
        if (isset($aVals['invite']) && !empty($aVals['invite']) && is_array($aVals['invite'])) {
            $sUserIds = '';
            foreach ($aVals['invite'] as $iUserId) {
                if (!is_numeric($iUserId)) {
                    continue;
                }
                $sUserIds .= $iUserId . ',';
            }
            $sUserIds = rtrim($sUserIds, ',');

            $aUsers = $this->database()->select('user_id, email, language_id, full_name')
                ->from(Phpfox::getT('user'))
                ->where('user_id IN(' . $sUserIds . ')')
                ->execute('getSlaveRows');


            foreach ($aUsers as $aUser) {

                $bResult = Phpfox::getService('directory.mail.process')->sendEmailTo($sType = 0, $aBusiness['business_id'], $aReceivers = $aUser['user_id'], $aCustomMesssage);

                if ($bResult) {
                    $iInviteId = $this->database()->insert(Phpfox::getT('directory_invite'), array(
                            'business_id' => $aBusiness['business_id'],
                            'inviting_user_id' => Phpfox::getUserId(),
                            'invited_user_id' => $aUser['user_id'],
                            'time_stamp' => PHPFOX_TIME
                        )
                    );
                }

                (Phpfox::isModule('request') ? Phpfox::getService('request.process')->add('directory_invited', $aBusiness['business_id'], $aUser['user_id']) : null);
            }
        }
    }
    public function closeBusiness($iBusinessId)
    {
        $this->database()->update(Phpfox::getT('directory_business'), array('business_status' => Phpfox::getService('directory.helper')->getConst('business.status.closed')), "business_id = {$iBusinessId}");
        return true;
    }
    public function openBusiness($iBusinessId)
    {
        $this->database()->update(Phpfox::getT('directory_business'), array('business_status' => Phpfox::getService('directory.helper')->getConst('business.status.running')), "business_id = {$iBusinessId}");
        return true;
    }

    public function updatePackageDataForBussiness($iBusinessId, $aPackageData)
    {
        $this->database()->update(Phpfox::getT('directory_business'), ['package_data' => json_encode($aPackageData)], "business_id = {$iBusinessId}");
    }

    private function _processUploadForm($aVals, &$aInsert)
    {
        if (!empty($aVals['logo_path']) && (!empty($aVals['temp_file']) || !empty($aVals['remove_photo']))) {
            if ($this->_deleteImage($aVals['logo_path'], 'directory_business_logo', $aVals['server_id'])) {
                $aInsert['logo_path'] = null;
                $aInsert['server_id'] = 0;
            }
        }

        if (!empty($aVals['temp_file'])) {
            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
            if (!empty($aFile)) {
                $aInsert['logo_path'] = 'yndirectory'. PHPFOX_DS . $aFile['path'];
                $aInsert['server_id'] = $aFile['server_id'];
                Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
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
}

?>