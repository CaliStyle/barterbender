<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/4/16
 * Time: 9:05 AM
 */
class Ynsocialstore_Service_Package_Process extends Phpfox_Service
{
    public function add($aVals)
    {
        $oParseInput = Phpfox::getLib('parse.input');

        if(!Phpfox::getService('ynsocialstore.helper')->isNumeric($aVals['expire_number'])){
            return Phpfox_Error::set(_p('valid_period_have_to_be_a_number_and_at_least_0'));
        }
        if(!Phpfox::getService('ynsocialstore.helper')->isNumeric($aVals['fee'])){
            return Phpfox_Error::set(_p('package_fee_have_to_be_a_number_and_at_least_0'));
        }
        if(isset($aVals['themes']) == false || count($aVals['themes']) <= 0){
            return Phpfox_Error::set(_p('select_at_least_one_theme'));
        }
        if(!Phpfox::getService('ynsocialstore.helper')->isNumeric($aVals['feature_store_fee'])){
            return Phpfox_Error::set(_p('fee_for_feature_store_have_to_be_a_number_and_at_least_0'));
        }
        if(!Phpfox::getService('ynsocialstore.helper')->isNumeric($aVals['feature_product_fee'])){
            return Phpfox_Error::set(_p('feature_products_fee_have_to_be_a_number_and_at_least_0'));
        }

        $iId = $this->database()->insert(Phpfox::getT("ynstore_store_package"), array(
                'name' => $oParseInput->clean($aVals['name'], 255),
                'expire_number' => $aVals['expire_number'],
                'fee' => $aVals['fee'],
                'themes' => json_encode($aVals['themes']),
                'max_products' => $aVals['max_products'],
                'feature_store_fee' => $aVals['feature_store_fee'],
                'feature_product_fee' => $aVals['feature_product_fee'],
                'theme_editable' => $aVals['theme_editable'],
                'enable_attribute' => $aVals['enable_attribute'],
                'max_photo_per_product' => $aVals['max_photo_per_product'],
            )
        );

        return $iId;
    }

    public function update($iId, $aVals)
    {
        $oParseInput = Phpfox::getLib('parse.input');

        if(!Phpfox::getService('ynsocialstore.helper')->isNumeric($aVals['expire_number'])){
            return Phpfox_Error::set(_p('valid_period_have_to_be_a_number_and_at_least_0'));
        }
        if(!Phpfox::getService('ynsocialstore.helper')->isNumeric($aVals['fee'])){
            return Phpfox_Error::set(_p('package_fee_have_to_be_a_number_and_at_least_0'));
        }
        if(isset($aVals['themes']) == false || count($aVals['themes']) <= 0){
            return Phpfox_Error::set(_p('select_at_least_one_theme'));
        }
        if(!Phpfox::getService('ynsocialstore.helper')->isNumeric($aVals['feature_store_fee'])){
            return Phpfox_Error::set(_p('fee_for_feature_store_have_to_be_a_number_and_at_least_0'));
        }
        if(!Phpfox::getService('ynsocialstore.helper')->isNumeric($aVals['feature_product_fee'])){
            return Phpfox_Error::set(_p('feature_products_fee_have_to_be_a_number_and_at_least_0'));
        }


        $this->database()->update(Phpfox::getT("ynstore_store_package"), array(
            'name' => $oParseInput->clean($aVals['name'], 255),
            'expire_number' => $aVals['expire_number'],
            'fee' => $aVals['fee'],
            'themes' => json_encode($aVals['themes']),
            'max_products' => $aVals['max_products'],
            'feature_store_fee' => $aVals['feature_store_fee'],
            'feature_product_fee' => $aVals['feature_product_fee'],
            'theme_editable' => $aVals['theme_editable'],
            'enable_attribute' => $aVals['enable_attribute'],
            'max_photo_per_product' => $aVals['max_photo_per_product'],
        ), 'package_id = ' . (int) $iId);

        return true;
    }

    public function activepackage($iId, $bActive)
    {
        return $this->database()->update(Phpfox::getT('ynstore_store_package'), array(
            'active' => $bActive,
        ), 'package_id ='. $iId);
    }

    public function delete($iId)
    {
        return $this->database()->delete(Phpfox::getT('ynstore_store_package'), 'package_id ='. $iId, 1);
    }
}