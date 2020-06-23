<?php

defined('PHPFOX') or exit('NO DICE!');

class Ynsocialstore_Component_Block_Store_Weekly_Hot_Seller extends Phpfox_Component
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

        $iLimit = $this->getParam('limit', 10);
        $aStores = Phpfox::getService('ynsocialstore')->getWeeklyHotSellers($iLimit);
        if(!count($aStores))
            return false;

        $this->template()->assign(array(
            'aItems' => $aStores,
            'sCorePath' => Phpfox::getParam('core.path_actual').'PF.Base/',
            'sHeader' => _p('weekly_hot_sellers'),
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
                'info' => _p('ynsocialstore_weekly_hot_seller_limit'),
                'description' => _p('ynsocialstore_weekly_hot_seller_limit_description'),
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
                'title' => _p('ynsocialstore_weekly_hot_seller_limit_validation')
            ]
        ];
    }
}