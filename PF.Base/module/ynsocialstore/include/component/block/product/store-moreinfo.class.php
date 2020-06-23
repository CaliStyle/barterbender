<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/26/16
 * Time: 8:53 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Product_Store_MoreInfo extends Phpfox_Component
{
    public function process()
    {
        if (null === ($iProductId = $this->request()->getInt('req3'))) {
            return false;
        }

        $aProduct = $this->getParam('aProduct');

        if (!count($aProduct)) {
            return false;
        }

        $this->template()->assign(array(
                'aItem' => $aProduct,
                'sHeader' => _p('Shipping and Payment info')
            )
        );

        return 'block';
    }
}