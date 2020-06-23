<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Profilecompleteness_Component_Controller_Admincp_Weightsettings extends Phpfox_Component
{

    public function process()
    {
        $user_id = phpfox::getUserId();
        //Get image weight
        $bIsSuccess = true;
        $aPhoto = PHPFOX::getService('profilecompleteness.process')->getProfileCompletenessSettings();
        $ListCustom = Phpfox::getService('custom')->getForListing();
        $aRow = phpfox::getService('profilecompleteness.process')->getProfileCompletenessWeight($user_id);

        Phpfox::getService("profilecompleteness.process")->ChangeTableWhenToProfile();

        foreach ($ListCustom as $KeyCustom => $Custom) {
            if (!empty($Custom['child'])) {
                foreach ($Custom['child'] as $Key => $Child) {
                    if ($Child['is_active'] == 1) {
                        $temp = phpfox::getService("profilecompleteness.process")->is_value_profilecomplteteness_weight("cf_" . $Child['field_name']);
                        if ($temp == 2) {
                            $valuetemp = 0;
                        } else {
                            $valuetemp = $temp;
                        }
                        if (isset($aRow["cf_" . $Child['field_name']]) != null) {
                            $valuetemp = $aRow["cf_" . $Child['field_name']];
                        }
                        $ListCustom[$KeyCustom]['child'][$Key]['weight'] = $valuetemp;
                    } else {
                        if (isset($aRow["cf_" . $Child['field_name']])) {
                            unset($aRow["cf_" . $Child['field_name']]);
                        }
                    }
                }
            }
        }

        // Check if submit the forms
        if ($val = $this->request()->get('val')) {
            $val['signature'] = !empty($val['signature']) ? $val['signature'] : 0;

            if (!is_numeric($this->request()->get('user_image')) || $this->request()->get('user_image') < 0) {
                $bIsSuccess = false;
                Phpfox_Error::set(phpfox::getService("profilecompleteness.process")->ConvertIdToString('user_image') . " " . _p('profilecompleteness.is_invalid'));
            }
            foreach ($val as $key => $value) {
                if (!is_numeric($value) || $value < 0) {
                    Phpfox_Error::set(phpfox::getService("profilecompleteness.process")->ConvertIdToString($key) . " " . _p('profilecompleteness.is_invalid'));
                    $bIsSuccess = false;
                }
            }
            if ($bIsSuccess) {
                Phpfox::getService("profilecompleteness.process")->CreateTableWeightSetting($ListCustom);
                Phpfox::getService("profilecompleteness.process")->updateUserImageWeight($this->request()->get('user_image'));
                Phpfox::getService("profilecompleteness.process")->InsertProfileCompletenessWeight($val);
                $this->url()->send('admincp.profilecompleteness.weightsettings', null,
                    _p('profilecompleteness.update_weight_settings_successfully'));
            }
        }

        $settingdefault = phpfox::getService("profilecompleteness.process")->getSettingDefaultPhpfox();

        $this->template()->setBreadCrumb(_p('profilecompleteness.admin_menu_weight_settings'),
            $this->url()->makeurl('admincp.profilecompleteness.weightsettings'));
        $this->template()->assign(array(
            'aForms' => $aRow,
            'bSuccess' => $bIsSuccess,
            'ListCustom' => $ListCustom,
            'settingdefault' => $settingdefault,
            'aPhoto' => $aPhoto
        ));
    }

}

