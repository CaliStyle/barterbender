<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/25/16
 * Time: 9:33 AM
 */
class Ynsocialstore_Component_Controller_Admincp_ManageProduct extends Phpfox_Component
{
    public function process()
    {
        if ($aDeleteIds = $this->request()->getArray('id'))
        {
            if (Phpfox::getService('ynsocialstore.product.process')->deleteMultiple($aDeleteIds))
            {
                $this->url()->send('admincp.ynsocialstore.manageproduct', null, _p('products_successfully_deleted'));
            }
        }

        // Page Number & Limit Per Page
        $iPage = $this->request()->getInt('page');
        $iPageSize = 10;

        $aVals = array();
        $aConds = array();

        // Search Filter
        $oSearch = Phpfox::getLib('search')->set(array(
            'type' => 'request',
            'search' => 'search',
        ));

        $aVals['title'] = $oSearch->get('title');
        $aVals['category_id'] = $oSearch->get('category_id');
        $aVals['store_name'] = $oSearch->get('store_name');
        $aVals['status'] = $oSearch->get('status');
        $aVals['feature'] = $oSearch->get('feature');
        $aVals['sort_name'] = $oSearch->get('sort_name');
        $aVals['sort_store_name'] = $oSearch->get('sort_store_name');
        $aVals['sort_price'] = $oSearch->get('sort_price');
        $aVals['sort_creation_date'] = $oSearch->get('sort_creation_date');

        if (!empty($aVals['title'])) {
            $aConds[] = "AND ecp.name like '%{$aVals['title']}%'";
        }
        if ($aVals['category_id']) {
            $aConds[] = "AND ecc.category_id = {$aVals['category_id']}";;
        }
        if (!empty($aVals['status'])) {
            $aConds[] = "AND ecp.product_status = '".Phpfox::getService('ynsocialstore.helper')->getProductStatusKey($aVals['status'])."'" ;
        }
        if ($aVals['store_name'] && !empty($aVals['store_name'])) {
            $aConds[] = "AND st.name like '%{$aVals['store_name']}%'";
        }

        if ($aVals['feature'] && !empty($aVals['feature'])) {
            switch ($aVals['feature']) {
                case 'featured':
                    $aConds[] = "AND ( (ecp.feature_start_time <=". PHPFOX_TIME ." AND ecp.feature_end_time >= " . PHPFOX_TIME . ") OR ecp.feature_end_time = 1 )";
                    break;
                case 'not_featured':
                    $aConds[] = "AND (ecp.feature_start_time  = 0 OR ecp.feature_end_time = 0)";
                    break;
            }
        }

        $sSortBy = null;

        if ($aVals['sort_name'] && !empty($aVals['sort_name'])) {
            switch ($aVals['sort_name']) {
                case 'asc':
                    $sSortBy = "ecp.name ASC";
                    break;
                case 'desc':
                    $sSortBy = "ecp.name DESC";
                    break;
            }
        }

        if ($aVals['sort_store_name'] && !empty($aVals['sort_store_name'])) {
            switch ($aVals['sort_store_name']) {
                case 'asc':
                    $sSortBy = "st.name ASC";
                    break;
                case 'desc':
                    $sSortBy = "st.name DESC";
                    break;
            }
        }

        if ($aVals['sort_price'] && !empty($aVals['sort_price'])) {
            switch ($aVals['sort_price']) {
                case 'asc':
                    $sSortBy = "ecp.product_price ASC";
                    break;
                case 'desc':
                    $sSortBy = "ecp.product_price DESC";
                    break;
            }
        }

        if ($aVals['sort_creation_date'] && !empty($aVals['sort_creation_date'])) {
            switch ($aVals['sort_creation_date']) {
                case 'asc':
                    $sSortBy = "ecp.product_creation_datetime ASC";
                    break;
                case 'desc':
                    $sSortBy = "ecp.product_creation_datetime DESC";
                    break;
            }
        }
//        die($sSortBy);
        list($iCount, $aProducts) = Phpfox::getService('ynsocialstore.product')->getManageProducts($aConds, $iPage, $iPageSize, $sSortBy);

        // Set page id
        PhpFox::getLib('pager')->set(array(
            'page' => $iPage,
            'size' => $iPageSize,
            'count' => $iCount,
        ));

        $this->template()->setTitle(_p('manage_products'));

        $aCategories = Phpfox::getService('ynsocialstore')->getAllCategories();

        $this->template()
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_ynsocialstore'), $this->url()->makeUrl('admincp.app').'?id=__module_ynsocialstore')
            ->setBreadcrumb(_p('manage_products'), $this->url()->makeUrl('admincp.ynsocialstore.managestore'));
        $this->template()->assign(array(
            'aList' => $aProducts,
            'aCategories' => $aCategories,
            'aForms' => $aVals,
            'iCount' => $iCount,
        ));

        $this->template()->setHeader(array(
            'manageproducts.js' => 'module_ynsocialstore',
        ));
        $this->template()->setPhrase(array(
            'ynsocialstore.are_you_sure',
            'ynsocialstore.yes',
            'ynsocialstore.no',
            'ynsocialstore.are_you_sure_want_to_delete_this_product_this_action_cannot_be_reverted',
            'ynsocialstore.are_you_sure_want_to_delete_these_products_this_action_cannot_be_reverted'
        ));
    }
}