<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/28/16
 * Time: 10:27
 */
defined('PHPFOX') or exit('NO DICE!');

class Ynsocialstore_Component_Block_Product_Featured extends Phpfox_Component
{
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $hideBlock = $this->getParam('hideBlock', false);
        if($hideBlock) {
            return false;
        }

        $iLimit = $this->getParam('limit', 5);

        $sSelect = Phpfox::getService('ynsocialstore.helper')->getNormalSelectQuery() . ', eps.total_rating, eps.rating, ecp.total_review';
        $aCond[] = "AND st.module_id = 'ynsocialstore' AND ecp.product_status = 'running' AND ((ecp.feature_start_time <= " . PHPFOX_TIME . " AND ecp.feature_end_time >= " . PHPFOX_TIME . ") OR ecp.feature_end_time = 1)";
        $iCount = 0;

        $aProducts = Phpfox::getService('ynsocialstore.product')->getProducts($sSelect, $iLimit, null, $iCount, $aCond, 'RAND()');

        if (!$iCount)
            return false;

        $this->template()->assign(array(
            'aItems' => $aProducts,
            'sCorePath' => Phpfox::getParam('core.path_actual') . 'PF.Base/',
            'sHeader' => _p('featured_products')
        ));

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