<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/28/16
 * Time: 14:59
 */
defined('PHPFOX') or exit('NO DICE!');

class Ynsocialstore_Component_Block_Product_Weekly_Hot_Selling extends Phpfox_Component
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


        $sFullControllerName = Phpfox::getLib('module')->getFullControllerName();
        $iStoreId = 0;
        $bInDetail = false;
        $iLimit = $this->getParam('limit', 10);

        if ($sFullControllerName == 'ynsocialstore.store/detail') {
            $aStore = $this->getParam('aStore');

            if (empty($aStore)) {
                return false;
            }
            $iLimit = $this->getParam('limit', 6);

            $bInDetail = true;
            $iStoreId = $aStore['store_id'];
        }


        $aProducts = Phpfox::getService('ynsocialstore.product')->getHotSellingInThisWeek($iLimit, $iStoreId);

        if (!count($aProducts)) {
            return false;
        }

        $this->template()->assign(array(
            'sHeader' => _p('weekly_hot_selling'),
            'aProducts' => $aProducts,
            'sCorePath' => Phpfox::getParam('core.path_file'),
            'bIsNoModerate' => true,
            'bInDetail' => $bInDetail,
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
                'info' => _p('ynsocialstore_weekly_hot_selling_limit'),
                'description' => _p('ynsocialstore_weekly_hot_selling_limit_description'),
                'value' => 10,
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
                'title' => _p('ynsocialstore_weekly_hot_selling_limit_validation')
            ]
        ];
    }
}