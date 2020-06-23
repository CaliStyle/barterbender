<?php
namespace Apps\P_AdvMarketplace\Block;

use Phpfox;
use Phpfox_Component;

defined('PHPFOX') or exit('NO DICE!');

class SponsorHelp extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {

    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_block_sponsorhelp_clean')) ? eval($sPlugin) : false);
    }
}
