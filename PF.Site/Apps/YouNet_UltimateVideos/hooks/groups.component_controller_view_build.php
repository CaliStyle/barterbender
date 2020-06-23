<?php

$val = Phpfox::getService('groups')->hasPerm($aPage['page_id'], 'ultimatevideo.share_videos');
$val = ($val) ? 1 : 0;
$this->template()->setHeader('<script>window.can_post_ult_video_on_group = ' . $val . ';</script>');
