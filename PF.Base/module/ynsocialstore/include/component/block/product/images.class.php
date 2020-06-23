<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/26/16
 * Time: 10:56 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Product_Images extends Phpfox_Component
{
    public function process()
    {
        if (null === ($iProductId = $this->request()->getInt('req3'))) {
            return false;
        }

        $aProduct = $this->getParam('aProduct');

        if (empty($aProduct)) {
            return false;
        }

        $this->template()
            ->assign(array(
                'aItem' => $aProduct['images'],
                'sStatus' => $aProduct['product_status_display'],
                'iProductId' => $aProduct['product_id'],
                'sHeader' => '',
            )
        );

        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();

        return 'block';
    }
}