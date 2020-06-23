<?php

defined('PHPFOX') or exit('NO DICE!');

class OpenSocialConnect_Component_Controller_Admincp_Providers extends Phpfox_Component
{
    public function process()
    {
        if ($aVals = $this->request()->getArray('val')) {
            Phpfox::getService('opensocialconnect')->updateProviderFields($aVals);
        }

        $iLimitSelected = 10;
        $aOpenProviders = Phpfox::getService('opensocialconnect.providers')->getOpenProviders($iLimitSelected, false);

        $this->template()->setTitle(_p('opensocialconnect.mange_social_providers'))
            ->setBreadcrumb('apps', $this->url()->makeUrl('admincp.apps'))
            ->setBreadcrumb('module_opensocialconnect', $this->url()->makeUrl('admincp.app', ['id' => '__module_opensocialconnect']))
            ->setBreadcrumb('opensocialconnect.mange_social_providers', $this->url()->makeUrl('admincp.opensocialconnect.providers'))
            ->setHeader('cache', array(
                'drag.js' => 'static_script',
                '<script type="text/javascript">$Behavior.fsconnectAdmincpProviders = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'opensocialconnect.ordering\'}); }</script>'
            ))
            ->assign(array(
                'aOpenProviders' => $aOpenProviders,
                'sCoreUrl' => Phpfox::getParam('core.path'),
            ));
    }

    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('rss.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }
}
