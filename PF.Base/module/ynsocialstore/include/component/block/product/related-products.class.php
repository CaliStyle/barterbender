<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/25/16
 * Time: 3:54 PM
 */
defined('PHPFOX') or exit('NO DICE!');

class Ynsocialstore_Component_Block_Product_Related_Products extends Phpfox_Component
{
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        // Get limit
        $iLimit = $this->getParam('limit', 4);
        if (!$iLimit) {
            return false;
        }

        // Get current product
        $aProduct = $this->getParam('aProduct');
        if (empty($aProduct)) {
            return false;
        }

        // Get main categories to query
        $aCategory = Phpfox::getService('ynsocialstore.product')->getCategoryByProductId($aProduct['product_id']);
        if (!isset($aCategory['category_id'])) {
            return false;
        }

        $aRelatedProducts = Phpfox::getService('ynsocialstore.product')->getRelatedProducts($aProduct['product_id'],
            $aCategory['category_id'], $iLimit);

        if (!count($aRelatedProducts)) {
            return false;
        }
        $sCategoryName = Core\Lib::phrase()->isPhrase($aCategory['title']) ? _p($aCategory['title']) : Phpfox::getLib('parse.input')->convert($aCategory['title']);
        $this->template()->assign(array(
                'sHeader' => _p('related_products'),
                'aItems' => $aRelatedProducts,
                'aCategory' => $aCategory,
                'bIsNoModerate' => true,
                'aFooter' => array(
                    _p('view_more') => Phpfox::permalink('ynsocialstore.category', $aCategory['category_id'], $sCategoryName)
                )
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
                'info' => _p('Related Products Limit'),
                'description' => _p('Define the limit of how many related products can be displayed when viewing the social store section. Set 0 will hide this block.'),
                'value' => 4,
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
                'title' => '"Related Products Limit" must be greater than or equal to 0'
            ]
        ];
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        // Lets clear it from memory
        $this->template()->clean(array(
                'aItems',
                'sHeader',
                'aCategory',
                'limit',
                'bIsNoModerate'
            )
        );

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_block_product_related_clean')) ? eval($sPlugin) : false);
    }
}