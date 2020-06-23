<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright      YouNet Company
 * @author         LyTK
 * @package        Module_ProfilePopup
 * @version        3.01
 */
class ProfilePopup_Component_Controller_Admincp_User extends Phpfox_Component
{

        /**
         * Class process method wnich is used to execute this component.
         */
        public function process()
        {
                $oProfilePopup = Phpfox::getService('profilepopup');
                $oProfilePopupProcess = Phpfox::getService('profilepopup.process');

                $oProfilePopupProcess->synchronizeAllCustomFieldInSystem();
                $aAllItems = $oProfilePopup->getAllItems(1);
                $iLen = count($aAllItems);

                for ($idx = 0; $idx < $iLen; $idx++)
                {
                        $aAllItems[$idx]['checked'] = '';
                        $aAllItems[$idx]['lang_name'] = '';
                        if (intval($aAllItems[$idx]['is_active']) == 1 && intval($aAllItems[$idx]['is_display']) == 1)
                        {
                                $aAllItems[$idx]['checked'] = 'checked';
                        }

                        if (intval($aAllItems[$idx]['is_custom_field']) == 1)
                        {
                                $aAllItems[$idx]['lang_name'] = _p($aAllItems[$idx]['phrase_var_name']);
                        } else
                        {
                                $aAllItems[$idx]['lang_name'] = _p('profilepopup.' . $aAllItems[$idx]['phrase_var_name']);
                        }
                }
				
				//	get resume fields
				$aResumeItems = $oProfilePopup->getItemsByModule(1, 'user', 'resume');
				$iResumeLen = count($aResumeItems);
                for ($idx = 0; $idx < $iResumeLen; $idx++)
                {
                        $aResumeItems[$idx]['checked'] = '';
                        $aResumeItems[$idx]['lang_name'] = '';
                        if (intval($aResumeItems[$idx]['is_active']) == 1 && intval($aResumeItems[$idx]['is_display']) == 1)
                        {
                                $aResumeItems[$idx]['checked'] = 'checked';
                        }

                        if (intval($aResumeItems[$idx]['is_custom_field']) == 1)
                        {
                                $aResumeItems[$idx]['lang_name'] = _p($aResumeItems[$idx]['phrase_var_name']);
                        } else
                        {
                                $aResumeItems[$idx]['lang_name'] = _p('profilepopup.' . $aResumeItems[$idx]['phrase_var_name']);
                        }
                }

                $this->template()->setTitle(_p('profilepopup.user_global_settings'))
                        ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
                        ->setBreadCrumb(_p('module_profilepopup'), $this->url()->makeUrl('admincp.app').'?id=__module_profilepopup')
                        ->setBreadcrumb(_p('profilepopup.user_global_settings'), $this->url()->makeUrl('admincp.profilepopup.user'))
                        ->assign(array(
                                'aAllItems' => $aAllItems
                                , 'aResumeItems' => $aResumeItems
                                )
                        )
                        ->setHeader('cache', array(
                                'quick_edit.js' => 'static_script'
                                )
                );
        }

        /**
         * Garbage collector. Is executed after this class has completed
         * its job and the template has also been displayed.
         */
        public function clean()
        {
                (($sPlugin = Phpfox_Plugin::get('profilepopup.component_controller_admincp_user_clean')) ? eval($sPlugin) : false);
        }

}

