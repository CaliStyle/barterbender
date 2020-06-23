<?php

namespace Apps\YNC_Blogs\Block;

use Phpfox_Component;
use Phpfox;

class OtherAuthors extends Phpfox_Component
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

        $aConds[] = 'AND u.user_id <> ' . Phpfox::getUserId();

        $aItems = Phpfox::getService('ynblog.blog')->getHotBloggers('other_author', $iLimit, $aConds, null, $iCacheTime);
        $aItems = Phpfox::getService('ynblog.helper')->getRandomItems($aItems, $iLimit);

        if (empty($aItems) || $this->request()->get('view') != 'my') {
            return false;
        }

        if ($bIsSideLocation) {
            $this->template()->assign('sModeViewDefault', 'list');
        } else {
            $this->template()->assign('sModeViewDefault', 'grid');
        }

        $this->template()
            ->assign(array(
                'sHeader' => _p('other_bloggers'),
                'aItems' => $aItems,
                'sCustomClassName' => 'p-block',
            ));

        return 'block';
    }

    public function getSettings()
    {
        return [
            [
                'info' => _p('Other Authors Limit'),
                'description' => _p('Define the limit of how many other authors can be displayed when viewing the blog section. Set 0 will hide this block.'),
                'value' => 6,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Same Author Blogs Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Other Authors</b> by minutes. 0 means we do not cache data for this block.'),
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
                'title' => '"Suggestion Other Authors Limit" must be greater than or equal to 0'
            ]
        ];
    }
}
