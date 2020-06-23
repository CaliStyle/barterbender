<?php
defined('PHPFOX') or exit('NO DICE!');

$db = Phpfox::getLib('database');

$tableName = 'ynchat_emoticon';
if($db->tableExists($tableName) && $db->isIndex($tableName, 'text')) {
    $db->dropIndex($tableName, 'text');
    $db->query('ALTER TABLE `'. $tableName .'` ADD UNIQUE INDEX `text_180` (`text`(180))');
}