<?php

namespace Apps\YNC_Blogs\Block;

use Phpfox_Component;
use Phpfox;

class RelatedBlog extends Phpfox_Component
{
    public function process()
    {

        $iLimit = $this->getParam('limit', 3);
        if (!$iLimit) {
            return false;
        }

        if (($iBlogId = $this->getParam('blog_id', 0)) <= 0) {
            return false;
        }

        $aItems = Phpfox::getService('ynblog.blog')->getRelatedBlogs($iBlogId, $iLimit);

        if (empty($aItems)) {
            return false;
        }
        $this->template()
            ->assign(array(
                'sHeader' => _p('related_blogs'),
                'aItems' => $aItems,
                'sTypeBlock' => 'total_view',
                'sTypeUnit' => array(
                    'plural' => 'views',
                    'singular' => 'view',
                ),
            ));

        return 'block';
    }

    public function getSettings()
    {
        return [
            [
                'info' => _p('Related Blogs Limit'),
                'description' => _p('Define the limit of how many featured blogs can be displayed when viewing the blog section. Set 0 will hide this block.'),
                'value' => 3,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Related Blogs Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Related Blogs</b> by minutes. 0 means we do not cache data for this block.'),
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
