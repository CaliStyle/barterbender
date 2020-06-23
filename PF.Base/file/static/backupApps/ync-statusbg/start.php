<?php
namespace Apps\YNC_StatusBg;

use Phpfox;
use Phpfox_Module;

$oModule = Phpfox_Module::instance();
$oModule->addAliasNames('yncstatusbg', 'YNC_StatusBg')
    ->addComponentNames('controller', [
        'yncstatusbg.admincp.manage-collections' => Controller\Admin\ManageCollectionsController::class,
        'yncstatusbg.admincp.add-collection' => Controller\Admin\AddCollectionController::class,
        'yncstatusbg.admincp.frame-upload' => Controller\Admin\FrameUploadController::class
    ])
    ->addComponentNames('block', [
        'yncstatusbg.collections-list' => Block\CollectionsListBlock::class
    ])
    ->addComponentNames('ajax', [
        'yncstatusbg.ajax' => Ajax\Ajax::class
    ])
    ->addServiceNames([
        'yncstatusbg' => Service\Yncstatusbg::class,
        'yncstatusbg.process' => Service\Process::class,
        'yncstatusbg.callback' => Service\Callback::class
    ])
    ->addTemplateDirs([
        'yncstatusbg' => PHPFOX_DIR_SITE_APPS . 'ync-statusbg' . PHPFOX_DS . 'views',
    ]);
group('/admincp', function () {
    route('/yncstatusbg', function () {
        auth()->isAdmin(true);
        Phpfox::getLib('module')->dispatch('yncstatusbg.admincp.manage-collections');
        return 'controller';
    });
});
Phpfox::getLib('setting')->setParam('yncstatusbg.thumbnail_sizes', array(48, 300, 1024));