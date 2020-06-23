<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/25/16
 * Time: 5:41 PM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Product_Store_Info extends Phpfox_Component
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

        $this->template()->assign(array(
                'aItem' => $aProduct,
                'sCorePath' => Phpfox::getParam('core.path'),
                'sHeader' => _p('Store Info')
            )
        );

        return 'block';
    }
}