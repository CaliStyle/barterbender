<?php

namespace Apps\YNC_Blogs\Block;

use Phpfox_Component;
use Phpfox;

class HotBlogger extends Phpfox_Component
{
    public function process()
    {
        $iLimit = $this->getParam('limit', 6);
        if (!$iLimit) {
            return false;
        }

        $aItems = Phpfox::getService('ynblog.blog')->getHotBloggers('hot_bloggers', $iLimit, [], 'viewed_total_entries DESC');

        if (empty($aItems) || $this->request()->get('view') != '') {
            return false;
        }

        $this->template()
            ->assign([
                'sHeader' => _p('hot_bloggers'),
                'aItems' => $aItems
            ]);

        return 'block';
    }

    public function getSettings()
    {
        return [
            [
                'info' => _p('Hot Blogger Limit'),
                'description' => _p('Define the limit of how many hot blogger can be displayed when viewing the blog section. Set 0 will hide this block.'),
                'value' => 6,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Same Author Blogs Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Hot Bloggers</b> by minutes. 0 means we do not cache data for this block.'),
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
                'title' => '"Suggestion Hot Bloggers Limit" must be greater than or equal to 0'
            ]
        ];
    }
}
