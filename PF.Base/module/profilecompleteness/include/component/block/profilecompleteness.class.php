<?php

class Profilecompleteness_Component_Block_Profilecompleteness extends phpfox_component
{

    public function process()
    {
        $user_id = phpfox::getUserId();
        $profile_id = $this->getParam("user_id");
        if (!$profile_id) { // not in profile page
            $profile_id = $user_id;
        }
        $bShouldNotShow = false;
        if ($user_id == 0 || $user_id != $profile_id) {
            $bShouldNotShow = true;
        } else {
            Phpfox::getService("profilecompleteness.process")->ChangeTableWhenToProfile();
            $aRow_Settings = phpfox::getService("profilecompleteness.process")->getProfileCompletenessSettings();

            list($iGroup_id, $iPercent, $Key, $PercentValue, $isPhoTo, $PercentTotal) = Phpfox::getService('profilecompleteness.process')->getPercentProfileCompleteness($user_id);
            if (!is_numeric($iGroup_id)) {
                $iGroup_id = 'basic';
            }

            if ($PercentTotal == 100) {

                $is_turnoff = $aRow_Settings['check_complete'];
                if ($is_turnoff == 1) {
                    $bShouldNotShow = true;
                }
            }
            if ($PercentTotal != 100 || $bShouldNotShow == false) {
                $this->template()->assign(array(
                    'sHeader' => _p('profilecompleteness.profile_completeness'),
                    'iPercent' => $iPercent,
                    'Key' => $Key,
                    'PercentValue' => $PercentValue,
                    'isPhoTo' => $isPhoTo,
                    'PercentTotal' => $PercentTotal,
                    'colorbackground' => $aRow_Settings['gaugecolor'],
                    'iGroup_id' => $iGroup_id
                ));
            }
        }

        if ($bShouldNotShow) {
            return false;
        }

        return 'block';
    }

}

