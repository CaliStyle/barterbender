<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/26/16
 * Time: 9:54 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Product_Statistic extends Phpfox_Component
{
    public function process()
    {
        if (null === ($iProductId = $this->request()->getInt('req3'))) {
            return false;
        }

        $aProduct = $this->getParam('aProduct');

        $sDefaultSymbol = $this->getParam('sDefaultSymbol');
        if (empty($aProduct)) {
            return false;
        }
        $aElements = Phpfox::getService('ynsocialstore.product')->getAllElements($iProductId);
        if(count($aElements) && $aProduct['product_type'] == 'physical') {

            if ($aElements[0]['price'] <= $aProduct['product_price']) {
                $aProduct['discount_percentage'] = intval((($aProduct['product_price'] - $aElements[0]['price']) * 100)/$aProduct['product_price']);
            }
            else{
                $aProduct['discount_percentage'] = 0;
            }

            $aProduct['discount_display'] = $aElements[0]['price'];
            $aProduct['remaining'] = $aElements[0]['remain'];
            $aProduct['product_quantity_main'] = $aElements[0]['quantity'];
        }
        else {
            $aProduct['remaining'] = $aProduct['product_quantity'];
        }

        $this->template()->assign(array(
                'aItem' => $aProduct,
                'sHeader' => _p('statistic'),
                'sDefaultSymbol' => $sDefaultSymbol,
            )
        );

        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();

        return 'block';
    }
}