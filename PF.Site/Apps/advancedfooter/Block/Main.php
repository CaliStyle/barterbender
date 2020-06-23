<?php
/**
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_AdvancedFooter
 */
namespace Apps\Advancedfooter\Block;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;

class Main extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $footerPath = str_replace("index.php","PF.Site",Phpfox::getParam('core.path')) . "Apps/advancedfooter/assets/images/";
        $aFooterMenus = Phpfox::getService('advancedfooter.menu')->getForAdmin(0,1,1);
        $aSocialIcons = Phpfox::getService('advancedfooter.social')->getForAdmin(true);
        $aUsers = Phpfox::getService('advancedfooter.data')->getUsers(15);

        $this->template()->assign([
            'footerPath' => $footerPath,
            'aFooterMenus' => $aFooterMenus,
            'aFooterUsers' => $aUsers,
            'aSocialIcons' => $aSocialIcons
        ]);
    }
}