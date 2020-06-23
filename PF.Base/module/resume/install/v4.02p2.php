<?php
defined('PHPFOX') or exit('NO DICE!');

$db = Phpfox::getLib('phpfox.database');

$tableName = Phpfox::getT('resume_skill');
if($db->tableExists($tableName)) {
    $db->query('ALTER TABLE `'. $tableName .'` DROP PRIMARY KEY');
    $db->query('ALTER TABLE `'. $tableName .'` ADD PRIMARY KEY (`skill_name`(180))');
}

$tableName = Phpfox::getT('resume_publication');
if($db->tableExists($tableName)) {
    if(db()->isIndex($tableName, 'published_year_magazine_published_month')) {
        db()->dropIndex($tableName, 'published_year_magazine_published_month');
        db()->addIndex($tableName, '`published_day`,`published_year`,`title`(180),`published_month`', '180_published_year_magazine_published_month');
    }
}