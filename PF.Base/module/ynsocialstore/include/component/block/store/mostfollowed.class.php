<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/14/16
 * Time: 17:00
 */
defined('PHPFOX') or exit('NO DICE!');

class Ynsocialstore_Component_Block_Store_Mostfollowed extends Phpfox_Component
{
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }
        $iLimit = $this->getParam('limit', 5);
        if (!$iLimit) {
            return false;
        }
        $aStores = Phpfox::getService('ynsocialstore')->getMostFollowedStores($iLimit);
        if (!count($aStores)) {
            return false;
        }

        $this->template()->assign(array(
            'sHeader' => _p('most_following'),
            'aItems' => $aStores,
            'sCorePath' => Phpfox::getParam('core.path_actual') . 'PF.Base/',
            'aFooter' => array(
                _p('view_more') => $this->url()->makeUrl('ynsocialstore.store') . '?sort=most-following'
            )
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
                'info' => _p('Most Followed Stores Limit'),
                'description' => _p('Define the limit of how many most followed stores can be displayed when viewing the social store section. Set 0 will hide this block.'),
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
                'title' => '"Most Followed Stores Limit" must be greater than or equal to 0'
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

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_block_store_most_followed_clean')) ? eval($sPlugin) : false);
    }
}