<?php

namespace Apps\P_AdvMarketplace\Controller;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

class UpController extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iEditId = $this->request()->get('id');
        Phpfox::getService('advancedmarketplace.process')->uploadImages($iEditId, null);
        $oTemplate = Phpfox::getLib('template');
        $oTemplate
            ->assign(array())
            ->setHeader('cache', array(
                    'pager.css' => 'style_css',
                    'country.js' => 'module_core',
                    'browse.css' => 'module_advancedmarketplace',
                    'feed.js' => 'module_feed'
                )
            );
        $oTemplate->getTemplate("advancedmarketplace.block.uploadjscontrol");
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_controller_up_process_end')) ? eval($sPlugin) : false);
        exit;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_controller_up_clean')) ? eval($sPlugin) : false);
    }
}
