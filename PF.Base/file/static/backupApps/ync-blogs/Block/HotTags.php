<?php

namespace Apps\YNC_Blogs\Block;

use Phpfox_Component;
use Phpfox;

class HotTags extends Phpfox_Component
{
    public function process()
    {
        if (!Phpfox::isModule('tag'))
            return false;

        if (!$this->getParam('bIsSearch')) {
            return false;
        }

        $iLimit = $this->getParam('limit', 10);
        if (!$iLimit) {
            return false;
        }

        $aHotTags = Phpfox::getService('ynblog.blog')->getHotTags($iLimit);

        if (empty($aHotTags)) {
            return false;
        }
        $this->template()
            ->assign([
                'sHeader' => _p('hot_tags'),
                'aHotTags' => $aHotTags,
            ]);

        return 'block';
    }

    public function getSettings()
    {
        return [
            [
                'info' => _p('Hot Tags Limit'),
                'description' => _p('Define the limit of how many hot tags can be displayed when viewing the blog section. Set 0 will hide this block.'),
                'value' => 10,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Hot Tags Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Hot Tags</b> by minutes. 0 means we do not cache data for this block.'),
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
                'title' => '"Suggestion Hot Tags Limit" must be greater than or equal to 0'
            ]
        ];
    }
}
