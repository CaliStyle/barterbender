<?php

/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Opensocialconnect_Component_Block_ViewMore extends Phpfox_Component
{
    public function process()
    {
        $iLimit = 7;
        $iLimitSelected = 40;
        $aOpenProviders = Phpfox::getService('opensocialconnect')->getEnabledProviders($iLimit, $iLimitSelected);
        $this->template()->assign(array(
            'aOpenProviders' => $aOpenProviders,
            'sCoreUrl' => Phpfox::getParam('core.path'),
        ));

        return 'block';
    }

    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('Opensocialconnect.component_block_viewmore_clean')) ? eval($sPlugin) : false);
    }

}

?>