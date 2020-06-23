<?php

namespace Apps\YouNet_UltimateVideos\Install;

use Phpfox;

class UltimateVideosv402
{
    private function database()
    {
        return Phpfox::getLib('phpfox.database');
    }

    public function process()
    {
        if (!$this->database()->isField(Phpfox::getT('ynultimatevideo_videos'), 'location_latlng')) {
            $this->database()->query("ALTER TABLE  `" . Phpfox::getT('ynultimatevideo_videos') . "` ADD  `location_latlng` VARCHAR( 100 ) DEFAULT NULL");
        }
        if (!$this->database()->isField(Phpfox::getT('ynultimatevideo_videos'), 'location_name')) {
            $this->database()->query("ALTER TABLE  `" . Phpfox::getT('ynultimatevideo_videos') . "` ADD  `location_name` VARCHAR( 255 ) DEFAULT NULL");
        }
    }
}