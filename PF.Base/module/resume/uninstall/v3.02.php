<?php
defined('PHPFOX') or exit('NO DICE!');

$oDb = Phpfox::getLib('phpfox.database');

$oDb->query('drop table IF EXISTS '.Phpfox::getT('resume_custom_field'));
$oDb->query('drop table IF EXISTS '.Phpfox::getT('resume_custom_value'));
$oDb->query('drop table IF EXISTS '.Phpfox::getT('resume_custom_option'));

?>