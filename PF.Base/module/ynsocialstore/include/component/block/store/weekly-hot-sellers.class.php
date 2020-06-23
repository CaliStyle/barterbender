<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 11/17/16
 * Time: 4:10 PM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Store_Weekly_Hot_Sellers extends Phpfox_Component
{
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }
        $iLimit = Phpfox::getParam('ynsocialstore.max_item_weekly_hot_seller_store', 10);
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
}