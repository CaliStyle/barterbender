<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 11/15/16
 * Time: 11:53 AM
 */
defined('PHPFOX') or exit('NO DICE!');

class Ynsocialstore_Component_Block_Product_You_May_Like extends Phpfox_Component
{
    public function process()
    {
        /*
         * This block products in the same category with products currents logged on user has bought recently.
         * Maximum number to be shown defined in back end
         */
        if (!Phpfox::isUser()) {
            return false;
        }

        $iLimit = $this->getParam('limit', 4);
        if (!$iLimit) {
            return false;
        }

        $aProducts = Phpfox::getService('ynsocialstore.product')->getYouMayLikeProducts($iLimit);

        if (!count($aProducts)) {
            return false;
        }

        $this->template()->assign(array(
                'aProducts' => $aProducts,
                'sHeader' => _p('you_may_like'),
                'bIsNoModerate' => true,
                'aFooter' => array(
                    _p('view_more') => $this->url()->makeUrl('ynsocialstore')
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
                'info' => _p('You May Like Products Limit'),
                'description' => _p('Define the limit of how many you may like products can be displayed when viewing the social store section. Set 0 will hide this block.'),
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
                'title' => '"You May Like Products Limit" must be greater than or equal to 0'
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
                'limit',
                'sCorePath'
            )
        );

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_block_product_you_may_like_clean')) ? eval($sPlugin) : false);
    }
}