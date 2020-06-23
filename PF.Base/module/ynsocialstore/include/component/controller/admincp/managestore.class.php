<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/5/16
 * Time: 9:21 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Controller_Admincp_Managestore extends Phpfox_Component
{
    /**
     * Class process method which is used to execute this component.
     */
    public function process()
    {
        if ($aDeleteIds = $this->request()->getArray('id'))
        {
            if (Phpfox::getService('ynsocialstore.process')->deleteMultiple($aDeleteIds))
            {
                $this->url()->send('admincp.ynsocialstore.managestore', null, _p('stores_successfully_deleted'));
            }
        }

        // Page Number & Limit Per Page
        $iPage = $this->search()->getPage();
        $iPageSize = 10;

        $aVals = array();
        $aConds = array();

        // Search Filter
        $oSearch = Phpfox::getLib('search')->set(array(
            'type' => 'request',
            'search' => 'search',
        ));
//        die(d($oSearch->get()));
        $aVals['title'] = $oSearch->get('title');
        $aVals['category_id'] = $oSearch->get('category_id');
        $aVals['status'] = $oSearch->get('status');
        $aVals['owner'] = $oSearch->get('owner');
        $aVals['feature'] = $oSearch->get('feature');
        $aVals['package_id'] = $oSearch->get('package_id');
        $aVals['sort_name'] = $oSearch->get('sort_name');
        $aVals['sort_owner'] = $oSearch->get('sort_owner');

        if (!empty($aVals['title'])) {
            $aConds[] = "AND ynst.name like '%{$aVals['title']}%'";
        }
        if ($aVals['category_id']) {
            $aConds[] = "AND ec.category_id = {$aVals['category_id']}";;
        }
        if (!empty($aVals['status'])) {
             $aConds[] = "AND ynst.status = '".$aVals['status']."'" ;
        }
        if ($aVals['owner'] && !empty($aVals['owner'])) {
            $aConds[] = "AND u.full_name like '%{$aVals['owner']}%'";
        }

        if ($aVals['feature'] && !empty($aVals['feature'])) {
            switch ($aVals['feature']) {
                case 'featured':
                    $aConds[] = "AND ynst.is_featured = 1 ";
                    break;
                case 'not_featured':
                    $aConds[] = "AND  ynst.is_featured  = 0  ";
                    break;
            }
        }

        $sSortBy = null;

        if ($aVals['sort_name'] && !empty($aVals['sort_name'])) {
            switch ($aVals['sort_name']) {
                case 'name_asc':
                    $sSortBy = "ynst.name ASC";
                    break;
                case 'name_desc':
                    $sSortBy = "ynst.name DESC";
                    break;
            }
        }

        if ($aVals['sort_owner'] && !empty($aVals['sort_owner'])) {
            switch ($aVals['sort_owner']) {
                case 'name_asc':
                    $sSortBy = "u.full_name ASC";
                    break;
                case 'name_desc':
                    $sSortBy = "u.full_name DESC";
                    break;
            }
        }
//        d($sSortBy);die;
        if ($aVals['package_id'] && $aVals['package_id'] != 0) {
            $aConds[] = "AND ystpk.package_id = {$aVals['package_id']}";
        }

        list($iCount, $aStores) = Phpfox::getService('ynsocialstore')->getManageStores($aConds, $iPage, $iPageSize, null, $sSortBy);

        // Set page id
        PhpFox::getLib('pager')->set(array(
            'page' => $iPage,
            'size' => $iPageSize,
            'count' => $iCount,
        ));

        $this->template()->setTitle(_p('manage_stores'));

        $aCategories = Phpfox::getService('ynsocialstore')->getAllCategories();

        $aPackages = Phpfox::getService('ynsocialstore.package')->getPackages();

        $this->template()
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_ynsocialstore'), $this->url()->makeUrl('admincp.app').'?id=__module_ynsocialstore')
            ->setBreadcrumb(_p('manage_stores'), $this->url()->makeUrl('admincp.ynsocialstore.managestore'));
        $this->template()->assign(array(
            'aList' => $aStores,
            'aCategories' => $aCategories,
            'aForms' => $aVals,
            'iCount' => $iCount,
            'aPackages' => $aPackages,
        ));

        $this->template()->setHeader(array(
            'managestore.js' => 'module_ynsocialstore',
        ));
        $this->template()->setPhrase(array(
            'ynsocialstore.are_you_sure',
            'ynsocialstore.yes',
            'ynsocialstore.no',
            'ynsocialstore.confirm_feature_store_unlimited',
            'ynsocialstore.ynsocialstore_confirm_feature_store_limited',
            'ynsocialstore.are_you_sure_want_to_delete_this_store_this_action_cannot_be_reverted_and_all_products_in_store_will_be_lost',
            'ynsocialstore.are_you_sure_want_to_delete_these_stores_this_action_cannot_be_reverted_and_all_products_in_these_stores_will_be_lost'
        ));
    }
}