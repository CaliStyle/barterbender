<?php
use Apps\P_AdvMarketplace\Installation\Version\v403 as v403;

$installer = new Core\App\Installer();
$installer->onInstall(function () use ($installer) {
    (new v403())->process();
});
