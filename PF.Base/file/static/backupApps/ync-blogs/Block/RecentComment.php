<?php

namespace Apps\YNC_Blogs\Block;

use Phpfox_Component;
use Phpfox;

class RecentComment extends Phpfox_Component
{
    public function process()
    {
        $iLimit = $this->getParam('limit', 3);
        if (!$iLimit) {
            return false;
        }

        $aItems = Phpfox::getService('ynblog.blog')->getRecentPosts('recent_comment', $iLimit, 'ab.latest_comment DESC', 'AND ab.latest_comment <> 0');

        if (empty($aItems) || $this->request()->get('view') != '') {
            return false;
        }

        $this->template()
            ->assign(array(
                'sHeader' => _p('recent_comment'),
                'aItems' => $aItems,
                'bIsRecent' => true,
            ));

        return 'block';
    }

    public function getSettings()
    {
        return [
            [
                'info' => _p('Recent Comment Blogs Limit'),
                'description' => _p('Define the limit of how many recent comment blogs can be displayed when viewing the blog section. Set 0 will hide this block.'),
                'value' => 3,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Recent Comment Blogs Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Recent Comment Blogs</b> by minutes. 0 means we do not cache data for this block.'),
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
                'title' => '"Suggestion Recent Comment Blogs Limit" must be greater than or equal to 0'
            ]
        ];
    }
}
