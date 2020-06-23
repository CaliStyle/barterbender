<?php

defined('PHPFOX') or exit('NO DICE!');

class Ynsocialstore_Component_Block_Custom_View extends Phpfox_Component {

    public function process()
    {
        $iProductId = $this->getParam('product_id');
        $aMainCategory = Phpfox::getService('ynsocialstore.product')->getProductMainCategory($iProductId);
        $aCustomFields = Phpfox::getService('ynsocialstore.product')->getCustomFieldByCategoryId($aMainCategory);
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
            'aCustomFields' => $aCustomFields,
        ]);
    }
}

?>
