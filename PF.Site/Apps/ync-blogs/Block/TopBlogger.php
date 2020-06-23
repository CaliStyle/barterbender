<?php

namespace Apps\YNC_Blogs\Block;

use Phpfox_Component;
use Phpfox;

class TopBlogger extends Phpfox_Component
{
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $iLimit = $this->getParam('limit', 6);
        if (!$iLimit) {
            return false;
        }

        $iCacheTime = $this->getParam('cache_time', 5);

        $bIsSearch = $this->getParam('bIsSearch');
        $sBlockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $bIsSideLocation = Phpfox::getService('ynblog.helper')->bIsSideLocation($sBlockLocation);

        if (($bIsSearch || Phpfox::getLib('module')->getFullControllerName() == 'ynblog.following') && !$bIsSideLocation) {
            return false;
        }

        $aItems = Phpfox::getService('ynblog.blog')->getHotBloggers('top_bloggers', $iLimit, [], 'viewed_total_entries DESC', $iCacheTime);

        if (empty($aItems)) {
            return false;
        }

        foreach ($aItems as &$aItem) {
            $aItem['aLatestPost'] = Phpfox::getService('ynblog.blog')->getRecentPosts('latest_post' . $aItem['user_id'], 1, 'ab.time_stamp DESC', 'AND u.user_id = ' . $aItem['user_id'], $iCacheTime);
            if (!empty($aItem['cover_photo'])) {
                $aItem['aCoverPhoto'] = Phpfox::getService('photo')->getCoverPhoto($aItem['cover_photo']);;
            } else {
                $aItem['aCoverPhoto'] = array();
            }
        }

        if ($bIsSideLocation) {
            $this->template()->assign('sModeViewDefault', 'list');
        } else {
            $this->template()->assign('sModeViewDefault', 'grid');
        }

        $this->template()
            ->assign([
                'sHeader' => _p('top_bloggers'),
                'aItems' => $aItems,
                'sCustomClassName' => 'p-block',
                'sCoverDefaultUrl' => flavor()->active->default_photo('user_cover_default', true),
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
                'description' => _p('Define how long we should keep the cache for the <b>Top Bloggers</b> by minutes. 0 means we do not cache data for this block.'),
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
