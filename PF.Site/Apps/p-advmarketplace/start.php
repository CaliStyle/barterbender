<?php

Phpfox::getLib('module')->addServiceNames([
    'advancedmarketplace' => Apps\P_AdvMarketplace\Service\AdvancedMarketplace::class,
    'advancedmarketplace.browse' => Apps\P_AdvMarketplace\Service\Browse::class,
    'advancedmarketplace.process' => Apps\P_AdvMarketplace\Service\Process::class,
    'advancedmarketplace.callback' => Apps\P_AdvMarketplace\Service\Callback::class,
    'advancedmarketplace.helper' => Apps\P_AdvMarketplace\Service\Helper::class,
    'advancedmarketplace.category' => Apps\P_AdvMarketplace\Service\Category\Category::class,
    'advancedmarketplace.category.process' => Apps\P_AdvMarketplace\Service\Category\Process::class,
    'advancedmarketplace.custom.group' => Apps\P_AdvMarketplace\Service\Custom\Group::class,
    'advancedmarketplace.custom.process' => Apps\P_AdvMarketplace\Service\Custom\Process::class,
    'advancedmarketplace.customfield.advancedmarketplace' => Apps\P_AdvMarketplace\Service\Customfield\AdvancedMarketplace::class,
    'advancedmarketplace.customfield.group' => Apps\P_AdvMarketplace\Service\Customfield\Group::class,
    'advancedmarketplace.customfield.process' => Apps\P_AdvMarketplace\Service\Customfield\Process::class,
    'advancedmarketplace.rate' => Apps\P_AdvMarketplace\Service\Rate\Rate::class,
    'advancedmarketplace.rate.process' => Apps\P_AdvMarketplace\Service\Rate\Process::class,
    'advancedmarketplace.invoice' => Apps\P_AdvMarketplace\Service\Invoice\Invoice::class,
])->addComponentNames('controller', [
    'advancedmarketplace.admincp.advancedmarketplace' => Apps\P_AdvMarketplace\Controller\Admin\AdvancedmarketplaceController::class,
    'advancedmarketplace.admincp.index' => Apps\P_AdvMarketplace\Controller\Admin\IndexController::class,
    'advancedmarketplace.admincp.add' => Apps\P_AdvMarketplace\Controller\Admin\AddController::class,
    'advancedmarketplace.admincp.listingstatistics' => Apps\P_AdvMarketplace\Controller\Admin\ListingstatisticsController::class,
    'advancedmarketplace.admincp.todaylisting' => Apps\P_AdvMarketplace\Controller\Admin\TodaylistingController::class,
    'advancedmarketplace.admincp.migration' => Apps\P_AdvMarketplace\Controller\Admin\MigrationController::class,
    'advancedmarketplace.admincp.setting' => Apps\P_AdvMarketplace\Controller\Admin\SettingController::class,
    'advancedmarketplace.index' => Apps\P_AdvMarketplace\Controller\IndexController::class,
    'advancedmarketplace.search' => Apps\P_AdvMarketplace\Controller\SearchController::class,
    'advancedmarketplace.add' => Apps\P_AdvMarketplace\Controller\AddController::class,
    'advancedmarketplace.frame-upload' => Apps\P_AdvMarketplace\Controller\FrameUploadController::class,
    'advancedmarketplace.detail' => Apps\P_AdvMarketplace\Controller\DetailController::class,
    'advancedmarketplace.profile' => Apps\P_AdvMarketplace\Controller\ProfileController::class,
    'advancedmarketplace.purchase' => Apps\P_AdvMarketplace\Controller\PurchaseController::class,
    'advancedmarketplace.invoice.index' => Apps\P_AdvMarketplace\Controller\Invoice\IndexController::class,
    'advancedmarketplace.invoice.seller' => Apps\P_AdvMarketplace\Controller\Invoice\SellerController::class,
    'advancedmarketplace.embed' => Apps\P_AdvMarketplace\Controller\EmbedController::class
])->addComponentNames('block', [
    'advancedmarketplace.category' => Apps\P_AdvMarketplace\Block\Category::class,
    'advancedmarketplace.topcategories' => Apps\P_AdvMarketplace\Block\TopCategories::class,
    'advancedmarketplace.topsellers' => Apps\P_AdvMarketplace\Block\TopSellers::class,
    'advancedmarketplace.listinglist' => Apps\P_AdvMarketplace\Block\ListingList::class,
    'advancedmarketplace.owner' => Apps\P_AdvMarketplace\Block\Owner::class,
    'advancedmarketplace.search' => Apps\P_AdvMarketplace\Block\Search::class,
    'advancedmarketplace.tag' => Apps\P_AdvMarketplace\Block\Tag::class,
    'advancedmarketplace.photo' => Apps\P_AdvMarketplace\Block\Photo::class,
    'advancedmarketplace.rating' => Apps\P_AdvMarketplace\Block\Rating::class,
    'advancedmarketplace.reviewer-listing' => Apps\P_AdvMarketplace\Block\ReviewerListing::class,
    'advancedmarketplace.feed' => Apps\P_AdvMarketplace\Block\Feed::class,
    'advancedmarketplace.gmap' => Apps\P_AdvMarketplace\Block\Gmap::class,
    'advancedmarketplace.invite-list' => Apps\P_AdvMarketplace\Block\InviteList::class,
    'advancedmarketplace.purchase-popup' => Apps\P_AdvMarketplace\Block\PurchasePopup::class,
    'advancedmarketplace.detail-payment' => Apps\P_AdvMarketplace\Block\DetailPayment::class,
    'advancedmarketplace.sponsorhelp' => Apps\P_AdvMarketplace\Block\SponsorHelp::class,
    'advancedmarketplace.frontend.customfield' => Apps\P_AdvMarketplace\Block\Frontend\Customfield::class,
    'advancedmarketplace.frontend.viewcustomfield' => Apps\P_AdvMarketplace\Block\Frontend\ViewCustomfield::class,
    'advancedmarketplace.frontend.edit.combobox' => Apps\P_AdvMarketplace\Block\Frontend\Edit\Combobox::class,
    'advancedmarketplace.frontend.edit.selectcheckbox' => Apps\P_AdvMarketplace\Block\Frontend\Edit\Selectcheckbox::class,
    'advancedmarketplace.frontend.edit.selectradio' => Apps\P_AdvMarketplace\Block\Frontend\Edit\Selectradio::class,
    'advancedmarketplace.frontend.edit.textline' => Apps\P_AdvMarketplace\Block\Frontend\Edit\Textline::class,
    'advancedmarketplace.frontend.view.combobox' => Apps\P_AdvMarketplace\Block\Frontend\View\Combobox::class,
    'advancedmarketplace.frontend.view.selectcheckbox' => Apps\P_AdvMarketplace\Block\Frontend\View\Selectcheckbox::class,
    'advancedmarketplace.frontend.view.selectradio' => Apps\P_AdvMarketplace\Block\Frontend\View\Selectradio::class,
    'advancedmarketplace.frontend.view.textline' => Apps\P_AdvMarketplace\Block\Frontend\View\Textline::class,
    'advancedmarketplace.admincp.todaylisting' => Apps\P_AdvMarketplace\Block\Admin\TodayListing::class,
    'advancedmarketplace.admincp.managecustomfield' => Apps\P_AdvMarketplace\Block\Admin\ManageCustomField::class,
    'advancedmarketplace.admincp.customfield' => Apps\P_AdvMarketplace\Block\Admin\Customfield::class,
    'advancedmarketplace.admincp.customfieldcell' => Apps\P_AdvMarketplace\Block\Admin\Customfieldcell::class,
    'advancedmarketplace.admincp.customfieldgroup' => Apps\P_AdvMarketplace\Block\Admin\Customfieldgroup::class,
    'advancedmarketplace.admincp.customfieldoption' => Apps\P_AdvMarketplace\Block\Admin\Customfieldoption::class,
    'advancedmarketplace.admincp.groupcustomfields' => Apps\P_AdvMarketplace\Block\Admin\Groupcustomfields::class,
])->addComponentNames('ajax', [
    'advancedmarketplace.ajax' => Apps\P_AdvMarketplace\Ajax\Ajax::class,
])->addTemplateDirs([
    'advancedmarketplace' => PHPFOX_DIR_SITE_APPS . 'p-advmarketplace' . PHPFOX_DS . 'views'
])->addAliasNames('advancedmarketplace', 'P_AdvMarketplace');

group('/advancedmarketplace' , function () {
    route('/admincp', function () {
        auth()->isAdmin(true);
        Phpfox::getLib('module')->dispatch('advancedmarketplace.admincp.listingstatistics');
        return 'controller';
    });

    route('/', 'advancedmarketplace.index');
    route('/tag/:text','advancedmarketplace.index');
    route('/search', 'advancedmarketplace.search');
    route('/search/tag/:text','advancedmarketplace.search');
    route('/search/category/:id/:name/*', 'advancedmarketplace.search')->where([':id' => '([0-9]+)']);
    route('/add/*', 'advancedmarketplace.add');
    route('/frame-upload/', 'advancedmarketplace.frame-upload');
    route('/detail/:id/*', 'advancedmarketplace.detail')->where([':id' => '([0-9]+)']);
    route('/invoice','advancedmarketplace.invoice.index');
    route('/invoice/seller','advancedmarketplace.invoice.seller');
    route('/embed/:id/*','advancedmarketplace.embed');
});

Phpfox::getLib('setting')->setParam('advancedmarketplace.url_pic', Phpfox::getParam('core.url_pic') . 'advancedmarketplace' . PHPFOX_DS);
Phpfox::getLib('setting')->setParam('advancedmarketplace.dir_pic', Phpfox::getParam('core.dir_pic') . 'advancedmarketplace' . PHPFOX_DS);
Phpfox::getLib('setting')->setParam('advancedmarketplace.thumbnail_sizes', [50, 120, 200, 400]);
