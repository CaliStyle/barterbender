<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/19/16
 * Time: 17:39
 */
class Ynsocialstore_Component_Controller_Store_Compare extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
        $sStoreList = Phpfox::getCookie('ynsocialstore_compare_store_name');
        $aStoreList = [];
        if (!empty($sStoreList)) {
            $aStoreList = Phpfox::getService('ynsocialstore')->getStoreDetailToCompare($sStoreList);
        }
        Phpfox::getService('ynsocialstore.helper')->buildMenu();
        $compareField = Phpfox::getService('ynsocialstore')->getFieldsComparison('store');
        $aFields = Phpfox::getService('ynsocialstore')->doComparisonField($compareField);
        $this->template()->setBreadcrumb(_p('social_store'),
            $this->url()->makeUrl('ynsocialstore.store'))->setBreadCrumb(_p('compare_stores'),
                '')->setTitle(_p('compare_stores'));
        $this->template()->assign([
            'aStoreList' => $aStoreList,
            'aFieldStatus' => $aFields,
            'sCorePath' => Phpfox::getParam('core.path_actual') . 'PF.Base/'
        ])->keepBody(true);
    }
}
