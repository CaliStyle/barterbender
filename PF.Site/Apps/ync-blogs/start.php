<?php

/**
 * @param $offsetCount
 * @return string
 */

function ynblog_profile($aUserName)
{
    $sCustomUrl = Phpfox::getService('ynblog.helper')->getCustomURL();
    return (Phpfox::getLib('url')->makeUrl($aUserName) . $sCustomUrl);
}

function ynblog_n($number, $single, $plural, $translate = 1)
{
    if ($number == 1) {
        return $translate ? _p($single) : $single;
    } else {
        return $translate ? _p($plural) : $plural;
    }
}

Phpfox::getLib('module')->addServiceNames([
    'ynblog.category' => Apps\YNC_Blogs\Service\Category::class,
    'ynblog.process' => Apps\YNC_Blogs\Service\Process::class,
    'ynblog.browse' => Apps\YNC_Blogs\Service\Browse::class,
    'ynblog.callback' => Apps\YNC_Blogs\Service\Callback::class,
    'ynblog.cache_remove' => Apps\YNC_Blogs\Service\CacheRemove::class,
    'ynblog.blog' => Apps\YNC_Blogs\Service\Blog::class,
    'ynblog.helper' => Apps\YNC_Blogs\Service\Helper::class,
    'ynblog.permission' => Apps\YNC_Blogs\Service\Permission::class,
])->addComponentNames('controller', [
    'ynblog.admincp.add-category' => Apps\YNC_Blogs\Controller\Admin\AddCategoryController::class,
    'ynblog.admincp.category' => Apps\YNC_Blogs\Controller\Admin\CategoryController::class,
    'ynblog.admincp.manageblogs' => Apps\YNC_Blogs\Controller\Admin\ManageBlogsController::class,
    'ynblog.admincp.importcoreblogs' => Apps\YNC_Blogs\Controller\Admin\ImportCoreBlogController::class,
    'ynblog.index' => Apps\YNC_Blogs\Controller\IndexController::class,
    'ynblog.add' => Apps\YNC_Blogs\Controller\AddController::class,
    'ynblog.view' => Apps\YNC_Blogs\Controller\ViewController::class,
    'ynblog.import' => Apps\YNC_Blogs\Controller\ImportController::class,
    'ynblog.following' => Apps\YNC_Blogs\Controller\FollowingController::class,
    'ynblog.rss' => Apps\YNC_Blogs\Controller\RssController::class,
    'ynblog.profile' => Apps\YNC_Blogs\Controller\ProfileController::class,
    'ynblog.export' => Apps\YNC_Blogs\Controller\ExportController::class,
    'ynblog.embed' => Apps\YNC_Blogs\Controller\EmbedController::class,
])->addComponentNames('ajax', [
    'ynblog.ajax' => Apps\YNC_Blogs\Ajax\Ajax::class,
])->addTemplateDirs([
    'ynblog' => PHPFOX_DIR_SITE_APPS . 'ync-blogs' . PHPFOX_DS . 'views',
])->addComponentNames('block', [
    'ynblog.hot_tags' => Apps\YNC_Blogs\Block\HotTags::class,
    'ynblog.category' => Apps\YNC_Blogs\Block\Category::class,
    'ynblog.top_blogger' => Apps\YNC_Blogs\Block\TopBlogger::class,
    'ynblog.recent_comment' => Apps\YNC_Blogs\Block\RecentComment::class,
    'ynblog.tag_author' => Apps\YNC_Blogs\Block\TagAuthor::class,
    'ynblog.author' => Apps\YNC_Blogs\Block\Author::class,
    'ynblog.other_authors' => Apps\YNC_Blogs\Block\OtherAuthors::class,
    'ynblog.admin.importchoosecategory' => Apps\YNC_Blogs\Block\Admin\ImportChooseCategory::class,
    'ynblog.feed_item' => Apps\YNC_Blogs\Block\FeedItem::class,
    'ynblog.top_categories' => Apps\YNC_Blogs\Block\TopCategories::class,
    'ynblog.blog_list' => Apps\YNC_Blogs\Block\BlogList::class,
    'ynblog.rss' => Apps\YNC_Blogs\Block\Rss::class,
    'ynblog.add_category_list' => \Apps\YNC_Blogs\Block\AddCategoryList::class,
])->addAliasNames('ynblog', 'ynblog');

group('/ynblog' , function () {
    route('/admincp', function () {
        auth()->isAdmin(true);
        Phpfox::getLib('module')->dispatch('ynblog.admincp.category');

        return 'controller';
    });

    route('/admincp/add-category', 'ynblog.admincp.add-category');
    route('/admincp/category/order', function () {
        auth()->isAdmin(true);
        $ids = request()->get('ids');
        $ids = trim($ids, ',');
        $ids = explode(',', $ids);
        $values = [];
        foreach ($ids as $key => $id) {
            $values[$id] = $key + 1;
        }
        Phpfox::getService('core.process')->updateOrdering([
                'table' => 'ynblog_category',
                'key' => 'category_id',
                'values' => $values,
            ]
        );

        Phpfox::getLib('cache')->remove('ynblog', 'substr');

        return true;
    });

    route('/admincp/add-category', 'ynblog.admincp.add-category');

    route('/category/:id/:name/*', 'ynblog.index')
        ->where([':id' => '([0-9]+)']);
    route('/sub-category/:id/:name/*', 'ynblog.index')
        ->where([':id' => '([0-9]+)']);
    route('/tag/:name/*', 'ynblog.index');

    route('/', 'ynblog.index');

    route('/profile/*', 'ynblog.view');

    route('/add/*', 'ynblog.add');

    route('/import/*', 'ynblog.import');

    route('/export/*', 'ynblog.export');

    route('/embed/*', 'ynblog.embed');

    route('/following/*', 'ynblog.following');

    route('/rss/*', 'ynblog.rss');

    route('/delete/:delete', 'ynblog.index')
        ->where([':delete' => '([0-9]+)']);

    route('/:name/*', 'ynblog.view')
        ->where([':name' => '([0-9]+)'])
        ->filter(function () {
            return true;
        });


});

Phpfox::getLib('setting')->setParam('ynblog.thumbnail_sizes', [240, 500, 1024]);