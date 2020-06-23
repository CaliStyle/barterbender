<?php
namespace Apps\yn_backuprestore;

defined('PHPFOX_DEBUG') or define('PHPFOX_DEBUG', true);

use Phpfox_Module;

Phpfox_Module::instance()->addServiceNames([
    'ynbackuprestore.destination' => Service\Destination::class,
    'ynbackuprestore.type'        => Service\Type::class,
    'ynbackuprestore.backup'      => Service\Backup::class,
    'ynbackuprestore.schedule'    => Service\Schedule::class,
    'ynbackuprestore.restore'     => Service\Restore::class,
])->addComponentNames('controller', [
    'ynbackuprestore.admincp.add-destination'           => Controller\AdminAddDestinationController::class,
    'ynbackuprestore.admincp.destination'               => Controller\AdminDestinationController::class,
    'ynbackuprestore.admincp.backup'                    => Controller\AdminBackupController::class,
    'ynbackuprestore.admincp.manage-backup'             => Controller\AdminManageBackupController::class,
    'ynbackuprestore.admincp.process-backup'            => Controller\AdminProcessBackupController::class,
    'ynbackuprestore.admincp.download'                  => Controller\AdminDownloadBackupController::class,
    'ynbackuprestore.admincp.download-log'              => Controller\AdminDownloadLogController::class,
    'ynbackuprestore.admincp.authorize'                 => Controller\AdminAuthorizeController::class,
    'ynbackuprestore.admincp.authorize-google'          => Controller\AdminAuthorizeGoogleController::class,
    'ynbackuprestore.admincp.get-onedrive-access-token' => Controller\AdminGetOnedriveAccessTokenController::class,
    'ynbackuprestore.admincp.get-google-access-token'   => Controller\AdminGetGoogleAccessTokenController::class,
    'ynbackuprestore.admincp.re-backup'                 => Controller\AdminReBackupController::class,
    'ynbackuprestore.admincp.add-schedule'              => Controller\AdminAddScheduleController::class,
    'ynbackuprestore.admincp.manage-schedule'           => Controller\AdminManageScheduleController::class,
    'ynbackuprestore.admincp.restore'                   => Controller\AdminRestoreController::class,
    'ynbackuprestore.admincp.process-restore'           => Controller\AdminProcessRestoreController::class,
])->addComponentNames('block', [
])->addComponentNames('ajax', [
    'ynbackuprestore.ajax' => Ajax\Ajax::class,
])->addTemplateDirs([
    'ynbackuprestore' => PHPFOX_DIR_SITE_APPS . 'yn_backuprestore' . PHPFOX_DS . 'views'
])->addAliasNames('ynbackuprestore', 'YouNet Backup and Restore');

route('/ynbackuprestore/admincp', function (){
    auth()->isAdmin(true);
    echo '<script type="text/javascript">window.location.href="'.\Phpfox_Url::instance()->makeUrl('admincp.app.settings',['id'=>'yn_backuprestore']).'";</script>';
    return true;
});
//(new Install())->processInstall();
//include "installer.php";