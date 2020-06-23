<?php
if (!empty($aVals['status_background_id'])) {
    Phpfox::getService('yncstatusbg.process')->addBackgroundForStatus($this->_aCallback['feed_id'], $iStatusId,
        $aVals['status_background_id'], Phpfox::getUserId(), $this->_aCallback['module']);
}