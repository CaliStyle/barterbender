<?php

/**
 * [PHPFOX_HEADER]
 */
namespace Apps\YouNet_UltimateVideos\Controller\Admin\Category;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class IndexController extends \Phpfox_Component
{
    /**
     * Class process method which is used to execute this component.
     */
    public function process()
    {

        $bSubCategory = false;

        if (($iId = $this->getParam('sub')))
        {
            $bSubCategory = true;
        }

        if ($iDelete = $this->request()->getInt('delete'))
        {

            if (Phpfox::getService('ultimatevideo.category.process')->delete($iDelete))
            {
                //$this->url()->send('admincp.ultimatevideo', null, _p('category_successfully_deleted'));
            }

        }

        $corePath = Phpfox::getParam('core.path_actual').'PF.Site/Apps/YouNet_UltimateVideos';
        $aCategories = ($bSubCategory ? Phpfox::getService('ultimatevideo.category')->getForAdmin($iId) : Phpfox::getService('ultimatevideo.category')->getForAdmin());

        $this->template()->setTitle(($bSubCategory ?  _p('manage_sub_categories') : _p('manage_categories')))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(($bSubCategory ?  _p('manage_sub_categories') : _p('manage_categories')))
//            ->setHeader(array(
//                    'drag.js' => 'static_script',
//                    '<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'ultimatevideo.categoryOrdering\'}); }</script>'
//                )
//            )
            ->setPhrase(
                array(
                    'custom_group',
                    'are_you_sure',
                    'yes',
                    'no',
                ))
            ->assign(array(
                    'bSubCategory' => $bSubCategory,
                    'aCategories' => $aCategories,
                    'corePath' => $corePath,
                    'sUrl' => $this->url()->makeUrl('admincp.ultimatevideo.category')
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('coupon.component_controller_admincp_category_index_clean')) ? eval($sPlugin) : false);
    }

}

?>
