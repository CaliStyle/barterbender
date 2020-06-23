<?php

namespace Apps\YNC_WebPush;

use Phpfox;
use Phpfox_Module;

$module = Phpfox_Module::instance();

$module->addAliasNames('yncwebpush', 'YNC_WebPush')
    ->addComponentNames('controller', [
        'yncwebpush.setting' => Controller\SettingController::class,
        'yncwebpush.mark-read-notification' => Controller\MarkReadNotificationController::class,
        'yncwebpush.admincp.send-push-notification' => Controller\Admin\SendPushNotificationController::class,
        'yncwebpush.admincp.manage-notifications' => Controller\Admin\ManageNotificationsController::class,
        'yncwebpush.admincp.manage-subscribers' => Controller\Admin\ManageSubscribersController::class,
        'yncwebpush.admincp.manage-templates' => Controller\Admin\ManageTemplatesController::class,
        'yncwebpush.admincp.add-template' => Controller\Admin\AddTemplateController::class,
        'yncwebpush.admincp.notification-detail' => Controller\Admin\NotificationDetailController::class,
    ])
    ->addComponentNames('block', [
        'yncwebpush.request-banner' => Block\RequestBannerBlock::class,
        'yncwebpush.subscribers-notification' => Block\SubscribersNotificationBlock::class,
        'yncwebpush.admincp.compose-notification' => Block\Admin\ComposeNotificationBlock::class
    ])
    ->addComponentNames('ajax', [
        'yncwebpush.ajax' => Ajax\Ajax::class
    ])
    ->addServiceNames([
        'yncwebpush' => Service\Yncwebpush::class,
        'yncwebpush.callback' => Service\Callback::class,
        'yncwebpush.setting' => Service\Setting\Setting::class,
        'yncwebpush.setting.process' => Service\Setting\Process::class,
        'yncwebpush.token' => Service\Token\Token::class,
        'yncwebpush.token.process' => Service\Token\Process::class,
        'yncwebpush.notification' => Service\Notification\Notification::class,
        'yncwebpush.notification.process' => Service\Notification\Process::class,
        'yncwebpush.template' => Service\Template\Template::class,
        'yncwebpush.template.process' => Service\Template\Process::class,
    ])
    ->addTemplateDirs([
        'yncwebpush' => PHPFOX_DIR_SITE_APPS . 'ync-webpush' . PHPFOX_DS . 'views',
    ]);

group('/push-notification', function () {
    route('/', 'yncwebpush.setting');
    route('/mark-read-notification', 'yncwebpush.mark-read-notification');
});
group('/admincp', function () {
    route('/yncwebpush', function () {
        auth()->isAdmin(true);
        Phpfox::getLib('module')->dispatch('yncwebpush.admincp.manage-notifications');
        return 'controller';
    });
});
Phpfox::getLib('setting')->setParam('yncwebpush.sent_time_stamp', 'M d, Y g:i A');