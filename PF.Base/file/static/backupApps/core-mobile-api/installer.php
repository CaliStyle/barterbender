<?php
$installer = new Core\App\Installer();
$installer->onInstall(function () use ($installer) {
    (new \Apps\Core_MobileApi\Installation\Version\v410())->process();
    (new \Apps\Core_MobileApi\Installation\Version\v421())->process();
    (new \Apps\Core_MobileApi\Installation\Version\v440())->process();
});