<?php
$installer = new Core\App\Installer();

$installer->onInstall(function() use ($installer) {
    (new \Apps\YNC_Feed\Installation\Data\YnFeedv401())->process();
});