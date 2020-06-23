<?php
/**
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_AdvancedFooter
 */
namespace Apps\Advancedfooter\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class SocialController extends Phpfox_Component
{
    public function process()
    {
        $this->template()->setTitle(_p('Manage Social Icons'))
            ->setBreadCrumb(_p('Manage Social Icons'))
            ->assign([
                    'aCategories' => Phpfox::getService('advancedfooter.social')->getForAdmin()
                ]
            );
    }
}
