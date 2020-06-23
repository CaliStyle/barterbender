<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/25/16
 * Time: 3:54 PM
 */
defined('PHPFOX') or exit('NO DICE!');

class Ynsocialstore_Component_Block_Product_Others_Product extends Phpfox_Component
{
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $iLimit = $this->getParam('limit', 4);
        if (!$iLimit) {
            return false;
        }

        $aProduct = $this->getParam('aProduct');
        if (empty($aProduct)) {
            return false;
        }

        $sSelect = Phpfox::getService('ynsocialstore.helper')->getNormalSelectQuery();
        $aCond[] = "AND ecp.product_status = 'running'";
        $aCond[] = "AND st.store_id = {$aProduct['store_id']} AND ecp.product_id <> {$aProduct['product_id']}";
        $iCount = 0;

        $aOtherProducts = Phpfox::getService('ynsocialstore.product')->getProducts($sSelect, $iLimit, null, $iCount,
            $aCond, 'ecp.product_creation_datetime DESC', true);

        if (!count($aOtherProducts)) {
            return false;
        }
        $sStoreName = Phpfox::getService('ynsocialstore')->getFieldsStoreById('name', $aProduct['item_id'], 'getfield');
        $sViewMoreLink = 'ynsocialstore.store.' . $aProduct['item_id'] . '.' . Phpfox::getLib('parse.input')->cleanTitle($sStoreName) . '.products';
        $this->template()->assign(array(
                'sHeader' => _p('others_from_this_store'),
                'aItems' => $aOtherProducts,
                'sViewMoreLink' => $sViewMoreLink,
                'bIsNoModerate' => true,
                'aFooter' => array(
                    _p('view_more') => Phpfox::getLib('url')->makeUrl($sViewMoreLink)
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
                'info' => _p('Others Products Limit'),
                'description' => _p('Define the limit of how many others products can be displayed when viewing the social store section. Set 0 will hide this block.'),
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
                'title' => '"Others Products Limit" must be greater than or equal to 0'
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
                'sHeader',
                'aItems',
                'sViewMoreLink',
                'limit',
                'bIsNoModerate'
            )
        );

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_block_product_others_clean')) ? eval($sPlugin) : false);
    }
}