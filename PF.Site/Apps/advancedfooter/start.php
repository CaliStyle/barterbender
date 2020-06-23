<?php
/**
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_AdvancedFooter
 */
namespace Apps\Advancedfooter;

defined('PHPFOX') or exit('NO DICE!');

use Phpfox;

Phpfox::getLib('module')
    ->addAliasNames('advancedfooter', 'Advancedfooter')
    ->addServiceNames([
        'advancedfooter.data' => Service\Data::class,
        'advancedfooter.social' => Service\Social::class,
        'advancedfooter.menu' => Service\Menu::class,
    ])
    ->addTemplateDirs([
        'advancedfooter' => PHPFOX_DIR_SITE_APPS . 'advancedfooter' . PHPFOX_DS . 'views'
    ])
    ->addComponentNames('controller', [
        'advancedfooter.admincp.social' => Controller\Admin\SocialController::class,
        'advancedfooter.admincp.addsocial' => Controller\Admin\AddsocialController::class,
        'advancedfooter.admincp.delete-social' => Controller\Admin\DeleteSocialController::class,

        'advancedfooter.admincp.index' => Controller\Admin\IndexController::class,
        'advancedfooter.admincp.addmenu' => Controller\Admin\AddmenuController::class,
        'advancedfooter.admincp.delete-menu' => Controller\Admin\DeleteMenuController::class,
    ])
    ->addComponentNames('ajax', [
        'advancedfooter.ajax' => Ajax\Ajax::class
    ])
    ->addComponentNames('block', [
        'advancedfooter.main' => Block\Main::class,
    ]);

group('/advancedfooter', function () {
    // BackEnd routes
    route('/admincp', function () {
        auth()->isAdmin(true);
        Phpfox::getLib('module')->dispatch('advancedfooter.admincp.index');
        return 'controller';
    });

    route('/admincp/social/order', function () {
        auth()->isAdmin(true);
        $ids = request()->get('ids');
        $ids = trim($ids, ',');
        $ids = explode(',', $ids);
        $values = [];
        foreach ($ids as $key => $id) {
            $values[$id] = $key + 1;
        }
        Phpfox::getService('core.process')->updateOrdering([
                'table' => 'advancedfooter_category',
                'key' => 'category_id',
                'values' => $values,
            ]
        );

        return true;
    });

    route('/admincp/menu/order', function () {
        auth()->isAdmin(true);
        $ids = request()->get('ids');
        $ids = trim($ids, ',');
        $ids = explode(',', $ids);
        $values = [];
        foreach ($ids as $key => $id) {
            $values[$id] = $key + 1;
        }
        Phpfox::getService('core.process')->updateOrdering([
                'table' => 'advancedfooter_menu',
                'key' => 'category_id',
                'values' => $values,
            ]
        );

        return true;
    });
});