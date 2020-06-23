<?php

/**
 * [PHPFOX_HEADER]
 */
namespace Apps\YouNet_UltimateVideos\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class CategoryController extends Phpfox_Component
{
    /**
     * Class process method which is used to execute this component.
     */
    public function process()
    {

        $bSubCategory = false;

        if (($iId = $this->getParam('sub'))) {
            $bSubCategory = true;
        }

        if ($iDelete = $this->request()->getInt('delete')) {
            Phpfox::getService('ultimatevideo.category.process')->delete($iDelete);
        }

        $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/YouNet_UltimateVideos';
        $aCategories = ($bSubCategory ? Phpfox::getService('ultimatevideo.category')->getForAdmin($iId) : Phpfox::getService('ultimatevideo.category')->getForAdmin());

        $this->template()->setTitle(($bSubCategory ? _p('ultimate_manage_sub_categories') : _p('ultimate_manage_categories')))
            ->setBreadCrumb(($bSubCategory ? _p('ultimate_manage_sub_categories') : _p('ultimate_manage_categories')))
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
