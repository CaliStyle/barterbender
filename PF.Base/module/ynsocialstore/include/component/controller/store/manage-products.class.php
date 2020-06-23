<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/19/16
 * Time: 09:17
 */
class Ynsocialstore_Component_Controller_Store_Manage_Products extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        $sError = "";
        if ($iEditId = $this->request()->getInt('id')) {
            $this->setParam('iStoreId',$iEditId);
        }
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
            if ($aDeleteIds = $this->request()->getArray('product_id'))
            {
                if (Phpfox::getService('ynsocialstore.product.process')->deleteMultiple($aDeleteIds,$iEditId))
                {
                    $this->url()->send('ynsocialstore.store.manage-products',['id'=>$iEditId], _p('products_successfully_deleted'));
                }
            }
            $aCategories = Phpfox::getService('ynsocialstore')->getAllCategories();
            $aPackageStore = Phpfox::getService('ynsocialstore.package')->getById($aEditStore['package_id']);
            $aConds[] = ' AND st.store_id =' . $iEditId;
            $iPage = $this->request()->getInt('page');
            $iPageSize = 10;

            // Search Filter
            $oSearch = Phpfox::getLib('search')->set(array(
                                                         'type' => 'request',
                                                         'search' => 'search',
                                                     ));
            list($iCount, $aProducts) = Phpfox::getService('ynsocialstore.product')->getManageProducts($aConds, $iPage, $iPageSize);
            // Set page id
            PhpFox::getLib('pager')->set(array(
                                             'page' => $iPage,
                                             'size' => $iPageSize,
                                             'count' => $iCount,
                                             'ajax' => 'ynsocialstore.changePageManageProducts',
                                             'popup' => true,
                                         ));
        }
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
        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();

    }
}