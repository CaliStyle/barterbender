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

class IndexController extends Phpfox_Component
{
    public function process()
    {
        $bSubCategory = false;
        if (($iId = $this->request()->getInt('sub'))) {
            $bSubCategory = true;
            $aParentCategory = Phpfox::getService('advancedfooter.menu')->getCategory($iId);
            if ($aParentCategory) {
                $this->template()->assign('sParentCategory', _p($aParentCategory['name']));
            }
        }

        $this->template()->setTitle(($bSubCategory ? _p('Manage sub menus') : _p('Manage Menus')))
            ->setBreadCrumb(($bSubCategory ? _p('Manage sub menus') : _p('Manage Menus')))
            ->assign([
                    'bSubCategory' => $bSubCategory,
                    'aCategories' => Phpfox::getService('advancedfooter.menu')->getForAdmin($iId)
                ]
            );
    }
}
