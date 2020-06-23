<?php

namespace Apps\P_AdvMarketplace\Controller;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

class TokenController extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $sToken = Phpfox::getService('log.session')->getToken();
        echo $sToken;
        // echo $sPermalink = Phpfox::getLib('url')->makeUrl('advancedmarketplace.token');
        exit;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_controller_view_clean')) ? eval($sPlugin) : false);
    }
}
