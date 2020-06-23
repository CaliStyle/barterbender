<?php

use Apps\P_AdvEventAPI\Installation\Version\v401 as v401;

$installer = new Core\App\Installer();
$installer->onInstall(function() use ($installer) {
    (new v401())->process();
});