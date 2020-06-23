<?php


include "cli.php";
@ini_set('display_startup_errors',1);
@ini_set('display_errors',1);
@ini_set('error_reporting',-1);

$oService = Phpfox::getService('yncaffiliate.commission.process');

$oService->cronUpdateCommission();

echo "\nRun Cron successfully";