<?php
namespace Apps\P_AdvEvent;

use Phpfox;
use Phpfox_Module;

Phpfox_Module::instance()
    ->addAliasNames('fevent', 'P_AdvEvent')
    ->addServiceNames([
        'fevent.category' => Service\Category\Category::class,
        'fevent.category.process' => Service\Category\Process::class,
        'fevent.custom' => Service\Custom\Custom::class,
        'fevent.custom.process' => Service\Custom\Process::class,
        'fevent.gapi' => Service\GApi\GApi::class,
        'fevent.gapi.process' => Service\GApi\Process::class,
        'fevent.browse' => Service\Browse::class,
        'fevent.callback' => Service\Callback::class,
        'fevent' => Service\FEvent::class,
        'fevent.helper' => Service\Helper::class,
        'fevent.multicat' => Service\MultiCat::class,
        'fevent.process' => Service\Process::class,
    ])
    ->addTemplateDirs([
        'fevent' => PHPFOX_DIR_SITE_APPS . 'p-advevent' . PHPFOX_DS . 'views',
    ])
    ->addComponentNames('ajax', [
        'fevent.ajax' => Ajax\Ajax::class
    ])
    ->addComponentNames('controller', [
        'fevent.admincp.custom' => Controller\Admin\Custom\AdapterController::class,
        'fevent.admincp.custom.add' => Controller\Admin\Custom\AddController::class,
        'fevent.admincp.custom.index' => Controller\Admin\Custom\IndexController::class,
        'fevent.admincp.add' => Controller\Admin\AddController::class,
        'fevent.admincp.index' => Controller\Admin\IndexController::class,
        'fevent.admincp.location' => Controller\Admin\LocationController::class,
        'fevent.admincp.manageevents' => Controller\Admin\ManageEventsController::class,
        'fevent.admincp.migrations' => Controller\Admin\MigrationsController::class,
        'fevent.admincp.settinggapi' => Controller\Admin\SettingGApiController::class,
        'fevent.admincp.birthdayphoto' => Controller\Admin\BirthdayPhotoController::class,
        'fevent.add' => Controller\AddController::class,
        'fevent.group' => Controller\GroupController::class,
        'fevent.index' => Controller\IndexController::class,
        'fevent.pagecalendar' => Controller\PageCalendarController::class,
        'fevent.profile' => Controller\ProfileController::class,
        'fevent.view' => Controller\ViewController::class,
        'fevent.frame-upload' => Controller\FrameUploadController::class,
        'fevent.unsubscribe' => Controller\Unsubscribe::class,
    ])
    ->addComponentNames('block', [
        'fevent.applyforrepeatevent' => Block\ApplyForRepeatEventBlock::class,
        'fevent.birthday' => Block\BirthDayBlock::class,
        'fevent.browse' => Block\BrowseBlock::class,
        'fevent.calendar' => Block\CalendarBlock::class,
        'fevent.category' => Block\CategoryBlock::class,
        'fevent.custom' => Block\CustomBlock::class,
        'fevent.editlocation' => Block\EditLocationBlock::class,
        'fevent.glogin' => Block\GloginBlock::class,
        'fevent.gmap' => Block\GmapBlock::class,
        'fevent.invite' => Block\InviteBlock::class,
        'fevent.list' => Block\ListBlock::class,
        'fevent.photo' => Block\PhotoBlock::class,
        'fevent.rsvp' => Block\RsvpBlock::class,
        'fevent.search' => Block\SearchBlock::class,
        'fevent.find-event' => Block\FindEventBlock::class,
        'fevent.subscribe-event' => Block\SubscribeEventBlock::class,
        'fevent.event-list' => Block\EventList::class,
        'fevent.guest-list' => Block\GuestListBlock::class,
        'fevent.send-wish' => Block\SendYourWishBlock::class,
        'fevent.feed-event' => Block\FeedEventBlock::class,
    ]);

group('/fevent', function () {
    route('/admincp', function(){
        auth()->isAdmin(true);
        Phpfox::getLib('module')->dispatch('fevent.admincp.index');

        return 'controller';
    });

    route('/add/', 'fevent.add');
    route('/group', 'fevent.group');
    route('/pagecalendar', 'fevent.pagecalendar');
    route('/profile', 'fevent.profile');
    route('/:id/*', 'fevent.view')->where([':id' => '([0-9]+)']);;
    route('/frame-upload', 'fevent.frame-upload');
    route('/', 'fevent.index');
    route('/category/:id/*', 'fevent.index')->where([':id' => '([0-9]+)']);
    route('/unsubscribe/*','fevent.unsubscribe');
});

Phpfox::getLib('setting')->setParam('fevent.thumbnail_sizes', [50, 120, 200]);