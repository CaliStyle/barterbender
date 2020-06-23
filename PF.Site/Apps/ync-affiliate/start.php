<?php

namespace Apps\YNC_Affiliate;

use Phpfox_Module;
use Phpfox;
// Load phpFox module service instance, this is core of phpFox service,
// module service contains your app configuration.
$module = Phpfox_Module::instance();

// Instead of \Apps\FirstApp every where. Let register an alias **first_app** that map to our app.
$module->addAliasNames('yncaffiliate', 'YNC_Affiliate');

// Register your controller here
$module->addComponentNames('controller', [
    'yncaffiliate.index'                       => '\Apps\YNC_Affiliate\Controller\IndexController',
    'yncaffiliate.commission-tracking'         => '\Apps\YNC_Affiliate\Controller\CommissionTrackingController',
    'yncaffiliate.commission-rules'            => '\Apps\YNC_Affiliate\Controller\CommissionRulesController',
    'yncaffiliate.network-clients'             => '\Apps\YNC_Affiliate\Controller\NetworkClientsController',
    'yncaffiliate.link-tracking'               => '\Apps\YNC_Affiliate\Controller\LinkTrackingController',
    'yncaffiliate.dynamic-link'                => '\Apps\YNC_Affiliate\Controller\DynamicLinkController',
    'yncaffiliate.suggest-link'                => '\Apps\YNC_Affiliate\Controller\SuggestLinkController',
    'yncaffiliate.my-request'                  => '\Apps\YNC_Affiliate\Controller\MyRequestController',
    'yncaffiliate.register'                    => '\Apps\YNC_Affiliate\Controller\RegisterController',
    'yncaffiliate.statistics'                  => '\Apps\YNC_Affiliate\Controller\StatisticsController',
    'yncaffiliate.faqs'                        => '\Apps\YNC_Affiliate\Controller\FAQController',
    'yncaffiliate.codes'                        => '\Apps\YNC_Affiliate\Controller\CodesController',
    'yncaffiliate.links'                       => '\Apps\YNC_Affiliate\Controller\LinksController',
    'yncaffiliate.invite-link'                 => '\Apps\YNC_Affiliate\Controller\InviteLinkController',
    'yncaffiliate.admincp.manage-affiliate'    => '\Apps\YNC_Affiliate\Controller\Admin\ManageAffiliateController',
    'yncaffiliate.admincp.commission-rule'     => '\Apps\YNC_Affiliate\Controller\Admin\CommissionRuleController',
    'yncaffiliate.admincp.manage-commissions' => '\Apps\YNC_Affiliate\Controller\Admin\ManageCommissionsController',
    'yncaffiliate.admincp.manage-request'      => '\Apps\YNC_Affiliate\Controller\Admin\ManageRequestController',
    'yncaffiliate.admincp.network-client'      => '\Apps\YNC_Affiliate\Controller\Admin\NetworkClientController',
    'yncaffiliate.admincp.statistics'          => '\Apps\YNC_Affiliate\Controller\Admin\StatisticsController',
    'yncaffiliate.admincp.view-statistics'     => '\Apps\YNC_Affiliate\Controller\Admin\ViewStatisticsController',
    'yncaffiliate.admincp.manage-faq'          => '\Apps\YNC_Affiliate\Controller\Admin\ManageFAQController',
    'yncaffiliate.admincp.add-faq'             => '\Apps\YNC_Affiliate\Controller\Admin\AddFAQController',
    'yncaffiliate.admincp.term-service'        => '\Apps\YNC_Affiliate\Controller\Admin\TermServiceController',
    'yncaffiliate.admincp.conversion-rate'     => '\Apps\YNC_Affiliate\Controller\Admin\ConversionRateController',
    'yncaffiliate.admincp.edit-commission-rule'=> '\Apps\YNC_Affiliate\Controller\Admin\EditCommissionRuleController',
    'yncaffiliate.admincp.affiliate-materials' => '\Apps\YNC_Affiliate\Controller\Admin\AffiliateMaterialsController',
    'yncaffiliate.admincp.add-material'        => '\Apps\YNC_Affiliate\Controller\Admin\AddMaterialController',
    'yncaffiliate.admincp.affiliate-client'    => '\Apps\YNC_Affiliate\Controller\Admin\AffiliateClientController',
    'yncaffiliate.admincp.action-commission'   => '\Apps\YNC_Affiliate\Controller\Admin\ActionCommissionController',
    'yncaffiliate.admincp.action-request'      => '\Apps\YNC_Affiliate\Controller\Admin\ActionRequestController',
    'yncaffiliate.admincp.approve-request'      => '\Apps\YNC_Affiliate\Controller\Admin\ApproveRequestController',
])->addComponentNames('block',[
    'yncaffiliate.request-money-form'    => '\Apps\YNC_Affiliate\Block\RequestMoneyFormBlock',
    'yncaffiliate.statistic-chart'    => '\Apps\YNC_Affiliate\Block\StatisticChartBlock',
    'yncaffiliate.commission-rule'    => '\Apps\YNC_Affiliate\Block\CommissionRuleBlock',
    'yncaffiliate.edit-contact-form'    => '\Apps\YNC_Affiliate\Block\EditContactFormBlock'
    ]);


// Register your ajax here
$module->addComponentNames('ajax', [
    'yncaffiliate.ajax' => '\Apps\YNC_Affiliate\Ajax\Ajax',
]);

// Register your service here
$module->addServiceNames([
    'yncaffiliate.affiliate.process'             => '\Apps\YNC_Affiliate\Service\Affiliate\Process',
    'yncaffiliate.affiliate.affiliate'           => '\Apps\YNC_Affiliate\Service\Affiliate\Affiliate',
    'yncaffiliate.setting.process'               => '\Apps\YNC_Affiliate\Service\Setting\Process',
    'yncaffiliate.faq.process'                   => '\Apps\YNC_Affiliate\Service\Faq\Process',
    'yncaffiliate.faq.faq'                       => '\Apps\YNC_Affiliate\Service\Faq\Faq',
    'yncaffiliate.commissionrule.commissionrule' => '\Apps\YNC_Affiliate\Service\CommissionRule\CommissionRule',
    'yncaffiliate.commissionrule.process'        => '\Apps\YNC_Affiliate\Service\CommissionRule\Process',
    'yncaffiliate.helper'                        => '\Apps\YNC_Affiliate\Service\Helper',
    'yncaffiliate.callback'                      => '\Apps\YNC_Affiliate\Service\Callback',
    'yncaffiliate.link'                          => '\Apps\YNC_Affiliate\Service\Link\Link',
    'yncaffiliate.link.process'                  => '\Apps\YNC_Affiliate\Service\Link\Process',
    'yncaffiliate.materials'                     => '\Apps\YNC_Affiliate\Service\Materials\Materials',
    'yncaffiliate.materials.process'             => '\Apps\YNC_Affiliate\Service\Materials\Process',
    'yncaffiliate.commission'                    => '\Apps\YNC_Affiliate\Service\Commission\Commission',
    'yncaffiliate.commission.process'            => '\Apps\YNC_Affiliate\Service\Commission\Process',
    'yncaffiliate.request'                       => '\Apps\YNC_Affiliate\Service\Request\Request',
    'yncaffiliate.request.process'               => '\Apps\YNC_Affiliate\Service\Request\Process',
]);

// Register template directory
$module->addTemplateDirs([
    'yncaffiliate' => PHPFOX_DIR_SITE_APPS . 'ync-affiliate' . PHPFOX_DS . 'views',
]);

// Site Routes


group('/affiliate', function () {
    $iAff = Phpfox::getService('yncaffiliate.affiliate.affiliate')->checkIsAffiliate(Phpfox::getUserId());
    if(!$iAff || $iAff == 'pending' || $iAff == 'denied')
    {
        route('/','yncaffiliate.register');
        route('/commission-rules','yncaffiliate.index');
    }
    else{
        route('/','yncaffiliate.index');
    }
    route('/commission-tracking','yncaffiliate.commission-tracking');
    route('/my-request/*','yncaffiliate.my-request');
    route('/faqs','yncaffiliate.faqs');
    route('/links','yncaffiliate.links');
    route('/codes','yncaffiliate.codes');
    route('/link-tracking','yncaffiliate.link-tracking');
    route('/network-clients','yncaffiliate.network-clients');
    route('/statistics/*','yncaffiliate.statistics');
});
route('/yaf/*','yncaffiliate.invite-link');
// Admincp Routes
group('/yncaffiliate', function () {
    route('/admincp', function () {
        auth()->isAdmin(true);
        Phpfox_Module::instance()->dispatch('yncaffiliate.admincp.statistics');

        return 'controller';
    });
    route('/admincp/commission-rule', function () {
        auth()->isAdmin(true);
        Phpfox_Module::instance()->dispatch('yncaffiliate.admincp.commission-rule');

        return 'controller';
    });
    route('/admincp/manage-commissions', function () {
        auth()->isAdmin(true);
        Phpfox_Module::instance()->dispatch('yncaffiliate.admincp.manage-commissions');

        return 'controller';
    });

    route('/admincp/manage-faq', function () {
        auth()->isAdmin(true);
        Phpfox_Module::instance()->dispatch('yncaffiliate.admincp.manage-faq');

        return 'controller';
    });

    route('/admincp/add-faq', function () {
        auth()->isAdmin(true);
        Phpfox_Module::instance()->dispatch('yncaffiliate.admincp.add-faq');

        return 'controller';
    });

    route('/admincp/manage-affiliate', function () {
        auth()->isAdmin(true);
        Phpfox_Module::instance()->dispatch('yncaffiliate.admincp.manage-affiliate');

        return 'controller';
    });
    route('/admincp/manage-request', function () {
        auth()->isAdmin(true);
        Phpfox_Module::instance()->dispatch('yncaffiliate.admincp.manage-request');

        return 'controller';
    });

    route('/admincp/affiliate-client', function () {
        auth()->isAdmin(true);
        Phpfox_Module::instance()->dispatch('yncaffiliate.admincp.affiliate-client');

        return 'controller';
    });

    route('/admincp/term-service', function () {
        auth()->isAdmin(true);
        Phpfox_Module::instance()->dispatch('yncaffiliate.admincp.term-service');

        return 'controller';
    });

    route('/admincp/conversion-rate', function () {
        auth()->isAdmin(true);
        Phpfox_Module::instance()->dispatch('yncaffiliate.admincp.conversion-rate');

        return 'controller';
    });
    route('/admincp/affiliate-materials', function () {
        auth()->isAdmin(true);
        Phpfox_Module::instance()->dispatch('yncaffiliate.admincp.affiliate-materials');

        return 'controller';
    });
    route('/admincp/add-material', function () {
        auth()->isAdmin(true);
        Phpfox_Module::instance()->dispatch('yncaffiliate.admincp.add-material');

        return 'controller';
    });
    route('/admincp/action-commission', function () {
        auth()->isAdmin(true);
        Phpfox_Module::instance()->dispatch('yncaffiliate.admincp.action-commission');

        return 'controller';
    });
    route('/admincp/action-request', function () {
        auth()->isAdmin(true);
        Phpfox_Module::instance()->dispatch('yncaffiliate.admincp.action-request');

        return 'controller';
    });
    route('/admincp/approve-request', function () {
        auth()->isAdmin(true);
        Phpfox_Module::instance()->dispatch('yncaffiliate.admincp.approve-request');

        return 'controller';
    });
    route('/admincp/faq/order', function () {
        auth()->isAdmin(true);
        $ids = request()->get('ids');
        $ids = trim($ids, ',');
        $ids = explode(',', $ids);
        $values = [];
        foreach ($ids as $key => $id) {
            $values[$id] = $key + 1;
        }

        \Phpfox::getService('core.process')->updateOrdering([
                'table' => 'yncaffiliate_faqs',
                'key' => 'faq_id',
                'values' => $values,
            ]
        );

        \Phpfox::getLib('cache')->remove('yncaffiliate', 'substr');

        return true;
    });
    route('/admincp/materials/order', function () {
        auth()->isAdmin(true);
        $ids = request()->get('ids');
        $ids = trim($ids, ',');
        $ids = explode(',', $ids);
        $values = [];
        foreach ($ids as $key => $id) {
            $values[$id] = $key + 1;
        }

        \Phpfox::getService('core.process')->updateOrdering([
                'table' => 'yncaffiliate_materials',
                'key' => 'material_id',
                'values' => $values,
            ]
        );

        \Phpfox::getLib('cache')->remove('yncaffiliate', 'substr');

        return true;
    });
    route('/admincp/edit-commission-rule',function(){
        auth()->isAdmin(true);
        Phpfox_Module::instance()->dispatch('yncaffiliate.admincp.edit-commission-rule');
        return 'controller';
    });
});
// (new Install())->processInstall();