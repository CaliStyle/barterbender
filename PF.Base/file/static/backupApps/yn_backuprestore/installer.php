<?php
use Apps\yn_backuprestore\Install\BackupRestorev401;
use Core\App\Installer;

$installer = new Installer();
$installer->onInstall(function () use ($installer) {
    (new BackupRestorev401())->process();
});