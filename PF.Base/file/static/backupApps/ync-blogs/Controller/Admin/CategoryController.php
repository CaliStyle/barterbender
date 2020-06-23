<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 30/12/2016
 * Time: 18:31
 */

namespace Apps\YNC_Blogs\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class CategoryController extends Phpfox_Component
{
    public function process()
    {
        $bSubCategory = false;
        if (($iId = $this->getParam('sub'))) {
            $bSubCategory = true;
        }

        $this->template()->setTitle(($bSubCategory ? _p('manage_sub_categories') : _p('manage_categories')))
            ->setBreadCrumb(($bSubCategory ? _p('manage_sub_categories') : _p('manage_categories')))
            ->assign([
                    'bSubCategory' => $bSubCategory,
                    'aCategories' => Phpfox::getService('ynblog.category')->getForAdmin($iId),
                ]
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynblog.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }
}