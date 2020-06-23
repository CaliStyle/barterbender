<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 11/8/16
 * Time: 1:48 PM
 */
class Ynsocialstore_Component_Block_Product_Bought_By_Friends extends Phpfox_Component
{
    public function process()
    {
        if (!Phpfox::getParam('ynsocialstore.what_did_friend_buy')) {
            return false;
        }

        $iCount = 0;
        $iLimit = $this->getParam('limit', 3);
        if (!$iLimit) {
            return false;
        }

        $aProducts = Phpfox::getService('ynsocialstore.product')->getProductsBoughtByFriends($iLimit, $iCount);

        if (!$iCount) {
            return false;
        }

        $this->template()->assign(array(
                'aItems' => $aProducts,
                'iCount' => $iCount,
                'sTypeBlock' => 'bought-by-friends',
                'sHeader' => _p('Bought By Friends'),
                'bIsNoModerate' => true,
                'aFooter' => array(
                    _p('view_more') => $this->url()->makeUrl('ynsocialstore') . '?view=friendbuy'
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
                'info' => _p('Bought By Friends Products Limit'),
                'description' => _p('Define the limit of how many bought by friends products can be displayed when viewing the social store section. Set 0 will hide this block.'),
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
                'title' => '"Bought By Friends Products Limit" must be greater than or equal to 0'
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
                'iCount'
            )
        );

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_block_product_bought_by_friends_clean')) ? eval($sPlugin) : false);
    }
}