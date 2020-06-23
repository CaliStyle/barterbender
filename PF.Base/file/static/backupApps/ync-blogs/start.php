<?php

/**
 * @param $offsetCount
 * @return string
 */
function ynblog_mode_view_blog_format($offsetCount){
    $totalCount =  count(Phpfox::getLib('template')->getVar('aItems'));
    $MAX = 5;
    $total_row =  ceil($totalCount/$MAX);
    $current_row = ceil($offsetCount/$MAX);
    $current_offset =  ($offsetCount - 1)%$MAX;
    $current_row_total = $current_row < $total_row ? $MAX : ($totalCount - ($total_row - 1) * $MAX);
    return 'row_number_'.($current_row%2).' item_'. $current_offset.' row_total_'. $current_row_total;
}

function ynblog_profile($aUserName) {
    return Phpfox::getLib('url')->makeUrl('profile', $aUserName).'advanced-blog';
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
    'ynblog.admincp.category'     => Apps\YNC_Blogs\Controller\Admin\CategoryController::class,
    'ynblog.admincp.manageblogs'     => Apps\YNC_Blogs\Controller\Admin\ManageBlogsController::class,
    'ynblog.admincp.importcoreblogs'     => Apps\YNC_Blogs\Controller\Admin\ImportCoreBlogController::class,
    'ynblog.index'     => Apps\YNC_Blogs\Controller\IndexController::class,
    'ynblog.add'     => Apps\YNC_Blogs\Controller\AddController::class,
    'ynblog.view'     => Apps\YNC_Blogs\Controller\ViewController::class,
    'ynblog.import'     => Apps\YNC_Blogs\Controller\ImportController::class,
    'ynblog.following'     => Apps\YNC_Blogs\Controller\FollowingController::class,
    'ynblog.rss'     => Apps\YNC_Blogs\Controller\RssController::class,
    'ynblog.profile'     => Apps\YNC_Blogs\Controller\ProfileController::class,
    'ynblog.export'     => Apps\YNC_Blogs\Controller\ExportController::class,
    'ynblog.embed'     => Apps\YNC_Blogs\Controller\EmbedController::class,
    'ynblog.frame-upload' => Apps\YNC_Blogs\Controller\FrameUploadController::class,
])->addComponentNames('ajax', [
    'ynblog.ajax' => Apps\YNC_Blogs\Ajax\Ajax::class,
])->addTemplateDirs([
    'ynblog' => PHPFOX_DIR_SITE_APPS . 'ync-blogs' . PHPFOX_DS . 'views',
])->addComponentNames('block', [
    'ynblog.related-blog' => Apps\YNC_Blogs\Block\RelatedBlog::class,
    'ynblog.recent_posts' => Apps\YNC_Blogs\Block\RecentPosts::class,
    'ynblog.most_favorite' => Apps\YNC_Blogs\Block\MostFavorite::class,
    'ynblog.most_favorite_left' => Apps\YNC_Blogs\Block\MostFavoriteLeft::class,
    'ynblog.hot_tags' => Apps\YNC_Blogs\Block\HotTags::class,
    'ynblog.category' => Apps\YNC_Blogs\Block\Category::class,
    'ynblog.hot_blogger' => Apps\YNC_Blogs\Block\HotBlogger::class,
    'ynblog.recent_comment' => Apps\YNC_Blogs\Block\RecentComment::class,
    'ynblog.most_read' => Apps\YNC_Blogs\Block\MostRead::class,
    'ynblog.most_discussed' => Apps\YNC_Blogs\Block\MostDiscussed::class,
    'ynblog.tag_author' => Apps\YNC_Blogs\Block\TagAuthor::class,
    'ynblog.author' => Apps\YNC_Blogs\Block\Author::class,
    'ynblog.other_authors' => Apps\YNC_Blogs\Block\OtherAuthors::class,
    'ynblog.admin.importchoosecategory' => Apps\YNC_Blogs\Block\Admin\ImportChooseCategory::class,
    'ynblog.feed_item' => Apps\YNC_Blogs\Block\FeedItem::class,
    'ynblog.featured_blog' => Apps\YNC_Blogs\Block\FeaturedBlog::class,
    'ynblog.same_author' => Apps\YNC_Blogs\Block\SameAuthor::class
])->addAliasNames('ynblog', 'ynblog');

group('/advanced-blog', function () {
    route('/admincp', function () {
        auth()->isAdmin(true);
        Phpfox::getLib('module')->dispatch('ynblog.admincp.manageblogs');

        return 'controller';
    });

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

    route('/frame-upload/', 'ynblog.frame-upload');

    route('/delete/:delete', 'ynblog.index')
        ->where([':delete' => '([0-9]+)']);

    route('/:name/*', 'ynblog.view')
        ->where([':name' => '([0-9]+)'])
        ->filter(function (){
            return true;
        });


});

// ((new Apps\YNC_Blogs\Install())->processInstall());