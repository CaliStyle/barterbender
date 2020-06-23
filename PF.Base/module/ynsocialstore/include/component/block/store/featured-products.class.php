<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/27/16
 * Time: 4:23 PM
 *
 * This only show all featured products in store detail pages
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Store_Featured_Products extends Phpfox_Component
{
    public function process()
    {
        $hideBlock = $this->getParam('hideBlock', false);

        if($hideBlock) {
            return false;
        }

        $aStore = $this->getParam('aStore');

        if (empty($aStore)) {
            return false;
        }
        $iLimit = $this->getParam('limit', 5);
        $aProducts = Phpfox::getService('ynsocialstore.product')->getFeaturedProductByStoreId($iLimit, $aStore['store_id']);

        if (!count($aProducts)) {
            return false;
        }

        $aDefaultCurrency = Phpfox::getService('core.currency')->getDefault();
        $sDefaultSymbol = Phpfox::getService('core.currency')->getSymbol($aDefaultCurrency);

        $this->template()->assign(array(
                'aItems' => $aProducts,
                'sDefaultSymbol' => $sDefaultSymbol,
                'sHeader' => _p('featured_products'),
                'sCorePath' => Phpfox::getParam('core.path_actual').'PF.Base/',
            )
        );
        return 'block';
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('ynsocialstore_featured_limit'),
                'description' => _p('ynsocialstore_featured_limit_description'),
                'value' => 5,
                'type' => 'integer',
                'var_name' => 'limit',
            ]
        ];
    }

    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => _p('ynsocialstore_featured_limit_validation')
            ]
        ];
    }
}
