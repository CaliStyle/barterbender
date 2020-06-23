<?php

namespace Apps\YNC_Blogs\Block;

use Phpfox_Component;
use Phpfox;

class MostRead extends Phpfox_Component
{
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            return false;
        }

        $iLimit = $this->getParam('limit', 4);
        if (!$iLimit) {
            return false;
        }

        $aItems = Phpfox::getService('ynblog.blog')->getRecentPosts('most_read', $iLimit, 'ab.total_view DESC', null);

        if (empty($aItems) || $this->request()->get('view') != '') {
            return false;
        }


        $this->template()
            ->assign(array(
                'sHeader' => _p('most_read'),
                'aItems' => $aItems,
                'sTypeBlock' => 'total_view',
                'sTypeUnit' => array(
                    'plural' => 'views',
                    'singular' => 'view',
                ),
                'aFooter' => array(
                    _p('view_more') => $this->url()->makeUrl('ynblog') . '?s=1&sort=most-viewed'
                ),
            ));


        return 'block';
    }

    public function getSettings()
    {
        return [
            [
                'info' => _p('Most Read Blogs Limit'),
                'description' => _p('Define the limit of how many most read blogs can be displayed when viewing the blog section. Set 0 will hide this block.'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Most Read Blogs Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Most Read Blogs</b> by minutes. 0 means we do not cache data for this block.'),
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
                'title' => '"Suggestion Most Read Blogs Limit" must be greater than or equal to 0'
            ]
        ];
    }
}
