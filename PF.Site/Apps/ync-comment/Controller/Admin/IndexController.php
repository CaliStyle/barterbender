<?php
namespace Apps\YNC_Comment\Controller\Admin;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox_Component;
use Phpfox_Plugin;


class IndexController extends Phpfox_Component
{
    public function process()
    {
        $this->url()->send('admincp.app',['id' => 'YNC_Comment']);
        return true;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynccomment.component_controller_admincp_indexclean')) ? eval($sPlugin) : false);
    }
}