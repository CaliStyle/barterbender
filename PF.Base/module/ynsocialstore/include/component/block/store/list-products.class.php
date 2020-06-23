<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/19/16
 * Time: 09:17
 */
class Ynsocialstore_Component_Block_Store_List_Products extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        $sError = "";
        $iEditId= $this->getParam('iStoreId');
        $aEditStore = Phpfox::getService('ynsocialstore')->getStoreById($iEditId);
        $aPackageStore = $aVals = $aConds = $aCategories = $aProducts = [];
        $iCount = 0;
        if(!$aEditStore)
        {
            $sError = _p('unable_to_find_the_store_you_are_looking_for');
        }
        if(!Phpfox::getService('ynsocialstore.permission')->canEditStore(false,$aEditStore['user_id']))
        {
            $sError = _p('you_do_not_have_permission_to_edit_this_store');
        }

        // Search Filter
        $oSearch = Phpfox::getLib('search')->set(array(
                                     'type' => 'request',
                                     'search' => 'search',
                                                 ));
        if(empty($sError)) {
            $aCategories = Phpfox::getService('ynsocialstore')->getAllCategories();
            $aPackageStore = Phpfox::getService('ynsocialstore.package')->getById($aEditStore['package_id']);
            $aConds[] = ' AND st.store_id =' . $iEditId;
            $iPage = $this->request()->getInt('page') ? $this->request()->getInt('page') : $this->getParam('page',0);
            $iPageSize = 10;

            // Search Filter
            $oSearch = Phpfox::getLib('search')->set(array(
                                                         'type' => 'request',
                                                         'search' => 'search',
                                                     ));

            // Set page id

            if(!$aSearch = $this->request()->getArray('search')) {
                $aSearch = $this->getParam('search');
            }
            if($aSearch){
                $aVals['title'] 		= $aSearch['title'];
                $aVals['category_id']  	= $aSearch['category_id'];
                $aVals['status']  		= $aSearch['status'];
            }
            else {
                $aVals = array(
                    'title' => '',
                    'category_id' => '',
                    'status' => '',
                );
            }
            if(!empty($aVals['title']))
            {
                $aConds[] = "AND ecp.name like '%{$aVals['title']}%'";
            }
            if(!empty($aVals['category_id']) && $aVals['category_id'] != 0)
            {
                $aConds[] = "AND eccd.is_main = 1 AND eccd.category_id = {$aVals['category_id']}";
            }
            if(!empty($aVals['status']))
            {
                $aConds[] = "AND ecp.product_status like '{$aVals['status']}'";
            }
            list($iCount, $aProducts) = Phpfox::getService('ynsocialstore.product')->getManageProducts($aConds, $iPage, $iPageSize);

            PhpFox::getLib('pager')->set(array(
                                             'page' => $iPage,
                                             'size' => $iPageSize,
                                             'count' => $iCount,
                                             'ajax' => 'ynsocialstore.changePageManageProducts',
                                             'popup'	=> true,
                                         ));
        }
        $this->template()
            ->setTitle(_p('manage_products'))
            ->setBreadcrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'))
            ->setBreadcrumb($aEditStore['name'], $this->url()->makeUrl('ynsocialstore.store',$aEditStore['store_id']))
            ->setBreadcrumb(_p('manage_products'), $this->url()->makeUrl('ynsocialstore.store.manage-productss','id_'.$aEditStore['store_id']),true)
            ->assign([
                         'sError' => $sError,
                         'iStoreId' => $iEditId,
                         'aCategories' => $aCategories,
                         'aStore' => $aEditStore,
                         'aPackageStore' => $aPackageStore,
                         'aProducts' => $aProducts,
                         'iCount' => $iCount,
                         'aForms' => $aVals,
                     ]);
        //Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();

    }
}