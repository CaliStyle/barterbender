<?php
namespace Apps\YouNet_UltimateVideos\Install;

use Phpfox;

class UltimateVideosv403p1
{
    public function process()
    {
        $this->addSponsorField();
    }

    private function addSponsorField()
    {
        if(!db()->isField(Phpfox::getT('ynultimatevideo_videos'), 'is_sponsor')) {
            db()->query('ALTER TABLE `'. Phpfox::getT('ynultimatevideo_videos') .'` ADD `is_sponsor` TinyInt(1) NOT NULL DEFAULT \'0\' AFTER `is_featured`');
        }
    }
}