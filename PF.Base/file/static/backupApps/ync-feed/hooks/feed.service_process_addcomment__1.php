<?php

$aInsert = [
    'module' => $this->_aCallback['module'],
    'type_id' => $this->_aCallback['feed_id'],
    'table_prefix' => $this->_aCallback['table_prefix'],
    'item_id' => $iStatusId
];


Phpfox::getService('ynfeed')->setExtraInfo(array_merge($aInsert, $aVals));
