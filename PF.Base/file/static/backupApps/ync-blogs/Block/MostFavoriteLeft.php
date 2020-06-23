<?php

namespace Apps\YNC_Blogs\Block;

use Phpfox_Component;
use Phpfox;

class MostFavoriteLeft extends Phpfox_Component
{
    public function process()
    {
        if ($this->request()->get('view') != 'favorite') {
            return false;
        }

        $iLimit = $this->getParam('limit', 10);
        if (!$iLimit) {
            return false;
        }

        $aItems = Phpfox::getService('ynblog.blog')->getRecentPosts('most_favorited', $iLimit, 'ab.total_favorite DESC', null);

        if (empty($aItems)) {
            return false;
        }

        $this->template()
            ->assign(array(
                'sHeader' => _p('most_favorited'),
                'aItems' => $aItems,
                'sTypeBlock' => 'total_favorite',
                'sTypeUnit' => array(
                    'plural' => 'favoriters',
                    'singular' => 'favoriter',
                ),
                'aFooter' => array(
                    _p('view_more') => $this->url()->makeUrl('ynblog') . '?s=1&sort=most-liked'
                ),
            ));

        return 'block';
    }

    public function getSettings()
    {
        return [
            [
                'info' => _p('Most Favorite Blogs Limit'),
                'description' => _p('Define the limit of how many most favorite blogs can be displayed when viewing the blog section. Set 0 will hide this block.'),
                'value' => 10,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Most Favorite Blogs Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Most Favorite Blogs</b> by minutes. 0 means we do not cache data for this block.'),
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
                'title' => '"Suggestion Most Favorite Blogs Limit" must be greater than or equal to 0'
            ]
        ];
    }
}
