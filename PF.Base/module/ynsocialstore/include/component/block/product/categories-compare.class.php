<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 11/10/16
 * Time: 15:01
 */
class Ynsocialstore_Component_Block_Product_Categories_Compare extends Phpfox_Component
{

    public function process()
    {
        $aCompareCategory = [];
        $iCategoryId = $this->request()->get('id', '');
        if(!$iCategoryId)
        {
            $iCategoryId = $this->getParam('id','');
        }
        $sProductList = Phpfox::getCookie('ynsocialstore_compare_product_name');
        $bIsHaveCurrent = false;
        if (!empty($sProductList)) {
            $aCompareCategory = Phpfox::getService('ynsocialstore.product')->getAllCategoryForCompare(trim($sProductList));

            foreach ($aCompareCategory as $key => $aCompare) {
                if($aCompare['category_id'] == $iCategoryId){
                    $bIsHaveCurrent = true;
                }
                $aCompareCategory[$key]['products'] = Phpfox::getService('ynsocialstore.product')->getProductToCompare($aCompare['category_id'],trim($sProductList));
                $aCompareCategory[$key]['compare_link'] = $this->url()->makeUrl('ynsocialstore.product.compare',['id' => $aCompare['category_id']]);
                $aCompareCategory[$key]['total'] = count($aCompareCategory[$key]['products']);
            }

        }

        $this->template()->assign([
                          'sCorePath' => Phpfox::getParam('core.path_file'),
                          'aCategories' => $aCompareCategory,
                          'iCategoryId' => $iCategoryId,
                          'bIsHaveCurrent' => $bIsHaveCurrent,
            ]);
    }
}