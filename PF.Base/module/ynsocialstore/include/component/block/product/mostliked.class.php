<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/25/16
 * Time: 3:54 PM
 */
defined('PHPFOX') or exit('NO DICE!');

class Ynsocialstore_Component_Block_Product_MostLiked extends Phpfox_Component
{
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $iCount = 0;
        $iLimit = $this->getParam('limit', 5);
        if (!$iLimit) {
            return false;
        }

        $sSelect = Phpfox::getService('ynsocialstore.helper')->getNormalSelectQuery();
        $aCond[] = 'AND ecp.product_status = \'running\'';
        $aProducts = Phpfox::getService('ynsocialstore.product')->getProducts($sSelect, $iLimit, null, $iCount, $aCond,
            'ecp.total_like DESC');

        if (!$iCount) {
            return false;
        }

        $this->template()->assign(array(
                'sHeader' => _p('most_liked_products'),
                'aItems' => $aProducts,
                'sTypeBlock' => 'most-liked',
                'bIsNoModerate' => true,
                'aFooter' => array(
                    _p('view_more') => $this->url()->makeUrl('ynsocialstore') . '?sort=most-liked'
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
                'info' => _p('Most Liked Products Limit'),
                'description' => _p('Define the limit of how many most liked products can be displayed when viewing the social store section. Set 0 will hide this block.'),
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
                'title' => '"Most Liked Products Limit" must be greater than or equal to 0'
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
                'sTypeBlock',
                'limit',
                'bIsNoModerate'
            )
        );

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_block_product_most_liked_clean')) ? eval($sPlugin) : false);
    }
}