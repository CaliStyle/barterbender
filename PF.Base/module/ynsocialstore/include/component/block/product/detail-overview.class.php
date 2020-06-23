<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 11/1/16
 * Time: 09:18
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Product_Detail_Overview extends Phpfox_Component
{
    public function process()
    {
        $aProduct = $this->getParam('aProduct');
        $iProductId = $this->getParam('iProductId');
        $aMainCategory = Phpfox::getService('ynsocialstore.product')->getProductMainCategory($iProductId);
        $aCustomFields = Phpfox::getService('ynsocialstore.product')->getCustomFieldByCategoryId($aMainCategory['category_id']);

        $aCustomData = array();

        if ($iProductId)
        {
            $aCustomDataTemp = Phpfox::getService('ecommerce.custom')->getCustomFieldByProductId($iProductId,'ynsocialstore_product');

            if (count($aCustomFields))
            {
                foreach ($aCustomFields as $aField)
                {
                    foreach ($aCustomDataTemp as $aFieldValue)
                    {
                        if ($aField['field_id'] == $aFieldValue['field_id'])
                        {
                            $aCustomData[$aFieldValue['group_phrase_var_name']][] = $aFieldValue;
                        }
                    }
                }
            }
        }
        if (count($aCustomData))
        {
            $aCustomFields = $aCustomData;
        }

        $this->template()->assign([
                'aProduct' => $aProduct,
                'aCustomFields' => $aCustomFields,
               ]);
    }
}