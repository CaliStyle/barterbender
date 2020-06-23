<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class profilecompleteness_service_process extends Phpfox_Service {

    public function getProfileCompletenessWeight($user_id) {
        $aRow = $this->database()->select('*')
                ->from(phpfox::getT('profilecompleteness_weight'))
                ->execute('getSlaveRow');
        if (count($aRow) == 0) {
            $aRow = $this->InitProfileCompletenessWeight();
        } else {
            $ListArrayRight = array();
            $ListCustom = Phpfox::getService('custom')->getForListing();
            foreach ($ListCustom as $Key => $CustomField) {
                if (isset($CustomField['child']) && !empty($CustomField['child'])) {
                    if (count($CustomField['child']) > 0 && $CustomField['child'][0]['group_id'] != 0 && $CustomField['is_active'] == 1) {
                        
                    } else {
                        if (!empty($CustomField['child'])) {
                            foreach ($CustomField['child'] as $child)
                                $ListArrayRight[] = "cf_" . $child['field_name'];
                        }
                    }
                }
            }

            $kq_aRow = array();
            foreach ($aRow as $Key => $Row) {
                if (!in_array($Key, $ListArrayRight)) {
                    $kq_aRow[$Key] = $Row;
                }
            }
            return $kq_aRow;
        }

        return $aRow;
    }

    public function getTypeCustomField($field_name) {
        $aRow = $this->database()->select('*')
                ->from(phpfox::getT('custom_field'))
                ->where('field_name="' . $field_name . '"')
                ->execute('getSlaveRow');
        return $aRow;
    }

    public function CreateTableWeightSetting($ListCustom) {
        $attribute = "";

        $aRow = array();
        $aRow['country_iso'] = 1;
        $aRow['city_location'] = 1;
        $aRow['postal_code'] = 1;
        $aRow['birthday'] = 1;
        $aRow['gender'] = 1;
        $aRow['cf_relationship_status'] = 1;
        $aRow['signature'] = 0;
        $aRow['user_id'] = phpfox::getUserId();

        foreach ($aRow as $KeyRow => $Row) {
            $attribute.=$KeyRow . " int(11) NOT NULL,";
        }

        foreach ($ListCustom as $KeyCustom => $Custom) {

            foreach ($Custom['child'] as $Key => $Child) {

                $attribute.="cf_" . $Child['field_name'] . " int(11) NOT NULL,";
            }
        }

        if ($attribute != "") {
            $prefix = Phpfox::getParam(array('db', 'prefix'));
            $this->database()->query("drop table `" . $prefix . "profilecompleteness_weight`");

            $this->database()->query("
                    CREATE TABLE IF NOT EXISTS `" . $prefix . "profilecompleteness_weight` (
          `weight_id` int(11) NOT NULL AUTO_INCREMENT,
          " . $attribute . "
          PRIMARY KEY (`weight_id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
        }
    }

    public function InitProfileCompletenessWeight() {
        $aRow = array();
        $aRow['country_iso'] = 1;
        $aRow['city_location'] = 1;
        $aRow['postal_code'] = 1;
        $aRow['birthday'] = 1;
        $aRow['gender'] = 1;
        $aRow['cf_relationship_status'] = 1;
        $aRow['signature'] = 0;
        $aRow['cf_about_me'] = 1;
        $aRow['cf_who_i_d_like_to_meet'] = 1;
        $aRow['cf_movies'] = 1;
        $aRow['cf_interests'] = 1;
        $aRow['cf_music'] = 1;
        $aRow['cf_smoker'] = 1;
        $aRow['cf_drinker'] = 1;
        $aRow['user_id'] = phpfox::getUserId();
        return $aRow;
    }

    public function InsertProfileCompletenessWeight($val) {
        $val['user_id'] = phpfox::getUserId();
        $val['signature'] = 0;
        $this->database()->delete(phpfox::getT('profilecompleteness_weight'), 'user_id=' . $val['user_id']);
        $this->database()->insert(phpfox::getT('profilecompleteness_weight'), $val);
    }

    public function InsertProfileCompletenessSettings($val) {
        $this->database()->delete(phpfox::getT('profilecompleteness_settings'), '1=1');
        if (isset($val['check_complete']) == false) {
            $val['check_complete'] = 0;
        }
        else
            $val['check_complete'] = 1;
        foreach ($val as $key => $value) {
            $this->database()->insert(phpfox::getT('profilecompleteness_settings'), array(
                'name' => $key,
                'default_value' => $value,
            ));
        }
    }

    public function updateUserImageWeight($iWeight) {
        $aRow = $this->database()->select('*')
                ->from(Phpfox::getT('profilecompleteness_settings'))
                ->where('name="'."user_image".'"')
                ->execute('getRow');
        if(isset($aRow['default_value']))
        {
            $this->database()->query("UPDATE " . PHPFOX::getT('profilecompleteness_settings') . " SET default_value=" . $iWeight . " WHERE name='user_image'");
        }
        else
        {
            $this->database()->insert(phpfox::getT('profilecompleteness_settings'), array(
                'name' => 'user_image',
                'default_value' => $iWeight,
            ));
        }
    }

    public function getProfileCompletenessSettings() {
        $aRows = $this->database()->select('*')
                ->from(phpfox::getT('profilecompleteness_settings'))
                ->execute('getSlaveRows');
        $aRows_settings = array();
        if (count($aRows) == 0) {
            $aRows_settings = $this->InitProfileCompletenessSettings();
        } else {
            foreach ($aRows as $aRow) {
                $aRows_settings[$aRow['name']] = $aRow['default_value'];
            }
        }
        if(!isset($aRows_settings['gaugecolor']))
        {
            $aRows_settings['gaugecolor'] = '#999999';
        }
        if(!isset($aRows_settings['check_complete']))
        {
            $aRows_settings['check_complete'] = 0;
        }
        if(!isset($aRows_settings['user_image']))
        {
            $aRows_settings['user_image'] = 2;
        }
        return $aRows_settings;
    }

    public function InitProfileCompletenessSettings() {
        $aRow = array();
        $aRow['gaugecolor'] = '#999999';
        $aRow['user_image'] = 2;
        $aRow['check_complete'] = 0;
        return $aRow;
    }

    public function is_value_profilecomplteteness_weight($value) {
        $aRow = $this->database()->select('*')
                ->from(phpfox::getT('profilecompleteness_weight'))
                ->execute('getSlaveRow');
        if (count($aRow) == 0)
            return 2;
        foreach ($aRow as $Key => $Row) {
            if ($Key != "weight_id" && $Key != "user_id" && $Key != "user_image") {
                if ($Key == $value)
                    return $aRow[$Key];
            }
        }
        return 0;
    }

    public function getPercentProfileCompleteness($user_id) {
        $aRow = $this->getProfileCompletenessWeight($user_id);
        $iUserGroupId = $this->database()->select('user_group_id')->from(Phpfox::getT('user'))->where('user_id ='.$user_id)->execute('getField');
        $ListCustom = Phpfox::getService('custom')->getForListing();
        $aCustumFields = $this->database()->select('cf.*')
                ->from(PHPFOX::getT('custom_field'),'cf')
                ->join(Phpfox::getT('custom_group'),'cg','cf.group_id = cg.group_id')
            ->where('cf.user_group_id IN (0,'.$iUserGroupId.') AND cg.user_group_id IN (0,'.$iUserGroupId.')')
                ->execute('getSlaveRows');

        foreach ($aRow as $sKey => $iValue) {
            
            $sTemp = explode('cf_', $sKey, 2);
            
            
            if (count($sTemp) == 2) {
                
                $iFlag = 0;
                foreach ($aCustumFields as $aCustomField) {
                    if($sKey == 'cf_relationship_status')
                    {
                        $iFlag=1;
                        break;
                    }
                    if ((string)$sTemp[1] === (string)$aCustomField['field_name']) {
                        
                        $iFlag = 1;
                        break;
                    }                    
                }
                if ($iFlag == 0) {
                        unset($aRow[$sKey]);
                    }

            }
        }        
        foreach ($ListCustom as $KeyCustom => $Custom) {
			if(isset($Custom['child'])){
				foreach ($Custom['child'] as $Key => $Child) {
					if ($Child['is_active'] != 1) {
						if (isset($aRow["cf_" . $Child['field_name']])) {
							unset($aRow["cf_" . $Child['field_name']]);
						}
					}
				}
			}
        }

        $aRow_settings = $this->getProfileCompletenessSettings();
        $aRow_infoUser = $this->getInfoUser($user_id);
        $defaultsetting = $this->getSettingDefaultPhpfox();

        $countWeight = 2;
        $aRow['user_image'] = $aRow_settings['user_image'];
        
        if (count($aRow_settings) > 0 && isset($aRow_settings['user_image']) != null) {
            $countWeight = $aRow_settings['user_image'];
        }
        $maxKey = "user_image";
        $valueKey = $countWeight;
        if ($aRow_infoUser['user_image'] != "" || $aRow_infoUser['user_image'] != null) {
            $maxKey = "";
            $valueKey = 0;
        }

        $isPhoTo = 0;
        $currentWeight = 0;
        $CountremainWeight = 0;

        foreach ($aRow as $Key => $value) {
            if ($Key != "weight_id" && $Key != "user_id" && $Key != "user_image") {
                if ($defaultsetting['enable_relationship_status'] == 0 && $Key == "cf_relationship_status") {
                    unset($aRow[$Key]);
                    continue;
                }
                if ($defaultsetting['cf_gender'] == 0 && $Key == "gender") {
                    unset($aRow[$Key]);
                    continue;
                }
                if ($defaultsetting['cf_birthday'] == 0 && $Key == "birthday") {
                    unset($aRow[$Key]);
                    continue;
                }
                if ($defaultsetting['cf_signature'] == 0 && $Key == "signature") {
                    unset($aRow[$Key]);
                    continue;
                }

                $countWeight+=$value;
            }
        }
        $check_last = 0;
        if ($countWeight != 0) {
            foreach ($aRow as $Key => $value) {
                if ($Key != "weight_id" && $Key != "user_id") {
                    $getcf = explode("cf_", $Key);
                    $tam = 1;
                    if (count($getcf) > 1 && $Key != "cf_relationship_status") {
                        $kq = $getcf[1];

                        $CustomField_kq = $this->getCustomField(phpfox::getUserId(), $kq);
                        {
                            if (count($CustomField_kq) > 0) {
                                $check_last = 1;                              
                                $tam = 0;
                                $currentWeight+=$value * 1.0 / $countWeight;
                            }
                        }
                    }

                    if ($Key == "cf_relationship_status") {

                        $CustomField_relation = $this->getRelationData(phpfox::getUserId());
                        if ($CustomField_relation > 0) {
                            $tam = 0;
                            $currentWeight+=$value * 1.0 / $countWeight;
                        } else {
                            $CountremainWeight++;

                            if ($value > $valueKey) {
                                $valueKey = $value;
                                $maxKey = $Key;
                                $isPhoTo = 1;
                            }
                            continue;
                        }
                    }
                    if ($tam == 1) {

                        if (isset($aRow_infoUser[$Key]) == null || $aRow_infoUser[$Key] == "" || ($Key == "gender" && $aRow_infoUser[$Key] == 0)) {
                            $CountremainWeight++;

                            if ($value > $valueKey) {
                                $valueKey = $value;
                                $maxKey = $Key;
                                $isPhoTo = 1;
                            }
                        } else {

                            $currentWeight+=$value * 1.0 / $countWeight;
                        }
                    }
                }
            }
        }
        
        $PercentValue = 100;
        if ($countWeight != 0) {
            $PercentValue = round($valueKey * 1.0 / $countWeight * 100);
        }

        $PercentTotal = round($currentWeight * 100);
        if ($CountremainWeight == 2 && $check_last == 0)
            $PercentValue = 100 - $PercentTotal;
        if($CountremainWeight == 1)
            $PercentValue = 100 - $PercentTotal;
        if ($CountremainWeight == 0)
            $PercentTotal = 100;
        return array($this->ConvertGroupToString($maxKey), $PercentTotal, $this->ConvertIdToString($maxKey), $PercentValue, $isPhoTo, $PercentTotal);
    }

    public function getInfoUser($user_id) {
        $aRow = $this->database()->select('*')
                ->from(phpfox::getT('user'), 'user')
                ->leftJoin(phpfox::getT('user_field'), 'field', 'field.user_id=' . $user_id)
                ->leftJoin(phpfox::getT('user_custom'), 'custom', 'custom.user_id=' . $user_id)
                ->where('user.user_id=' . $user_id)
                ->execute('getSlaveRow');
        return $aRow;
    }

    public function getCustomField($user_id, $field_name) {
        $aRow = $this->database()->select('*')
                ->from(phpfox::getT('user'), 'user')
                ->join(phpfox::getT('user_custom_multiple_value'), 'custom', 'custom.user_id=user.user_id')
                ->join(phpfox::getT('custom_field'), 'field', 'field.field_name="' . $field_name . '" and custom.field_id=field.field_id')
                ->where('user.user_id=' . $user_id)
                ->execute('getSlaveRow');
        return $aRow;
    }

    public function getNameCustomField($field_name) {

        $aRow = $this->database()->select('*')
                ->from(phpfox::getT('custom_field'), 'field')
                ->where('field.field_name="' . $field_name . '"')
                ->execute('getSlaveRow');
        return $aRow;
    }

    public function getCustomGroup($sField_name) {
        $aRow = $this->database()->select('group_id')
                ->from(phpfox::getT('custom_field'), 'field')
                ->where('field.field_name="' . $sField_name . '"')
                ->execute('getSlaveRow');
        return $aRow;
    }

    public function getRelationData($user_id) {
        $aRow = $this->database()->select('*')
                ->from(phpfox::getT('custom_relation_data'))
                ->where('user_id=' . $user_id)
                ->order('relation_data_id desc')
                ->limit(1)
                ->execute('getSlaveRow');
        if (count($aRow) == 0)
            return 0;
        if ($aRow['relation_id'] == 1)
            return 0;
        return 1;
    }

    public function updateBlockProfileCompleteness($is_active) {
        $this->database()->update(phpfox::getT('block'), array('is_active' => $is_active), 'component="' . "profilecompleteness" . '" and module_id="' . "profilecompleteness" . '"');
        $this->cache()->remove('block', 'substr');
    }

    public function getBlockProfileCompleteness() {
        $aRow = $this->database()->select('*')
                ->from(phpfox::getT('block'))
                ->where('component="' . "profilecompleteness" . '" and module_id="' . "profilecompleteness" . '"')
                ->execute('getSlaveRow');
        if (count($aRow) == 0)
            return -1;
        return $aRow['is_active'];
    }

    public function is_Hexa($value) {
        $pattern = '/^#[a-f0-9]{6}$/i';
        preg_match($pattern, $value, $matches);
        if ($matches == null)
            return 0;
        return 1;
    }

    public function ConvertIdToString($Id) {
        $string = "";

        switch ($Id) {
            case "country_iso":
                $string = _p('profilecompleteness.location');
                break;
            case "city_location":
                $string = _p('profilecompleteness.city');
                break;
            case "postal_code":
                $string = _p('profilecompleteness.zip_postal_code');
                break;
            case "birthday":
                $string = _p('profilecompleteness.date_of_birth');
                break;
            case "gender":
                $string = _p('profilecompleteness.gender');
                break;
            case "cf_relationship_status":
                $string = _p('profilecompleteness.relationship_status');
                break;
            case "signature":
                $string = _p('profilecompleteness.forum_signature');
                break;
            case "user_image":
                $string = _p('profilecompleteness.photo');
                break;

            default:

                $getcf = explode("cf_", $Id, 2);

                $tam = 1;

                if (count($getcf) > 1) {

                    $kq = $getcf[1];

                    $CustomField_kq = $this->getNameCustomField($kq);
                    $string = _p($CustomField_kq['phrase_var_name']);
                }
        }
        return $string;
    }

    //By HT

    public function ConvertGroupToString($Id) {
        $string = "";

        switch ($Id) {
            case "country_iso":
                $string = _p('profilecompleteness.location');
                break;
            case "city_location":
                $string = _p('profilecompleteness.city');
                break;
            case "postal_code":
                $string = _p('profilecompleteness.zip_postal_code');
                break;
            case "birthday":
                $string = _p('profilecompleteness.date_of_birth');
                break;
            case "gender":
                $string = _p('profilecompleteness.gender');
                break;
            case "cf_relationship_status":
                $string = _p('profilecompleteness.relationship_status');
                break;
            case "signature":
                $string = _p('profilecompleteness.forum_signature');
                break;
            case "user_image":
                $string = _p('profilecompleteness.photo');
                break;

            default:

                $getcf = explode("cf_", $Id, 2);
                $tam = 1;

                if (count($getcf) > 1) {

                    $kq = $getcf[1];

                    $CustomField_kq = $this->getCustomGroup($kq);
                    $string = $CustomField_kq['group_id'];
                }
        }
        return $string;
    }

    public function ChangeTableWhenToProfile() {
        $aRow = $this->database()->select('*')
                ->from(phpfox::getT('profilecompleteness_weight'))
                ->execute('getSlaveRow');
        $ListCustom = Phpfox::getService('custom')->getForListing();
        foreach ($ListCustom as $KeyCustom => $Custom) {
            if (!empty($Custom['child'])) {
                foreach ($Custom['child'] as $Key => $Child) {
                    if ($Child['is_active'] != 1) {
                        unset($ListCustom[$KeyCustom]['child'][$Key]);
                        if (isset($aRow["cf_" . $Child['field_name']])) {
                            unset($aRow["cf_" . $Child['field_name']]);
                        }
                    }
                }
            }
        }

        if (count($aRow) == 0) {

            $this->CreateTableWeightSetting($ListCustom);

            $aRow = array();
            $aRow['country_iso'] = 1;
            $aRow['city_location'] = 1;
            $aRow['postal_code'] = 1;
            $aRow['birthday'] = 1;
            $aRow['gender'] = 1;
            $aRow['cf_relationship_status'] = 1;
            $aRow['signature'] = 0;
            $aRow['user_id'] = phpfox::getUserId();

            foreach ($ListCustom as $KeyCustom => $Custom) {

                foreach ($Custom['child'] as $Key => $Child) {
                    $aRow["cf_" . $Child['field_name']] = 1;
                }
            }
            $this->database()->insert(phpfox::getT('profilecompleteness_weight'), $aRow);
        }
    }

    public function getSettingDefaultPhpfox() {
        $defaultSetting = array();
        $defaultSetting['enable_relationship_status'] = Phpfox::getParam('user.enable_relationship_status');
        if ($defaultSetting['enable_relationship_status'] != 0) {
            $defaultSetting['enable_relationship_status'] = Phpfox::getUserParam('custom.can_have_relationship');
        }
        $defaultSetting['cf_gender'] = Phpfox::getUserParam('user.can_edit_gender_setting');
        $defaultSetting['cf_birthday'] = Phpfox::getUserParam('user.can_edit_dob');
        $defaultSetting['cf_signature'] = Phpfox::isModule('forum');
        return $defaultSetting;
    }

}

?>
