<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Profilecompleteness_Component_Controller_Admincp_Settings extends Phpfox_Component
{
    public function process()
    {
        $aRow = phpfox::getService('profilecompleteness.process')->getProfileCompletenessSettings();

        if ($aVals = $this->request()->get('val')) {
            if ($this->_validate($aVals)) {
                Phpfox::getService("profilecompleteness.process")->InsertProfileCompletenessSettings($aVals);
                $this->url()->send('current', null, _p('profilecompleteness.update_global_settings_successfully'));
            }
        }

        asset('<link href="' . home() . 'PF.Base/static/jscript/colorpicker/css/colpick.css" rel="stylesheet">');
        $this->template()
            ->setBreadCrumb(_p('profilecompleteness.global_settings'), $this->url()->makeurl('admincp.profilecompleteness'))
            ->assign(array(
                'aForms' => $aRow,
            ))
            ->setHeader(array(
            'colorpicker/js/colpick.js' => 'static_script'
        ));
    }

    private function _validate(&$aVals)
    {
        if (!Phpfox::getService("profilecompleteness.process")->is_Hexa($aVals['gaugecolor'])) {
            Phpfox_Error::set(_p('Gauge Color in Hex is invalid'));
        }

        return Phpfox_Error::isPassed();
    }
}
