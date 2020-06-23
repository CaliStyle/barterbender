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

        $iCacheTime = $this->getParam('cache_time', 5);

        $bIsSearch = $this->getParam('bIsSearch');
        $sBlockLocation = $this->getParam('location', 0);  // 1,9 left 3,10 right
        $bIsSideLocation = Phpfox::getService('ynblog.helper')->bIsSideLocation($sBlockLocation);

        if (($bIsSearch || Phpfox::getLib('module')->getFullControllerName() == 'ynblog.following') && !$bIsSideLocation) {
            return false;
        }

        if (defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $aItems = Phpfox::getService('ynblog.blog')->getRecentPosts('recent_comment', $iLimit, 'ab.latest_comment DESC', 'AND ab.latest_comment <> 0',$iCacheTime);

        foreach ($aItems as $key => $aItem){
            $aUserComment = Phpfox::getService('ynblog.blog')->getUserComment($aItem['blog_id']);
            $aItems[$key]['full_name'] = $aUserComment['full_name'];
        }

        if (empty($aItems)) {
            return false;
        }
        $sBlockHeader = $this->getParam('title', _p('recent_comment'));

        $this->template()
            ->assign(array(
                'sHeader' => $sBlockHeader,
                'aItems' => $aItems,
                'bIsRecent' => true,
                'bIsSideLocation' => $bIsSideLocation,
                'sCustomClassName' => 'p-block',
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
