<?php

namespace Apps\YNC_Blogs\Service;

use Phpfox;
use Phpfox_Service;

class CacheRemove extends Phpfox_Service
{
    public function my()
    {
        $iUserId = Phpfox::getUserId();
        $this->user($iUserId);
    }

    public function user($iUserId = null)
    {
        if (!isset($iUserId)) {
            $iUserId = Phpfox::getUserId();
        }
        $iUserId = (int)$iUserId;
        $this->cache()->remove('ynblog_draft_count_' . $iUserId);
        $this->cache()->remove('ynblog_draft_total_' . $iUserId);
        $this->cache()->remove('ynblog_my_count_' . $iUserId);
        $this->cache()->remove('ynblog_my_total_' . $iUserId);
    }

    public function advancedblog($iBlogId)
    {
        $iBlogId = (int)$iBlogId;
        $this->cache()->remove('ynblog_detail_view_' . $iBlogId);
        $this->cache()->remove('ynblog_detail_edit_' . $iBlogId);
    }
}
