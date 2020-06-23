<?php

namespace Apps\YNC_Blogs\Block;

use Phpfox_Component;
use Phpfox;

class FeaturedBlog extends Phpfox_Component
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

        $aItems = Phpfox::getService('ynblog.blog')->getRecentPosts('featured_blog', 100, null, 'AND ab.is_featured = 1');

        if (empty($aItems)) {
            return false;
        }

        $aItems = Phpfox::getService('ynblog.helper')->getRandomItems($aItems, $iLimit);

        $this->template()
            ->assign([
                'sHeader' => _p(''),
                'aItems' => $aItems,
                'appPath' => Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-blogs',
            ]);

        return 'block';
    }

    public function getSettings()
    {
        return [
            [
                'info' => _p('Featured Blogs Limit'),
                'description' => _p('Define the limit of how many featured blogs can be displayed when viewing the blog section. Set 0 will hide this block.'),
                'value' => 6,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Featured Blogs Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Featured Blogs</b> by minutes. 0 means we do not cache data for this block.'),
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
                'title' => '"Suggestion Blogs Limit" must be greater than or equal to 0'
            ]
        ];
    }
}
