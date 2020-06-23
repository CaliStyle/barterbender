<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/27/16
 * Time: 9:17 AM
 */
class Ynsocialstore_Component_Block_Product_Recently_Viewed extends Phpfox_Component
{
    public function process()
    {
        $iLimit = $this->getParam('limit', 3);
        if (!$iLimit) {
            return false;
        }

        $sRecentlyViewed = Phpfox::getCookie('ynsocialstore_recently_viewed_product');

        if (trim($sRecentlyViewed) == '') {
            return false;
        }

        $sSelect = Phpfox::getService('ynsocialstore.helper')->getNormalSelectQuery();
        $aCond[] = "AND st.module_id = 'ynsocialstore' AND ecp.product_status = 'running' AND ecp.product_id IN (" . $sRecentlyViewed . ")";
        $iCount = 0;

        $aProducts = Phpfox::getService('ynsocialstore.product')->getProducts($sSelect, $iLimit, null, $iCount, $aCond,
            'FIELD(ecp.product_id, ' . $sRecentlyViewed . ')');

        if (!$iCount) {
            return false;
        }

        $this->template()->assign(array(
                'sHeader' => _p('recently_viewed_products'),
                'aItems' => $aProducts,
                'sTypeBlock' => 'recently-viewed',
                'bIsNoModerate' => true,
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
                'info' => _p('Recently Viewed Products Limit'),
                'description' => _p('Define the limit of how many recently viewed products can be displayed when viewing the social store section. Set 0 will hide this block.'),
                'value' => 3,
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
                'title' => '"Recently Viewed Products Limit" must be greater than or equal to 0'
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

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_block_product_recently_viewed_clean')) ? eval($sPlugin) : false);
    }
}