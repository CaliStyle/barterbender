<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_Manage_Business_Theme extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('directory.helper')->buildMenu();
        $iEditedBusinessId = 0;
        if ($this->request()->getInt('id')) {
            $iEditedBusinessId = $this->request()->getInt('id');
            $this->setParam('iBusinessId', $iEditedBusinessId);
        }

        if (!(int)$iEditedBusinessId) {
            $this->url()->send('directory');
        }
        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iEditedBusinessId);

        // check permission 
        if (!Phpfox::getService('directory.permission')->canEditBusiness($aBusiness['user_id'], $iEditedBusinessId) ||
            !Phpfox::getService('directory.permission')->canChangeBusinessTheme($iEditedBusinessId)
        ) {
            $this->url()->send('subscribe');
        }

        $aGlobalSetting = Phpfox::getService('directory')->getGlobalSetting();

        $aPackage = json_decode($aBusiness['package_data'], true);

        if (isset($aPackage['package_id']) == false || (int)$aPackage['active'] != 1) {
            $this->url()->send("directory.dashboard", array('id' => $iEditedBusinessId));
            // do nothing
        }

        if ($aVals = $this->request()->getArray('val')) {
            if (isset($aVals['apply_theme'])) {

                if (Phpfox::getService('directory.process')->updateThemeForBusiness($aVals)) {
                    $this->url()->send("directory.manage-business-theme", array('id' => $iEditedBusinessId), _p('directory.manage_themes_updated_successfully'));
                }
            }
        }

        $this->template()
            ->setEditor()
            ->setPhrase(array())
            ->setHeader('cache', array(
                'pager.css' => 'style_css',
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                'switch_legend.js' => 'static_script',
                'switch_menu.js' => 'static_script',
                'quick_edit.js' => 'static_script',
                'progress.js' => 'static_script',
                'share.js' => 'module_attachment',
                'country.js' => 'module_core',
            ));

        $this->template()->assign(array(
            'aPackage' => $aPackage,
            'aBusiness' => $aBusiness,
            'aGlobalSetting' => $aGlobalSetting,
            'iBusinessid' => $iEditedBusinessId,
            'core_path' => Phpfox::getParam('core.path_file'),
        ));
        $this->template()->setBreadcrumb(_p('directory.manage_business_theme'), $this->url()->permalink('directory.edit', 'id_' . $iEditedBusinessId));

        Phpfox::getService('directory.helper')->loadDirectoryJsCss();

    }

    public function clean()
    {

    }

}

?>