<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/19/16
 * Time: 15:40
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Compare extends Phpfox_Component
{
    public function process()
    {
        $sStoreIdList = Phpfox::getCookie('ynsocialstore_compare_store_name');
        $sProductIdList = Phpfox::getCookie('ynsocialstore_compare_product_name');
        $boxSize = Phpfox::getCookie('ynsocialstore_compare_store_box');
        $tabSelected = Phpfox::getCookie('ynsocialstore_compare_tab');
        $iTotalCompare = 0;
        $iTotalProduct = 0;
        $aStoreToCompare = $aStoreIdList = $aCompareProductList = $aProductList = [];
        if(!empty($sStoreIdList)) {
            $aStoreToCompare = Phpfox::getService('ynsocialstore')->getStoresToCompare($sStoreIdList);
            $iTotalCompare = $iTotalCompare + count($aStoreToCompare);
        }
        if(!empty($sProductIdList)){
            $aCompareProductList = Phpfox::getService('ynsocialstore.product')->getAllCategoryForCompare(trim($sProductIdList));
            foreach ($aCompareProductList as $key => $aCompare) {
                $aCompareProductList[$key]['products'] = Phpfox::getService('ynsocialstore.product')->getProductToCompare($aCompare['category_id'],trim($sProductIdList));
                $aCompareProductList[$key]['compare_link'] = $this->url()->makeUrl('ynsocialstore.product.compare',['id' => $aCompare['category_id']]);
                $aCompareProductList[$key]['total'] = count($aCompareProductList[$key]['products']);
                $iTotalCompare = $iTotalCompare + count($aCompareProductList[$key]['products']);
                $iTotalProduct += count($aCompareProductList[$key]['products']);
            }
        }
        $this->template()->assign([
                            'compareStoreLink' =>Phpfox::permalink('ynsocialstore.store.compare', null, null),
                            'sCorePath' => Phpfox::getParam('core.path_actual').'PF.Base/',
                            'iTotalCompare' => $iTotalCompare,
                            'iTotalStore' => count($aStoreToCompare),
                            'iTotalProduct' => $iTotalProduct,
                            'aCompareProductList' => $aCompareProductList,
                            'aStores' => $aStoreToCompare,
                            'boxSize' => (!empty($boxSize)) ? $boxSize : 'min',
                            'tabSelected' => (!empty($tabSelected)) ? $tabSelected : 'store'
                                  ]);
    }

    function __call($name, $arguments)
    {

    }

}