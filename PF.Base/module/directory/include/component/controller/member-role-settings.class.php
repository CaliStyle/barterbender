<?php

defined('PHPFOX') or exit('NO DICE!');


class Directory_Component_Controller_Member_Role_Settings extends Phpfox_Component
{
	public function process()
	{
        Phpfox::getService('directory.helper')->buildMenu();
        $iEditedBusinessId = 0;
        $view_role_id = 0;
        if ($this->request()->getInt('id') || $this->request()->get('view')) {
            $iEditedBusinessId = $this->request()->getInt('id');
            $view_role_id = $this->request()->get('view');
            $this->setParam('iBusinessId',$iEditedBusinessId);
        }
        
        if(!(int)$iEditedBusinessId){
                   $this->url()->send('directory');
        }

        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iEditedBusinessId);

        // check permission 
        if(!Phpfox::getService('directory.permission')->canEditBusiness($aBusiness['user_id'],$iEditedBusinessId) ||
            !Phpfox::getService('directory.permission')->canConfigureSettingRoleDashBoard($iEditedBusinessId)
          ){
                $this->url()->send('subscribe');
          }

     
        if ($aVals = $this->request()->getArray('val'))
        {
            $view_role_id = $aVals['role_id'];
            if(isset($aVals['submit_role_setting'])){
                Phpfox::getService('directory.process')->updateMemberRoleSetting($aVals);
            }

            $this->url()->send("directory.member-role-settings",array('id' => $iEditedBusinessId,'view' => $view_role_id ),_p('directory.updated_member_role_setting_successfully'));
        }

        $aRoles = Phpfox::getService('directory')->getMemberRolesByBusinessId($iEditedBusinessId);
        $aMemberRoleSettings = Phpfox::getService('directory')->getMemberRoleSettingByBusinessId($iEditedBusinessId);
        $aSettings = Phpfox::getService('directory')->getMemberRoleSettingByBusinessId($iEditedBusinessId);

/*        echo '<pre>';
        print_r($aMemberRoleSettings);
        die;*/
        $this->template()
                ->setEditor()
                ->setPhrase(array(
                ))
                ->setHeader('cache', array(
                    'pager.css' => 'style_css',
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'switch_legend.js' => 'static_script',
                    'switch_menu.js' => 'static_script',
                    'quick_edit.js' => 'static_script',
                    'progress.js' => 'static_script',
                    'share.js' => 'module_attachment',
                    'country.js' => 'module_core',
                ))
                ;

        $aModuleView = Phpfox::getService('directory')->getModuleViewInBusiness($aBusiness['business_id'], $aBusiness);      
        foreach ($aMemberRoleSettings as $keyaMemberRoleSettings => $valueaMemberRoleSettings) {
            foreach ($valueaMemberRoleSettings['settings'] as $keysettings => $valuesettings) {
                if(Phpfox::getService('directory.permission')->doShowSettingInBusiness($valuesettings['setting_name'], $aBusiness['business_id'], $aBusiness, $aModuleView) == false){
                    unset($aMemberRoleSettings[$keyaMemberRoleSettings]['settings'][$keysettings]);
                } else {
                    $aMemberRoleSettings[$keyaMemberRoleSettings]['settings'][$keysettings]['setting_title'] = str_replace('directory.', '', $valuesettings['setting_title']);
                }
            }
        }

        $this->template()->assign(array(
            'iBusinessid' => $iEditedBusinessId,
            'view_role_id' => $view_role_id,
            'aRoles' => $aRoles,
            'aMemberRoleSettings' => $aMemberRoleSettings,
            'aSettings' => $aSettings,
        ));
        $this->template()->setBreadcrumb(_p('directory.member_role_settings'), $this->url()->permalink('directory.edit','id_'.$iEditedBusinessId));

        Phpfox::getService('directory.helper')->loadDirectoryJsCss();

    }

    public function clean()
    {
        
    }

}
?>