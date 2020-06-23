<?php

include "cli.php";
@ini_set('display_startup_errors', 1);
@ini_set('display_errors', 1);
@ini_set('error_reporting', -1);
define('PHPFOX_FEED_NO_CHECK', true);

$service = Phpfox::getService("ultimatevideo.process");

$service->convertVideos();

echo "\nRun Cron successfully";