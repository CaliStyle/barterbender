<?php

namespace Apps\YNC_Blogs\Block;

use Phpfox;
use Phpfox_Component;

class RecentPosts extends Phpfox_Component
{
    public function process()
    {

        if (!$this->getParam('bIsSearch')) {
            return false;
        }
        $iLimit = $this->getParam('limit', 6);
        if (!$iLimit) {
            return false;
        }

        $aItems = Phpfox::getService('ynblog.blog')->getRecentPosts('recent_post', $iLimit, 'ab.time_stamp DESC', null);
        if (!empty($aItems)) {
            foreach ($aItems as &$aItem) {
                Phpfox::getService('ynblog.blog')->retrievePermissionForBlog($aItem);
            }
        }
        $this->template()
            ->assign(array(
                'sHeader' => count($aItems) ? _p('recent_blogs') : '',
                'aItems' => $aItems,
                'sTypeBlock' => 'total_favorite',
                'sTypeIcon' => 'fa fa-heart-o',
                'sTypeUnit' => array(
                    'plural' => 'favoriters',
                    'singular' => 'favoriter',
                ),
                'bShowModerator' => false,
            ));

        return 'block';
    }

    public function getSettings()
    {
        return [
            [
                'info' => _p('Recent Post Blogs Limit'),
                'description' => _p('Define the limit of how many recent post blogs can be displayed when viewing the blog section. Set 0 will hide this block.'),
                'value' => 6,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Recent Post Blogs Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Recent Post Blogs</b> by minutes. 0 means we do not cache data for this block.'),
                'value' => Phpfox::getParam('core.cache_time_default'),
                'options' => Phpfox::getParam('core.cache_time'),
                'type' => 'select',
                'var_name' => 'cache_time',
            ]
        ];
    }

    /**
     * @return array
     */
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => '"Suggestion Recent Post Blogs Limit" must be greater than or equal to 0'
            ]
        ];
    }
}
