<?php


$foxDir = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR;
if (!defined("PHPFOX")) {
    define('PHPFOX_NO_RUN', true);
    require($foxDir . 'PF.Base/start.php');
}

if (!class_exists("\Apps\P_AdvMarketplaceAPI\Install")) {
    require_once($foxDir. "PF.Site/Apps/p-advmarketplace-api/Install.php");
}

$app = new \Apps\P_AdvMarketplaceAPI\Install();
$app->processInstall();


echo "Installed Done";