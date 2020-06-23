<?php

defined('PHPFOX') or exit('NO DICE!');

if ($iReturnId > 0 && isset($aVals['parent_module_id']) && $aVals['parent_module_id'] == 'ynblog') {
    Phpfox::getService('ynblog.process')->updateShare($aVals['parent_feed_id']);
}

