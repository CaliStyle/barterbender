<?php

namespace Apps\YNC_Blogs\Block;

use Phpfox_Component;
use Phpfox;

class SameAuthor extends Phpfox_Component
{
    public function process()
    {
        $iLimit = $this->getParam('limit', 3);
        if (!$iLimit) {
            return false;
        }

        $aBlog = $this->getParam('aBlog');
        if (empty($aBlog['blog_id'])) {
            return false;
        }

        $aItems = Phpfox::getService('ynblog.blog')->getRecentPosts('same_author', $iLimit, null, 'AND ab.blog_id <> ' . $aBlog['blog_id'] . ' AND u.user_id = ' . $aBlog['user_id']);

        if (empty($aItems)) {
            return false;
        }

        $aItems = Phpfox::getService('ynblog.helper')->getRandomItems($aItems, $iLimit);

        $this->template()
            ->assign(array(
                'sHeader' => _p('same_blogger'),
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
                'info' => _p('Same Author Blogs Limit'),
                'description' => _p('Define the limit of how many same author blogs can be displayed when viewing the blog section. Set 0 will hide this block.'),
                'value' => 3,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Same Author Blogs Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Same Author Blogs</b> by minutes. 0 means we do not cache data for this block.'),
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
