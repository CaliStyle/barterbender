<?php


namespace Apps\YNC_Blogs\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

class IndexController extends Phpfox_Component
{
    public function process()
    {
        $aParentModule = $this->getParam('aParentModule');
        $bIsSearch = false;
        if ($this->request()->get('req2') == 'delete' && ($iDeleteId = $this->request()->getInt('req3'))) {
            if (Phpfox::getService('ynblog.permission')->canDeleteBlog($iDeleteId, true) && Phpfox::getService('ynblog.process')->deleteBlog($iDeleteId)) {
                if ($iProfileId = $this->request()->getInt('profile')) {
                    $aUser = Phpfox::getService('user')->getUser($iProfileId);
                    $this->url()->send($aUser['user_name'] . '.advanced-blog', [], _p('blog_successfully_deleted'));
                } else {
                    $this->url()->send('ynblog', [], _p('blog_successfully_deleted'));
                }
            } else {
                return Phpfox_Error::display(_p('delete_failed'));
            }
        }

        if ($aParentModule === null && $this->request()->getInt('req2') > 0) {

            if (($this->request()->get('req1') == 'pages' && Phpfox::isModule('pages') == false) ||
                ($aParentModule['module_id'] == 'pages' && Phpfox::getService('pages')->hasPerm($aParentModule['item_id'], 'blog.view_browse_blog') == false)
            ) {
                return Phpfox_Error::display(_p('cannot_display_due_to_privacy'));
            }
            return Phpfox::getLib('module')->setController('ynblog.view');
        }

        if (defined('PHPFOX_IS_USER_PROFILE') && ($sLegacyTitle = $this->request()->get('req3')) && !empty($sLegacyTitle)) {
            Phpfox::getService('core')->getLegacyItem(array(
                    'field' => array('blog_id', 'title'),
                    'table' => 'ynblog_blogs',
                    'redirect' => 'ynblog',
                    'title' => $sLegacyTitle,
                    'search' => 'title'
                )
            );
        }

        if ($this->request()->get('req2') == 'main') {
            return Phpfox::getLib('module')->setController('error.404');
        }

        (($sPlugin = Phpfox_Plugin::get('ynblog.component_controller_index_process_start')) ? eval($sPlugin) : false);

        $bIsInHomePage = $this->_checkIsInHomePage();
        if ($bIsInHomePage) {
            $bIsSearch = true;
        }

        if (($iRedirectId = $this->request()->get('redirect')) && ($aRedirectBlog = Phpfox::getService('ynblog.blog')->getBlogForEdit($iRedirectId))) {
            Phpfox::permalink('ynblog', $aRedirectBlog['blog_id'], $aRedirectBlog['title'], true);
        }

        if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            $bIsProfile = true;
            $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $aUser);
        } else {
            $bIsProfile = $this->getParam('bIsProfile');
            $aUser = $this->getParam('aUser');
            if ($bIsProfile === true) {
                $this->search()->setCondition('AND advblog.user_id = ' . $aUser['user_id']);
            }
        }

        /**
         * Check if we are going to view an actual blog instead of the blog index page.
         * The 2nd URL param needs to be numeric.
         */
        if (!Phpfox::isAdminPanel()) {
            if ($this->request()->getInt('req2') > 0 && !isset($aParentModule['module_id'])) {
                /**
                 * Since we are going to be viewing a blog lets reset the controller and get out of this one.
                 */
                return Phpfox::getLib('module')->setController('ynblog.view');
            }
        }

        /**
         * This creates a global variable that can be used in other components. This is a good way to
         * pass information to other components.
         */
        $this->setParam('sTagType', 'advblog');
        $this->setParam('bIsInHomePage', $bIsInHomePage);

        $this->template()->setTitle(($bIsProfile ? _p('full_name_s_blogs', array('full_name' => $aUser['full_name'])) : _p('advanced_blog')));

        if ($bIsProfile) {
            section(_p('Blogs'), url('/' . $aUser['user_name'] . '/advanced-blog'));
        } else {
            section(_p('Blogs'), url('/advanced-blog'));
        }

        $sView = $this->request()->get('view');

        $this->search()->set([
                'type' => 'advblog',
                'field' => 'advblog.blog_id',
                'ignore_blocked' => true,
                'search_tool' => [
                    'table_alias' => 'advblog',
                    'search' => [
                        'action' => ($aParentModule === null ? ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'], array('ynblog', 'view' => $sView)) : $this->url()->makeUrl('ynblog', array('view' => $sView))) : $aParentModule['url'] . 'ynblog?view=' . $sView),
                        'default_value' => _p('search_blogs_dot'),
                        'name' => 'search',
                        'field' => ['advblog.title']
                    ],
                    'sort' => [
                        'latest' => ['advblog.time_stamp', _p('latest')],
                        'most-viewed' => ['advblog.total_view', _p('most_viewed')],
                        'most-liked' => ['advblog.total_like', _p('most_liked')],
                        'most-talked' => ['advblog.total_comment', _p('most_discussed')]
                    ],
                    'show' => [10, 20, 30]
                ]
            ]
        );
        $aBrowseParams = array(
            'module_id' => 'ynblog',
            'alias' => 'advblog',
            'field' => 'blog_id',
            'table' => Phpfox::getT('ynblog_blogs'),
            'hide_view' => array('pending', 'my')
        );

        (($sPlugin = Phpfox_Plugin::get('ynblog.component_controller_index_process_search')) ? eval($sPlugin) : false);

        if (!defined('PHPFOX_IS_USER_PROFILE') && !isset($aParentModule['module_id'])) {

            $aFilterMenu = Phpfox::getService('ynblog.helper')->buildFilterMenu();
            $this->template()->buildSectionMenu('ynblog', $aFilterMenu);
        }

        //add button to add new group
        if (!isset($aParentModule['module_id']) && user('yn_advblog_add_blog')) {
            sectionMenu(_p('write_new'), url('/advanced-blog/add'));
        }

        switch ($sView) {
            case 'spam':
                Phpfox::isUser(true);
                if (Phpfox::getUserParam('yn_advblog_approve')) {
                    $this->search()->setCondition('AND advblog.is_approved = 9');
                }
                break;
            case 'pending':
                Phpfox::isUser(true);
                if (Phpfox::getUserParam('yn_advblog_approve')) {
                    $this->search()->setCondition('AND advblog.post_status = \'public\' AND advblog.is_approved = 0');
                }
                break;
            case 'my':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND advblog.user_id = ' . Phpfox::getUserId());
                break;
            default:
                $aPage = $this->getParam('aPage');
                $sCondition = "AND advblog.is_approved = 1 AND advblog.post_status = 'public'" . (Phpfox::getUserParam('privacy.can_comment_on_all_items') ? "" : " AND advblog.privacy IN(%PRIVACY%)");
                if (isset($aPage['privacy']) && $aPage['privacy'] == 1) {
                    $sCondition = "AND advblog.is_approved = 1 AND advblog.privacy IN(%PRIVACY%, 1) AND advblog.post_status = 'public'";
                }
                $this->search()->setCondition($sCondition);

                http_cache()->set();

                break;
        }

        $this->template()->assign(array(
            'sTypeBlock' => 'total_favorite',
            'sTypeIcon' => 'fa fa-heart-o',
            'sTypeUnit' => [
                'plural' => 'favoriters',
                'singular' => 'favoriter',
            ]
        ));

        //Advanced Search
        $aSearch = $this->request()->getArray('search');
        if (!empty($aSearch)) {
            if (isset($aSearch['author_name'])) {
                $this->search()->setCondition('AND u.full_name LIKE \'%' . $aSearch['author_name'] . '%\'');
            }

            if (!empty($aSearch['category_id']) && is_numeric($aSearch['category_id'])) {
                $this->search()->setCondition('AND acd.category_id = ' . $aSearch['category_id']);
            }

            if (!empty($aSearch['blogger_id']) && is_numeric($aSearch['blogger_id'])) {
                $this->search()->setCondition('AND advblog.user_id = ' . $aSearch['blogger_id']);
            }
        }

        if ($this->request()->get(($bIsProfile === true ? 'req3' : 'req2')) == 'category') {
            if ($aBlogCategory = Phpfox::getService('ynblog.category')->getCategory($this->request()->getInt(($bIsProfile === true ? 'req4' : 'req3')))) {
                if ($aBlogCategory['parent_id'] > 0 && $aBlogParentCategory = Phpfox::getService('ynblog.category')->getCategory($aBlogCategory['parent_id'])) {
                    if ($aBlogParentCategory['parent_id'] && $aBlogGrandParentCategory = Phpfox::getService('ynblog.category')->getCategory($aBlogParentCategory['parent_id'])) {
                        $this->template()->setBreadCrumb(Phpfox::getLib('locale')->convert(_p($aBlogGrandParentCategory['name'])), $this->url()->permalink('ynblog.category', $aBlogGrandParentCategory['category_id'], $aBlogGrandParentCategory['name']));
                    }

                    $this->template()->setBreadCrumb(Phpfox::getLib('locale')->convert(_p($aBlogParentCategory['name'])), $this->url()->permalink('ynblog.category', $aBlogParentCategory['category_id'], $aBlogParentCategory['name']));
                }

                $bIsSearchByCategory = true;

                $this->search()->setCondition('AND ac.category_id = ' . $this->request()->getInt(($bIsProfile === true ? 'req4' : 'req3')));

                $this->template()->setTitle(Phpfox::getSoftPhrase($aBlogCategory['name']));
                $this->template()->setBreadCrumb(Phpfox::getLib('locale')->convert(_p($aBlogCategory['name'])), $this->url()->makeUrl('current'), true);
                $this->template()->assign(array('bIsCategoryRequest' => true));
                $this->search()->setFormUrl($this->url()->permalink(array('ynblog.category', 'view' => $sView), $aBlogCategory['category_id'], $aBlogCategory['name']));
            }
        } elseif (Phpfox::isModule('tag') && $this->request()->get((defined('PHPFOX_IS_PAGES_VIEW') ? 'req4' : ($bIsProfile === true ? 'req3' : 'req2'))) == 'tag') {
            if (!defined('PHPFOX_GET_FORCE_REQ')) define('PHPFOX_GET_FORCE_REQ', true);

            $sTagText = $this->request()->get((defined('PHPFOX_IS_PAGES_VIEW') ? 'req5' : ($bIsProfile === true ? 'req4' : 'req3')));
            $sTagText = urldecode(Phpfox::getLib('database')->escape($sTagText));

            if ((Phpfox::getService('tag')->getTagInfo('ynblog', $sTagText))) {
                $this->template()->setBreadCrumb(_p('topic') . ': ' . $sTagText . '',
                    $this->url()->makeUrl('current'), true);
                $this->search()->setCondition('AND tag.tag_text = \'' . $sTagText . '\'');
            } else {
                $this->search()->setCondition('AND 0');
            }
        }
        if (isset($aParentModule) && isset($aParentModule['module_id'])) {
            /* Only get items without a parent (not belonging to pages) */
            $this->search()->setCondition('AND advblog.module_id = \'' . $aParentModule['module_id'] . '\' AND advblog.item_id = ' . (int)$aParentModule['item_id']);
        } else if ($aParentModule === null) {
            if (in_array($sView, ['saved', 'favorite', 'draft', 'my']) || ($sView == 'pending' && Phpfox::getUserParam('yn_advblog_approve'))) {

            } else {
                $this->search()->setCondition('AND advblog.module_id = \'ynblog\'');
            }
        }

        if (((defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm(null, 'blog.view_browse_blogs'))
            || (!defined('PHPFOX_IS_PAGES_VIEW') && $aParentModule['module_id'] == 'pages' && Phpfox::getService('pages')->hasPerm($aParentModule['item_id'], 'blog.view_browse_blogs')))
        ) {
            $sService = defined('PHPFOX_PAGES_ITEM_TYPE') ? PHPFOX_PAGES_ITEM_TYPE : 'pages';
            if (Phpfox::getService($sService)->isAdmin($aParentModule['item_id'])) {
                $this->request()->set('view', 'pages_admin');
            } elseif (Phpfox::getService($sService)->isMember($aParentModule['item_id'])) {
                $this->request()->set('view', 'pages_member');
            }
        }

        $this->search()->setContinueSearch(true);
        $this->search()->browse()->setPagingMode(Phpfox::getParam('ynblog.yn_advblog_paging_mode', 'loadmore'));
        $this->search()->browse()->params($aBrowseParams)->execute();

        $aItems = $this->search()->browse()->getRows();
        Phpfox::getLib('pager')->set(array('page' => $this->search()->getPage(), 'size' => $this->search()->getDisplay(), 'count' => $this->search()->browse()->getCount()));

        (($sPlugin = Phpfox_Plugin::get('ynblog.component_controller_index_process_middle')) ? eval($sPlugin) : false);

        $this->template()->setMeta('keywords', Phpfox::getParam('blog.blog_meta_keywords'));
        $this->template()->setMeta('description', Phpfox::getParam('blog.blog_meta_description'));
        if ($bIsProfile) {
            $this->template()->setMeta('description', '' . $aUser['full_name'] . ' has ' . $this->search()->browse()->getCount() . ' blogs.');
        }

        foreach ($aItems as $aItem) {
            $this->template()->setMeta('keywords', $this->template()->getKeywords($aItem['title']));
            if (!empty($aItem['tag_list'])) {
                $this->template()->setMeta('keywords', Phpfox::getService('tag')->getKeywords($aItem['tag_list']));
            }
        }

        /**
         * Here we assign the needed variables we plan on using in the template. This is used to pass
         * on any information that needs to be used with the specific template for this component.
         */
        $this->setParam('bIsSearch', $bIsSearch);
        $cnt = $this->search()->browse()->getCount();

        // Set pager
        $aParamsPager = array(
            'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $this->search()->browse()->getCount(),
            'paging_mode' => $this->search()->browse()->getPagingMode()
        );

        Phpfox::getLib('pager')->set($aParamsPager);

        $this->template()->assign(array(
                'iCnt' => $cnt,
                'aItems' => $aItems,
                'sSearchBlock' => _p('search_blogs_'),
                'bIsProfile' => $bIsProfile,
                'sBlogStatus' => $this->request()->get('status'),
                'sView' => $sView,
                'iShorten' => 500,
                'appPath' => Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/ync-blogs/',
                'sTagType' => ($bIsProfile === true ? 'ynblog_profile' : 'ynblog'),
                'bIsInHomePage' => $bIsInHomePage,
                'bIsSearchByCategory' => isset($bIsSearchByCategory) ? $bIsSearchByCategory : 0,
                'aForms' => $aSearch,
                'sFormUrl' => $this->search()->getFormUrl(),
                'aCategories' => Phpfox::getService('ynblog.category')->getForAdmin(),
            )
        )
            ->setHeader('cache', array(
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                )
            );

        $bShowModerator = false;
        $aModerationMenu = [];

        if ($sView == 'my') {
            $aModerationMenu[] = array(
                'phrase' => _p('Export to Wordpress'),
                'action' => 'export_wordpress'
            );

            $aModerationMenu[] = array(
                'phrase' => _p('Export to Tumblr'),
                'action' => 'export_tumblr'
            );

            $aModerationMenu[] = array(
                'phrase' => _p('Export to Blogger'),
                'action' => 'export_blogger'
            );

            $aModerationMenu[] = array(
                'phrase' => _p('Publish Selected'),
                'action' => 'publish'
            );

            $bShowModerator = true;
        }

        if ($sView == 'saved') {
            $aModerationMenu[] = array(
                'phrase' => _p('Un-Saved All'),
                'action' => 'un_saved'
            );

            $bShowModerator = true;
        }

        if ($sView == 'pending' && user('yn_advblog_approve')) {
            $aModerationMenu[] = array(
                'phrase' => _p('Approve'),
                'action' => 'approve'
            );

            $aModerationMenu[] = array(
                'phrase' => _p('Deny'),
                'action' => 'deny'
            );
            $bShowModerator = true;
        }

        if (user('yn_advblog_feature') && $sView != 'pending') {
            $aModerationMenu[] = array(
                'phrase' => _p('Feature'),
                'action' => 'feature'
            );

            $aModerationMenu[] = array(
                'phrase' => _p('Un-Feature'),
                'action' => 'unfeature'
            );

            $bShowModerator = true;
        }

        if (user('yn_advblog_delete_other')) {
            $aModerationMenu[] = array(
                'phrase' => _p('Delete'),
                'action' => 'delete'
            );
            $bShowModerator = true;
        }

        $this->setParam('global_moderation', array(
                'name' => 'ynblog',
                'ajax' => 'ynblog.moderation',
                'menu' => $aModerationMenu
            )
        );

        $this->template()->assign(['bShowModerator' => $bShowModerator]);

        if (defined('PHPFOX_CURRENT_TIMELINE_PROFILE') && PHPFOX_CURRENT_TIMELINE_PROFILE) {
            $this->template()->assign('iCurrentProfileId', PHPFOX_CURRENT_TIMELINE_PROFILE);
        }

        //Special breadcrumb for pages
        if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW && defined('PHPFOX_PAGES_ITEM_TYPE')) {
            if (Phpfox::hasCallback(PHPFOX_PAGES_ITEM_TYPE, 'checkPermission') && !Phpfox::callback(PHPFOX_PAGES_ITEM_TYPE . '.checkPermission', $aParentModule['item_id'], 'ynblog.view_browse_blogs')) {
                $this->template()->assign(['aSearchTool' => []]);
                return Phpfox_Error::display(_p('cannot_display_due_to_privacy'));
            }
            $this->template()
                ->clearBreadCrumb();
            $this->template()
                ->setBreadCrumb(Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getTitle($aParentModule['item_id']), $aParentModule['url'])
                ->setBreadCrumb(_p('ynblog'), $aParentModule['url'] . 'advanced-blog/');
        }

        (($sPlugin = Phpfox_Plugin::get('blog.component_controller_index_process_end')) ? eval($sPlugin) : false);
        return null;
    }

    private function _checkIsInHomePage()
    {
        $bIsInHomePage = false;
        $sTempView = $this->request()->get('view', false);

        if (!$sTempView && !$this->request()->get('search-id')
            && !$this->request()->get('sort')
            && !$this->request()->get('when')
            && !$this->request()->get('type')
            && !$this->request()->get('show')
            && !$this->request()->get('s')
            && !$this->request()->get('search')
            && empty($this->request()->get('req2'))
        ) {
            $bIsInHomePage = true;
        }

        return $bIsInHomePage;
    }

    public function clean()
    {
        (($sPlugin = Phpfox::getLib('plugin')->get('ynblog.controller_index_clean')) ? eval($sPlugin) : null);
    }
}
