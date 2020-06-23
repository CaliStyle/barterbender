<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/6/16
 * Time: 9:19 AM
 */
defined('PHPFOX') or exit('NO DICE!');

class Ynsocialstore_Component_Controller_Store_Index extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getUserParam('ynsocialstore.can_view_store', true);
        $aParentModule = $this->getParam('aParentModule');
        $bIsHomepage = $this->_checkIsInHomePage();
        $bIsNoCompare = defined('PHPFOX_IS_PAGES_VIEW') ? true : false;
        /**
         * Check if we are going to view an actual store instead of the store index page.
         * The 3nd URL param needs to be numeric.
         */
        if (defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_PAGES_VIEW')) {
            $bIsHomepage = false;
        }

        $this->setParam('hideBlock', !$bIsHomepage);

        if (!Phpfox::isAdminPanel()) {
            if ($this->request()->getInt('req3') > 0 && !isset($aParentModule['module_id'])) {
                /**
                 * Since we are going to be viewing a ynsocialstore lets reset the controller and get out of this one.
                 */
                return Phpfox_Module::instance()->setController('ynsocialstore.store.detail');
            }
            $bIsUserProfile = $this->getParam('bIsProfile');
            $aUser = [];
            if ($bIsUserProfile) {
                $aUser = $this->getParam('aUser');
            }
        }

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_controller_store_index_process_start')) ? eval($sPlugin) : false);

        $this->template()
            ->setBreadCrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'));
        $this->search()->set(Phpfox::getService('ynsocialstore.helper')->getParamsSearchStore($aParentModule, $bIsUserProfile, $aUser));

        $this->_checkIsThisACategoryRequestAndHandleIt();

        $bIsAdvSearch = false;

        if ($this->request()->get('flag_advancedsearch')) {
            $bIsAdvSearch = true;
        }


        // search ajax
        $bA = Phpfox_Request::instance()->get("bIsAdvSearch");

        if ($bA) {
            if ($this->search()->getPage() > 1) {
                $aVals = $_SESSION[Phpfox::getParam('core.session_prefix') . "ynsocialstore_searchAdv"];
                $bIsAdvSearch = true;

            }
        }

        if ($bIsAdvSearch) {
            $oServiceStore = Phpfox::getService('ynsocialstore');

            if ($this->search()->getPage() <= 1) {
                $aVals = $oServiceStore->getAdvSearchConditions();
                $_SESSION[Phpfox::getParam('core.session_prefix') . "ynsocialstore_searchAdv"] = $aVals;
            }
            $aVals['location_address'] = urldecode($aVals['location_address']);
            $oServiceStore->setAdvSearchConditions($aVals);

            $this->template()->assign(array(
                'aForms' => $aVals,
            ));
        } else {
            $this->template()->assign(array(
                'aForms' => array(),
            ));
        }

        if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            $bIsProfile = true;
            $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $aUser);
        } else {
            $bIsProfile = $this->getParam('bIsProfile');
            if ($bIsProfile === true) {
                $aUser = $this->getParam('aUser');
            }
        }

        $this->template()->assign(array(
            'apiKey' => Phpfox::getParam('core.google_api_key'),
            'bIsNoCompare' => $bIsNoCompare
        ));

        $aBrowseParams = array(
            'module_id' => 'ynsocialstore',
            'alias' => 'st',
            'field' => 'store_id',
            'table' => Phpfox::getT('ynstore_store'),
            'hide_view' => array('my')
        );

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_controller_store_index_process_search')) ? eval($sPlugin) : false);

        $sView = $this->request()->get('view');
        switch ($sView) {
            case 'favorite':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND stf.user_id = ' . Phpfox::getUserId() . '  AND st.status IN ("public","closed")');
                break;
            case 'follow':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND stfl.user_id = ' . Phpfox::getUserId() . '  AND st.status IN ("public","closed")');
                break;
            case 'featured':
                $this->search()->setCondition('AND st.module_id like \'ynsocialstore\' AND st.is_featured = 1  AND st.status = \'public\'');
                break;
            case 'pending':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND st.status = \'pending\'');
                break;
            default:
                if (($this->request()->get('req1') == 'pages' && Phpfox::isModule('pages') && $this->request()->getInt('req2') > 0)) {
                    $this->search()->setCondition('AND st.status = "public" AND st.module_id like "pages" AND st.item_id =' . $this->request()->getInt('req2'));
                    break;
                }
                if (defined('PHPFOX_IS_USER_PROFILE')) {
                    $this->search()->setCondition('AND st.status = \'public\' AND st.module_id = \'ynsocialstore\' AND st.user_id=' . intval($aUser['user_id']));
                    break;
                }
                $sCondition = "AND st.status = 'public' AND st.module_id like 'ynsocialstore' AND st.privacy IN (0)";
                $this->search()->setCondition($sCondition);
                break;
        }

        $this->search()->setContinueSearch(true);
        $this->search()->browse()->setPagingMode(Phpfox::getParam('ynsocialstore.ynstore_paging_mode', 'loadmore'));
        $this->search()->browse()->params($aBrowseParams)->execute();
        $aStore = $this->search()->browse()->getRows();

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_controller_store_index_process_middle')) ? eval($sPlugin) : false);

        foreach ($aStore as $key => $itemStore) {
            $aStore[$key]['time_stamp'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $itemStore['time_stamp']);
            $aStore[$key]['is_favorite'] = Phpfox::getService('ynsocialstore.favourite')->isFavorite(Phpfox::getUserId(), $itemStore['store_id']);
            $aStore[$key]['is_following'] = Phpfox::getService('ynsocialstore.following')->isFollowing(Phpfox::getUserId(), $itemStore['store_id']);
        }

        if ($this->search()->isSearch()) {
            $aSearch = $this->request()->get('search');
            $this->setParam('aSearch', array(
                    'when' => $this->request()->get('when', 'all-time'),
                    'show' => $this->search()->getDisplay(),
                    'sort' => $this->request()->get('sort', 'latest'),
                    'search' => isset($aSearch['search']) ? $aSearch['search'] : $this->request()->get('keywords'),
                )
            );
        }

        $this->template()
            ->setHeader('cache', array(
                    'owl.carousel.min.js' => 'module_ynsocialstore',
                    'owl.carousel.css' => 'module_ynsocialstore',
                )
            )
            ->assign(array(
                    'aItems' => $aStore,
                    'sView' => $sView,
                    'bIsHomepage' => $bIsHomepage,
                    'iPage' => $this->search()->getPage(),
                )
            );

        Phpfox::getLib('pager')->set(array(
            'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $this->search()->browse()->getCount(),
            'paging_mode' => $this->search()->browse()->getPagingMode()
        ));

        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
        Phpfox::getService('ynsocialstore.helper')->buildMenu();

        $aModerationMenu = [];
        $bShowModeration = false;
        if (Phpfox::getUserParam('ynsocialstore.can_delete_other_user_store')) {
            $aModerationMenu[] = [
                'phrase' => _p('core.delete'),
                'action' => 'delete'
            ];
            $bShowModeration = true;
        }

        if (Phpfox::getUserParam('ynsocialstore.can_approve_store') && $sView == 'pending') {
            $aModerationMenu[] = [
                'phrase' => _p('approve'),
                'action' => 'approve'
            ];

            $aModerationMenu[] = [
                'phrase' => _p('deny'),
                'action' => 'deny'
            ];

            $bShowModeration = true;
        }

        if (Phpfox::getUserParam('ynsocialstore.can_feature_store') && $sView == 'featured') {
            $aModerationMenu[] = [
                'phrase' => _p('un_featured'),
                'action' => 'unfeature'
            ];

            $bShowModeration = true;
        }

        if ($sView == 'favorite') {
            $aModerationMenu[] = [
                'phrase' => _p('un_favorite'),
                'action' => 'unfavorite'
            ];

            $bShowModeration = true;
        }

        if ($sView == 'follow') {
            $aModerationMenu[] = [
                'phrase' => _p('un_follow'),
                'action' => 'unfollow'
            ];

            $bShowModeration = true;
        }

        $this->template()->assign(array(
                'bShowModeration' => $bShowModeration,
            )
        );

        $this->setParam('global_moderation', [
            'name' => 'ynsocialstore',
            'ajax' => 'ynsocialstore.moderation',
            'menu' => $aModerationMenu
        ]);
        //Special breadcrumb for pages
        if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW && defined('PHPFOX_PAGES_ITEM_TYPE')) {
            if (Phpfox::hasCallback(PHPFOX_PAGES_ITEM_TYPE, 'checkPermission') && !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm($aParentModule['item_id'], 'ynsocialstore.view_browse_stores')) {
                $this->template()->assign(['aSearchTool' => []]);
                return \Phpfox_Error::display(_p('Cannot display this section due to privacy.'));
            }
            $this->template()
                ->clearBreadCrumb();
            $this->template()
                ->setBreadCrumb(Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getTitle($aParentModule['item_id']), $aParentModule['url'])
                ->setBreadCrumb(_p('social_store'), $aParentModule['url'] . 'ynsocialstore/');
        }
        else {
            $canCreateProduct = Phpfox::getService('ynsocialstore')->checkUserStores();
            if($canCreateProduct) {
                sectionMenu(_p('ynsocialstore_sell_new_product'), 'social-store/add');
            }
            sectionMenu(_p('ynsocialstore_open_new_store'), 'social-store/store/storetype');
        }

        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.component_controller_store_index_process_end')) ? eval($sPlugin) : false);
    }

    private function _checkIsInHomePage()
    {
        $bIsInHomePage = false;
        $aParentModule = $this->getParam('aParentModule');
        $sTempView = $this->request()->get('view', false);

        if ($sTempView == "" && !isset($aParentModule['module_id']) && !$this->request()->get('search-id')
            && !$this->request()->get('bIsAdvSearch')
            && !$this->request()->get('sort')
            && !$this->request()->get('when')
            && !$this->request()->get('type')
            && !$this->request()->get('show')
            && !$this->request()->get('search')
            && $this->request()->get('req3') == ''
        ) {
            if (!defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW')) {
                $bIsInHomePage = true;
            }
        }

        return $bIsInHomePage;
    }

    private function _checkIsThisACategoryRequestAndHandleIt()
    {
        // check category request and set corresponding condition
        if ($this->request()->get('req3') == 'category') {
            $this->template()->setBreadCrumb(_p('all_stores'), $this->url()->makeUrl('ynsocialstore.store'));
            if ($aCategory = Phpfox::getService('ecommerce.category')->getForEdit($this->request()->getInt('req4'))) {
                if ($aParentCategory = Phpfox::getService('ecommerce.category')->getForEdit($aCategory['parent_id'])) {
                    if ($aGrandParentCategory = Phpfox::getService('ecommerce.category')->getForEdit($aParentCategory['parent_id'])) {
                        $sPhrase = (\Core\Lib::phrase()->isPhrase($aGrandParentCategory['title']) ? _p($aGrandParentCategory['title']) : Phpfox::getLib('locale')->convert($aGrandParentCategory['title']));
                        $this->template()->setBreadCrumb($sPhrase, $this->url()->permalink(array('ynsocialstore.store.category', 'view' => $this->request()->get('view')), $aGrandParentCategory['category_id'], $sPhrase));
                    }

                    $sPhrase = (\Core\Lib::phrase()->isPhrase($aParentCategory['title']) ? _p($aParentCategory['title']) : Phpfox::getLib('locale')->convert($aParentCategory['title']));
                    $this->template()->setBreadCrumb($sPhrase, $this->url()->permalink(array('ynsocialstore.store.category', 'view' => $this->request()->get('view')), $aParentCategory['category_id'], $sPhrase));
                }

                $iCategory = $this->request()->getInt('req4');
                $this->_setConditionByCategory($iCategory);

                $sPhrase = (\Core\Lib::phrase()->isPhrase($aCategory['title']) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title']));
                $this->template()->setTitle($sPhrase);
                $this->template()->setBreadCrumb($sPhrase, $this->url()->makeUrl('current'), true);
                $this->template()->assign(array('bIsCategoryHandle' => true));

                $this->search()->setFormUrl($this->url()->permalink(array('ynsocialstore.store.category', 'view' => $this->request()->get('view')), $aCategory['category_id'], $aCategory['title']));
            }
        }
    }

    private function _setConditionByCategory($iCategory)
    {
        $sCategories = $iCategory;

        $sChildIds = Phpfox::getService('ecommerce.category')->getChildIds($iCategory);
        if (!empty($sChildIds)) {
            $sCategories .= ',' . $sChildIds;
        }

        $this->search()->setCondition('AND ecd.category_id IN(' . $sCategories . ')');
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynsocialstore.ynsocialstore_component_controller_store_index_clean')) ? eval($sPlugin) : false);
    }
}