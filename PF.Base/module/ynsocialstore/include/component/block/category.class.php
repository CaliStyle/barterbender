<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/6/16
 * Time: 9:05 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Category extends Phpfox_Component
{
    public function process()
    {
        $sType = $this->request()->get('req2', 'product');
        $sReq3 = $this->request()->get('req3');
        $sReq4 = $this->request()->getInt('req4');
        $bIsStoreDetail = false;
        $sUrl = 'ynsocialstore.category';

        if ($sType != 'store') $sType = 'product';

        $sFullControllerName = Phpfox::getLib('template')->getVar('sFullControllerName');

        if (isset($sFullControllerName) && in_array($sFullControllerName, array('ynsocialstore_store_detail', 'ynsocialstore_product_detail'))) {
            $aItem = $this->getParam('aStore', $this->getParam('aProduct'));

            if (empty($aItem))
                return false;

            $sType = 'product';

            $bIsStoreDetail = true;
            $aCategories = Phpfox::getService('ynsocialstore.category')->getCategoriesHaveProduct(0, 1, $aItem['store_id']);
        } else {
            $sUrl = ($sType == 'product') ? 'ynsocialstore.category' : 'ynsocialstore.store.category';
            $aCategories = Phpfox::getService('ecommerce.category')->getAllCategories();
        }

        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        if (!count($aCategories))
        {
            return false;
        }
        $iCurrentCategoryId = 0;

        if (isset($sReq3) && isset($sReq4) && $sReq3 == 'category' && $sReq4 > 0) {
            $iCurrentCategoryId = $sReq4;
        }

        $this->template()->assign(array(
                'aCategories' => $aCategories,
                'sUrl' => $sUrl,
                'iCurrentCategoryId' => $iCurrentCategoryId,
                'sHeader' => _p('categories'),
                'sType' => $sType,
                'bIsStoreDetail' => $bIsStoreDetail,
                'iStoreId' => isset($aItem['store_id']) ? $aItem['store_id'] : 0,
                'sStoreName' => isset($aItem['name']) ? $aItem['name'] : '',
            )
        );

        return 'block';
    }
}