<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/19/16
 * Time: 17:39
 */
class Ynsocialstore_Component_Controller_Product_Compare extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
        $sProductList = Phpfox::getCookie('ynsocialstore_compare_product_name');
        $iCategoryId = $this->request()->get('id', '');
        if ((int)$iCategoryId <= 0) {
            $this->url()->send('ynsocialstore');
            return false;
        }
        $aProductList = $aProductCompare = [];
        if (!empty($sProductList)) {
            $aProductCompare = Phpfox::getService('ynsocialstore.product')->getProductDetailToCompare($iCategoryId,
                $sProductList);

        }
        if (count($aProductCompare) < 2) {
            $this->url()->send('ynsocialstore', _p('need_at_least_2_products_to_compare'));
            return false;
        }
        $parent_category_id = Phpfox::getService('ynsocialstore.category')->getFirstParentId($iCategoryId);
        $aCustomFields = Phpfox::getService('ynsocialstore.product')->getCustomFieldByCategoryId($parent_category_id);
        $compareField = Phpfox::getService('ynsocialstore')->getFieldsComparison('product');
        $aFields = Phpfox::getService('ynsocialstore.product')->doComparisonField($compareField);
        $this->template()->setBreadcrumb(_p('social_store'),
            $this->url()->makeUrl('ynsocialstore'))->setBreadCrumb(_p('compare_products'),
                '')->setTitle(_p('compare_products'));
        $this->template()->assign([
            'aProductList' => $aProductList,
            'aFieldStatus' => $aFields,
            'sCorePath' => Phpfox::getParam('core.path_file'),
            'aProductCompare' => $aProductCompare,
            'iCategoryId' => $iCategoryId,
            'aCustomFields' => $aCustomFields
        ])->keepBody(true);

        Phpfox::getService('ynsocialstore.helper')->buildMenu();
    }
}

