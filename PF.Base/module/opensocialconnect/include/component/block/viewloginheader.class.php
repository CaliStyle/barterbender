<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Opensocialconnect_Component_Block_ViewloginHeader extends Phpfox_Component
{
    public function process()
    {
        if (Phpfox::getLib('module')->getFullControllerName() != 'user.login') {
            $iLimit = Phpfox::getParam('opensocialconnect.limit_providers_view_on_login_header');
            $iLimitSelected = Phpfox::getParam('opensocialconnect.limit_providers_view_on_login_header');
        } else {
            $iLimit = 8;
            $iLimitSelected = 8;
        }

        $aOpenProviders = Phpfox::getService('opensocialconnect')->getEnabledProviders($iLimit, $iLimitSelected);
        $iIconSize = (intval(Phpfox::getParam('opensocialconnect.size_of_icon_in_pixel')) >= 0) ? intval(Phpfox::getParam('opensocialconnect.size_of_icon_in_pixel')) : 24;
        $iMarginTop = intval(Phpfox::getParam('opensocialconnect.margin_top'));
        $iMarginLeft = intval(Phpfox::getParam('opensocialconnect.margin_left'));
        $iWidth = (count($aOpenProviders) + 1) * ($iIconSize + 6);

        $this->template()->assign(array(
            'aOpenProviders' => $aOpenProviders,
            'iLimitView' => $iLimit,
            'iLimitSelected' => $iLimitSelected,
            'sCoreUrl' => Phpfox::getParam('core.path'),
            'iIconSize' => $iIconSize,
            'iMarginTop' => $iMarginTop,
            'iMarginLeft' => $iMarginLeft,
            'iWidth' => $iWidth
        ));

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('opensocialconnect.component_block_ViewloginHeader_clean')) ? eval($sPlugin) : false);
    }

}
