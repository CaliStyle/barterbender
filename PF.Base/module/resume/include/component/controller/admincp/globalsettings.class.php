<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 * 
 */
class Resume_Component_Controller_Admincp_GlobalSettings extends Phpfox_Component
{
	/**
     * Process method which is used to process this component
     * @see Resume_Service_Setting_Process
     */
    public function process()
	 {
	 	$oSetting = Phpfox::getService("resume.setting");
		$oSettingProcess = Phpfox::getService("resume.setting.process");
		
		// Get custom group information
		$aCustomGroup = $oSetting->getCustomGroupInfo();
		$aCustomGroup_WhoView = $aCustomGroup;
		
        $aPers = $oSetting->getAllPermissions();
		$aPublic = $oSetting->getPermissionByName('public_resume');
		
		// Get global setting
		$aWhoViewedMeGroup  = $oSetting->getSettings(1);		
		$aViewAllResumeGroup = $oSetting->getSettings(2);
		
		foreach($aCustomGroup as $key=>$Group){
			$aCustomGroup[$key]['view_all_resume'] = Phpfox::GetService('user.group.setting')->getGroupParam($Group['user_group_id'],'resume.view_all_resume');
		}
		
		if ($aVals = $this->request()->getArray('val'))
		{
			$oSettingProcess->updateSetting($aVals);
            
            $oSettingProcess->removePermission();
            
			$oSettingProcess->addPermission('public_resume', $aVals['public_resume']);
			$oSettingProcess->addPermission('get_basic_information', $aVals['get_basic_information']);
			$oSettingProcess->addPermission('display_resume_in_profile_info', $aVals['display_resume_in_profile_info']);
			$oSettingProcess->addPermission('position', $aVals['position']);
			$this->url()->send('admincp.resume.globalsettings', array(), _p('resume.settings_successfully_updated'));
		}
		// Set breadcrumb
		$this->template()
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_resume'), $this->url()->makeUrl('admincp.app').'?id=__module_resume')
            ->setBreadCrumb(_p('resume.admin_menu_global_settings'), $this->url()->makeurl('admincp.resume.globalsettings'));
		
		$this->template()->assign(array(
			'aCustomGroups' 		=> $aCustomGroup,
			'aWhoViewedMeGroup'	=> $aWhoViewedMeGroup,
			'aPublic' => $aPublic,
			'aViewAllResumeGroup'	=> $aViewAllResumeGroup,
            'aPers' => $aPers
		));
	 }
}
	