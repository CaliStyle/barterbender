<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\YouNet_UltimateVideos\Controller\Admin;

use Phpfox;
use Phpfox_Component;

defined('PHPFOX') or exit('NO DICE!');

class AddCustomFieldController extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $bIsEditGroup = false;
        $bAddInput = false;

        $aValidation = array(
            'group_name' => array(
                'def' => 'required',
                'title' => _p("Group Name cannot be empty")
            ),
        );

        if ($aVals = $this->request()->getArray('val')) {
            if ($this->__isValid($aVals)) {

                if ($bIsEditGroup || isset($aVals['group_id'])) {
                    if (Phpfox::getService('ultimatevideo.custom.group')->updateGroup($aVals['group_id'], $aVals)) {
                        $this->url()->send('admincp.ultimatevideo.customfield', [], _p('custom_field_groups_successfully_updated'));
                    }
                } else {
                    if ($iGroupId = Phpfox::getService('ultimatevideo.custom.group')->addGroup($aVals)) {
                        $this->url()->send('admincp.ultimatevideo.customfield', [], _p('custom_field_groups_successfully_added'));
                    }
                }
            }
        }
        /*$aGroup = Phpfox::getService('ultimatevideo.custom.group')->getGroupForEdit()*/
        $aLanguages = Phpfox::getService('language')->get();
        $this->template()->setTitle($bIsEditGroup ? _p('edit_custom_field_groups') : _p('add_custom_field_groups'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("ultimate_videos"), Phpfox::getLib('url')->makeUrl('admincp.app', ['id' => 'YouNet_UltimateVideos']))
            ->setBreadcrumb($bIsEditGroup ? _p('edit_custom_field_groups') : _p('add_custom_field_groups'))
            ->assign(array(
                    'sCorePath' => Phpfox::getParam('core.path_actual'),
                    'aLanguages' => $aLanguages,
                    'sUrl' => $this->url()->makeUrl('ultimatevideo.admincp.customfield.add'),
                )
            )->setHeader(array(
                'jquery/ui.js' => 'static_script',
            ));

        return null;
    }

    private function __isValid($aVals)
    {
        $emptyGroupName = false;
        $group_name = $aVals['group_name'];
        foreach ($group_name as $keygroup_name => $valuegroup_name) {
            if (is_array($valuegroup_name)) {
                foreach ($valuegroup_name as $keyvaluegroup_name => $valuevaluegroup_name) {
                    if (strlen(trim($valuevaluegroup_name)) == 0) {
                        $emptyGroupName = true;
                        break;
                    }
                }
            } else {
                if (strlen(trim($valuegroup_name)) == 0) {
                    $emptyGroupName = true;
                    break;
                }
            }
        }
        if ($emptyGroupName) {
            \Phpfox_Error::set(_p('group_name_cannot_be_empty'));
            return false;
        }

        return true;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {

    }
}
