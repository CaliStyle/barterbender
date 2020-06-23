<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/31/16
 * Time: 12:12 PM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Product_Feature_Inbox extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process() {
        $iProductId = $this->getParam('iProductId');
        $aEditedProduct = Phpfox::getService('ynsocialstore.product')->getProductForEdit($iProductId);
        $aStore = Phpfox::getService('ynsocialstore')->getStoreById($aEditedProduct['item_id']);
        $aPackage = Phpfox::getService('ynsocialstore.package')->getById($aStore['package_id']);
        /*get featured or not*/
        $aEditedProduct['expired_date'] = '';
        if(1 == $aEditedProduct['feature_end_time'])
        {
            $aEditedProduct['is_unlimited'] = 1;
            $aEditedProduct['expired_date'] = '';
        } else if($aEditedProduct['feature_end_time'] >= PHPFOX_TIME)
        {
            $aEditedProduct['is_unlimited'] = 0;
            $aEditedProduct['expired_date'] = date(Phpfox::getParam('core.global_update_time'),$aEditedProduct['feature_end_time']);
        }

        if ($aEditedProduct['feature_start_time'] <= PHPFOX_TIME && $aEditedProduct['feature_end_time'] >= PHPFOX_TIME) {
            $aEditedProduct['is_featured'] = 1;
        } else {
            $aEditedProduct['is_featured'] = 0;
        }

        $this->template()->assign(array(
                'iProductId' => $iProductId,
                'sFormUrl' => $this->url()->makeUrl('ynsocialstore.add') .'id_'.$iProductId,
                'aEditedProduct' => $aEditedProduct,
                'iDefaultFeatureFee' => $aPackage['feature_product_fee'],
                'aCurrentCurrencies' => Phpfox::getService('ynsocialstore.helper')->getCurrentCurrencies(),
            )
        );
        return 'block';
    }
}