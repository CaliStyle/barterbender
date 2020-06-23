<?php

namespace Apps\Core_Pages\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Pages_Pages;
use Phpfox_Url;

defined('PHPFOX') or exit('NO DICE!');

class Pages extends Phpfox_Pages_Pages
{
    private $_aPhotoPicSizes = [50, 120, 200, 500, 1024];

    /**
     * @return Facade|object
     */
    public function getFacade()
    {
        return new Facade();
    }

    public function checkIfPageUser($profileUserId)
    {
        return db()->select('COUNT(*)')
            ->from(Phpfox::getT('user'), 'u')
            ->join(Phpfox::getT('pages'), 'p', 'p.page_id = u.profile_page_id')
            ->where('u.profile_page_id = ' . (int)$profileUserId . ' AND p.item_type = 0')
            ->execute('getSlaveField');
    }

    public function clearAdminCacheWhenCompletePageClaim($claimId)
    {
        $pageId = $this->database()->select('page_id')
            ->from(Phpfox::getT('pages_claim'))
            ->where('claim_id = ' . (int)$claimId . ' AND status_id = 2')
            ->execute('getSlaveField');

        if (empty($pageId)) {
            return false;
        }
        $this->cache()->remove('pages_' . $pageId . '_admins');
    }

    /**
     * Get profile photo id
     * @param $iPageId
     * @return int|null
     */
    public function getProfilePhotoIdForPage($iPageId)
    {
        $iId = db()->select('p.photo_id')
            ->from($this->_sTable, 'pages')
            ->leftJoin(':photo', 'p', 'p.group_id = pages.page_id AND p.module_id = "pages" AND p.is_cover = 1 AND p.is_profile_photo = 1')
            ->where('pages.page_id = ' . (int)$iPageId)
            ->execute('getSlaveField');
        return $iId;
    }

    public function buildWidgets($iId)
    {
        if (!$this->getFacade()->getItems()->hasPerm($iId,
            $this->getFacade()->getItemType() . '.view_browse_widgets')) {
            return;
        }

        $aWidgets = $this->database()->select('pw.*, pwt.text_parsed AS text')
            ->from(Phpfox::getT('pages_widget'), 'pw')
            ->join(Phpfox::getT('pages_widget_text'), 'pwt', 'pwt.widget_id = pw.widget_id')
            ->where('pw.page_id = ' . (int)$iId)
            ->order('pw.ordering ASC')
            ->execute('getSlaveRows');

        foreach ($aWidgets as $aWidget) {
            $this->_aWidgetEdit[] = array(
                'widget_id' => $aWidget['widget_id'],
                'title' => $aWidget['title'],
                'is_block' => $aWidget['is_block'],
                'menu_title' => $aWidget['menu_title'],
                'url_title' => $aWidget['url_title']
            );

            if (!$aWidget['is_block']) {
                $this->_aWidgetMenus[] = array(
                    'phrase' => $aWidget['menu_title'],
                    'url' => $this->getUrl($aWidget['page_id']) . $aWidget['url_title'] . '/',
                    'landing' => $aWidget['url_title'],
                    'icon_pass' => (empty($aWidget['image_path']) ? false : true),
                    'icon' => $aWidget['image_path'],
                    'icon_server' => $aWidget['image_server_id']
                );
            }

            $this->_aWidgetUrl[$aWidget['url_title']] = $aWidget['widget_id'];

            if ($aWidget['is_block']) {
                $this->_aWidgetBlocks[] = $aWidget;
            } else {
                $this->_aWidgets[$aWidget['url_title']] = $aWidget;
            }
        }
    }

    /**
     * Get widget by ordering ASC
     * @param $iPageId
     * @param bool $bIsBlock
     * @return array
     */
    public function getWidgetsOrdering($iPageId, $bIsBlock = true)
    {
        return db()->select('*')->from(':pages_widget')->where([
            'page_id' => $iPageId,
            'is_block' => intval($bIsBlock)
        ])->order('ordering ASC')->executeRows();
    }

    /**
     * Update widget order
     * @param $iWidgetId
     * @param $iOrder
     */
    public function updateWidgetOrder($iWidgetId, $iOrder)
    {
        db()->update(':pages_widget', ['ordering' => $iOrder], ['widget_id' => $iWidgetId]);
    }

    /**
     * @param $mId
     * @return array|int|string
     */
    public function getForView($mId)
    {
        if ($this->_aPage !== null) {
            $mId = $this->_aPage['page_id'];
        }

        $pageUserId = Phpfox::getUserId();

        if (Phpfox::isModule('friend')) {
            $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f',
                "f.user_id = p.user_id AND f.friend_user_id = " . $pageUserId);
        }

        $this->_aRow = $this->database()->select('p.*, u.user_image as image_path, p.image_path as pages_image_path, u.user_id as page_user_id, p.use_timeline, pc.claim_id, pu.vanity_url, pg.name AS category_name, pg.page_type, pt.text_parsed AS text, u.full_name, ts.style_id AS designer_style_id, ts.folder AS designer_style_folder, t.folder AS designer_theme_folder, t.total_column, ts.l_width, ts.c_width, ts.r_width, t.parent_id AS theme_parent_id, p_type.name AS parent_category_name, ' . Phpfox::getUserField('u2', 'owner_'))
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('pages_text'), 'pt', 'pt.page_id = p.page_id')
            ->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
            ->join(Phpfox::getT('user'), 'u2', 'u2.user_id = p.user_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
            ->leftJoin(Phpfox::getT('pages_category'), 'pg', 'pg.category_id = p.category_id')
            ->leftJoin(Phpfox::getT('pages_type'), 'p_type', 'p_type.type_id = pg.type_id')
            ->leftJoin(Phpfox::getT('theme_style'), 'ts', 'ts.style_id = p.designer_style_id')
            ->leftJoin(Phpfox::getT('theme'), 't', 't.theme_id = ts.theme_id')
            ->leftJoin(Phpfox::getT('pages_claim'), 'pc',
                'pc.page_id = p.page_id AND pc.user_id = ' . Phpfox::getUserId())
            ->where('p.page_id = ' . (int)$mId . ' AND p.item_type = ' . $this->getFacade()->getItemTypeId())
            ->execute('getSlaveRow');

        if (!isset($this->_aRow['page_id'])) {
            return false;
        }

        $this->_aRow['is_page'] = true;
        $this->_aRow['is_liked'] = $this->isMember($this->_aRow['page_id']);
        $this->_aRow['is_admin'] = $this->isAdmin($this->_aRow);
        $this->_aRow['link'] = $this->getFacade()->getItems()->getUrl($this->_aRow['page_id'], $this->_aRow['title'], $this->_aRow['vanity_url']);

        if ($this->_aRow['reg_method'] == '2' && Phpfox::isUser()) {
            $this->_aRow['is_invited'] = (int)$this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('pages_invite'))
                ->where('page_id = ' . (int)$this->_aRow['page_id'] . ' AND invited_user_id = ' . Phpfox::getUserId())
                ->execute('getSlaveField');

            if (!$this->_aRow['is_invited']) {
                unset($this->_aRow['is_invited']);
            }
        }

        $type = $this->getFacade()->getType()->getById($this->_aRow['type_id']);
        if (empty($this->_aRow['category_name']) && $type) {
            $this->_aRow['category_name'] = $type['name'];
            $this->_aRow['category_link'] = Phpfox::permalink('pages.category', $this->_aRow['type_id'], $type['name']);
        } else {
            $this->_aRow['type_link'] = Phpfox::permalink('pages.category', $this->_aRow['type_id'], $type['name']);
            $this->_aRow['category_link'] = Phpfox::permalink('pages.sub-category', $this->_aRow['category_id'],
                $this->_aRow['category_name']);
        }

        if ($this->_aRow['page_id'] == Phpfox::getUserBy('profile_page_id')) {
            $this->_aRow['is_liked'] = true;
        }

        // Issue with like/join button
        // Still not defined
        if (!isset($this->_aRow['is_liked'])) {
            // make it false: not liked or joined yet
            $this->_aRow['is_liked'] = false;
        }

        if ($this->_aRow['app_id']) {
            if ($this->_aRow['aApp'] = Phpfox::getService('apps')->getForPage($this->_aRow['app_id'])) {
                $this->_aRow['is_app'] = true;
                $this->_aRow['title'] = $this->_aRow['aApp']['app_title'];
                $this->_aRow['category_name'] = 'App';
            }
        } else {
            $this->_aRow['is_app'] = false;
        }

        return $this->_aRow;
    }

    /**
     * Get number of items in a category
     * @param $iCategoryId
     * @param $bIsSub
     * @param $iItemType
     * @param int $iUserId
     * @param bool $bGetCount
     * @param string $sView
     * @return array|int|string
     */
    public function getItemsByCategory($iCategoryId, $bIsSub, $iItemType, $iUserId = 0, $bGetCount = false, $sView = '')
    {
        $aConds = [
            'item_type' => $iItemType
        ];

        if ($bIsSub) {
            $aConds['category_id'] = $iCategoryId;
        } else {
            $aConds['type_id'] = $iCategoryId;
        }
        switch ($sView) {
            case '':
                $aConds['view_id'] = 0;

                break;
            case 'my':
                $aConds['user_id'] = $iUserId;

                break;
            case 'friend':
                $aFriends = Phpfox::getService('friend')->getFromCache();
                if ($aFriends) {
                    $sFriendsList = implode(',', array_column($aFriends, 'user_id'));
                } else {
                    $sFriendsList = '0';
                }
                $aConds['user_id'] = [
                    'in' => $sFriendsList
                ];
                $aConds['view_id'] = 0;

                break;
            case 'pending':
                $aConds['view_id'] = 1;

                break;
            default:
                break;
        }

        if ($bGetCount) {
            return db()->select('COUNT(*)')
                ->from(':pages')
                ->where($aConds)
                ->executeField();
        } else {
            return db()->select('*')
                ->from(':pages')
                ->where($aConds)
                ->executeRows();
        }
    }

    /**
     * Get page info
     * @param $iPageId
     * @param bool $bGetParsed
     * @return string
     */
    public function getInfo($iPageId, $bGetParsed = false)
    {
        if ($bGetParsed) {
            return db()->select('text_parsed')->from(':pages_text')->where(['page_id' => $iPageId])->executeField();
        }

        return db()->select('text')->from(':pages_text')->where(['page_id' => $iPageId])->executeField();
    }

    /**
     * Get page for profile index
     * @param $iUserId
     * @param int $iLimit
     * @param bool $bNoCount
     * @param string $sConds
     * @return array
     */
    public function getForProfile($iUserId, $iLimit = 0, $bNoCount = false, $sConds = '')
    {
        $iCnt = 0;
        if ($bNoCount == false) {
            $iCnt = $this->database()->select('p.page_id')
                ->from(Phpfox::getT('like'), 'l')
                ->join(Phpfox::getT('pages'), 'p', 'p.page_id = l.item_id AND p.view_id = 0 AND p.item_type = ' . $this->getFacade()->getItemTypeId())
                ->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
                ->where('l.type_id = \'' . $this->getFacade()->getItemType() . '\' AND l.user_id = ' . (int)$iUserId . $sConds)
                ->group('p.page_id', true)// fixes displaying duplicate pages if there are duplicate likes
                ->execute('getSlaveRows');
            $iCnt = count($iCnt);
        }

        if ($iLimit) {
            $this->database()->limit($iLimit);
        }

        $aPages = $this->database()->select('p.*, pt.name as type_name, pc.name as category_name, ph.destination as cover_image_path, ph.server_id as cover_image_server_id, pu.vanity_url, u.server_id, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('like'), 'l')
            ->join(Phpfox::getT('pages'), 'p',
                'p.page_id = l.item_id AND p.view_id = 0 AND p.item_type = ' . $this->getFacade()->getItemTypeId())
            ->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
            ->leftJoin(':photo', 'ph', 'ph.photo_id = p.cover_photo_id')
            ->leftJoin(':pages_type', 'pt', 'p.type_id = pt.type_id')
            ->leftJoin(':pages_category', 'pc', 'p.category_id = pc.category_id')
            ->where('l.type_id = \'' . $this->getFacade()->getItemType() . '\' AND l.user_id = ' . (int)$iUserId . $sConds)
            ->group('p.page_id', true)// fixes displaying duplicate pages if there are duplicate likes
            ->order('l.time_stamp DESC')
            ->execute('getSlaveRows');

        foreach ($aPages as $iKey => $aPage) {
            $aPages[$iKey]['is_app'] = false;
            $aPages[$iKey]['is_user_page'] = true;
            $aPages[$iKey]['user_image'] = sprintf($aPage['image_path'], '_50_square');
            $aPages[$iKey]['url'] = $this->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
        }

        return array($iCnt, $aPages);
    }

    /**
     * Return prepared params for generating main menu of pages app
     *
     * @return array
     */
    public function getSectionMenu()
    {
        $aFilterMenu = [];
        if (!defined('PHPFOX_IS_USER_PROFILE')) {
            $iMyPagesCount = Phpfox::getService('pages')->getMyPages(true, true);
            $aFilterMenu = array(
                _p('all_pages') => '',
                _p('my_pages') . ($iMyPagesCount ? '&nbsp;<span class="my count-item">' . ($iMyPagesCount > 99 ? '99+' : $iMyPagesCount) . '</span>' : '') => 'my'
            );

            if (!Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend') && !Phpfox::getUserBy('profile_page_id')) {
                $aFilterMenu[_p('friends_pages')] = 'friend';
            }

            if (Phpfox::getUserParam('pages.can_approve_pages')) {
                $iPendingTotal = Phpfox::getService('pages')->getPendingTotal();

                if ($iPendingTotal) {
                    $aFilterMenu[_p('pending_pages') . '&nbsp;<span class="count-item">' . ($iPendingTotal > 99 ? "99+" : $iPendingTotal) . '</span>'] = 'pending';
                }
            }
        }

        return $aFilterMenu;
    }

    public function getMenu($aPage)
    {
        $sPageUrl = $this->getFacade()->getItems()->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
        $aMenus = [
            [
                'phrase' => $this->getFacade()->getPhrase('home'),
                'url' => $sPageUrl . (empty($aPage['landing_page']) ? '' : 'wall/'),
                'icon' => 'misc/comment.png',
                'landing' => 'home'
            ],
            [
                'phrase' => $this->getFacade()->getPhrase('info'),
                'url' => $sPageUrl . 'info',
                'icon' => 'misc/comment.png',
                'landing' => 'info'
            ],
            [
                'phrase' => $this->getFacade()->getPhrase('members'),
                'url' => $sPageUrl . 'members',
                'icon' => 'misc/comment.png',
                'landing' => 'members'
            ]
        ];

        $aModuleCalls = Phpfox::massCallback('getPageMenu', $aPage);
        if ($aIntegrate = storage()->get('pages_integrate')) {
            $aIntegrate = (array)$aIntegrate->value;
        }

        foreach ($aModuleCalls as $sModule => $aModuleCall) {
            if (!is_array($aModuleCall)) {
                continue;
            }
            if ($aIntegrate && array_key_exists($sModule, $aIntegrate) && !$aIntegrate[$sModule]) {
                continue;
            }
            $aMenus[] = $aModuleCall[0];
        }

        if (count($this->_aWidgetMenus)) {
            $aMenus = array_merge($aMenus, $this->_aWidgetMenus);
        }

        $sLanding = Phpfox::getService('pages')->isPage($this->request()->get('req1')) ? $this->request()->get('req2') : $this->request()->get('req3');
        if (empty($sLanding) && !empty($aPage['landing_page'])) {
            $sLanding = $aPage['landing_page'];
        }
        $sLanding = Phpfox_Url::instance()->doRewrite($sLanding);
        $bActive = false;
        foreach ($aMenus as $key => $aMenu) {
            if (empty($aMenu['menu_icon'])) {
                $aMenus[$key]['menu_icon'] = materialParseIcon($aMenu['landing']);
            }
            $sMenuLandingRewrite = Phpfox_Url::instance()->doRewrite($aMenu['landing']);
            if ($sMenuLandingRewrite !== $aMenu['landing']) {
                $aMenus[$key]['url'] = $sPageUrl . $sMenuLandingRewrite . '/';
            }
            if (!$bActive && (
                    (($sLanding == 'wall' || empty($sLanding)) && $aMenu['landing'] == 'home')
                    || (!empty($sLanding) && $sLanding == $sMenuLandingRewrite)
                )
            ) {
                $bActive = $aMenus[$key]['is_active'] = true;
            }
        }

        if ($sPlugin = Phpfox_Plugin::get('pages.service_pages_getmenu')) {
            eval($sPlugin);
        }

        return $aMenus;
    }

    public function getMembers($iPageId, $iLimit = null, $iPage = 1, $sSearch = null)
    {
        if (!Phpfox::isModule('like')) {
            return false;
        }
        $sCacheId = $this->cache()->set('pages_' . $iPageId . '_members');
        if (false === ($aPageMembers = $this->cache()->getLocalFirst($sCacheId)) || $iLimit || $sSearch) {
            $aWhere = [
                'l.type_id' => 'pages',
                'l.item_id' => intval($iPageId)
            ];

            $iOwnerId = $this->getPageOwnerId($iPageId);

            $iCnt = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('like'), 'l')
                ->where($aWhere)
                ->execute('getSlaveField');

            if ($sSearch) {
                $aWhere['u.full_name'] = ['LIKE' => "%$sSearch%"];
            }

            if ($iLimit) {
                $this->database()->limit($iPage, $iLimit);
            }

            $aPageMembers = $this->database()->select('uf.total_friend, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('like'), 'l')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = l.user_id')
                ->join(Phpfox::getT('user_field'), 'uf', 'u.user_id = uf.user_id')
                ->leftJoin(Phpfox::getT('pages'), 'p', 'p.user_id = u.user_id')
                ->where($aWhere)
                ->order('field(p.user_id,' . (int)$iOwnerId . ') DESC, u.full_name ASC')
                ->group('u.user_id')
                ->executeRows();

            if ($iLimit == null && $sSearch == null) {
                $this->cache()->saveBoth($sCacheId, $aPageMembers);
            }
            return [$iCnt, $aPageMembers];
        }
        return [count($aPageMembers), $aPageMembers];
    }

    public function getPageAdmins($iId = null, $iPage = 1, $iLimit = null, $sSearch = null)
    {
        if (!$iId) {
            return [];
        }
        $sCacheId = $this->cache()->set('pages_' . $iId . '_admins');
        if (false === ($aPageAdmins = $this->cache()->getLocalFirst($sCacheId)) || $iLimit || $sSearch) {
            $aOwnerAdmin = $aPageAdmins = [];
            if ($iPage == 1) {
                $aOwnerAdmin[] = $this->getPageOwner($iId);
                $iLimit && $iLimit--;
            }
            if ($sSearch && !empty($aOwnerAdmin) && stristr($aOwnerAdmin[0]['full_name'], $sSearch) === false) {
                $aOwnerAdmin = [];
            }

            if ($iLimit) {
                $this->database()->limit($iPage, $iLimit);
            }

            $aWhere = ['pa.page_id' => $iId];
            if ($sSearch) {
                $aWhere['u.full_name'] = ['LIKE' => "%$sSearch%"];
            }

            if (count($aOwnerAdmin) && isset($aOwnerAdmin[0]['user_id'])) {
                $aWhere[] = 'AND pa.user_id != ' . $aOwnerAdmin[0]['user_id'];
            }
            $aPageAdmins = $this->database()->select(Phpfox::getUserField())
                ->from(Phpfox::getT('pages_admin'), 'pa')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = pa.user_id')
                ->where($aWhere)
                ->executeRows();

            $aPageAdmins = array_merge($aOwnerAdmin, $aPageAdmins);

            if ($iLimit == null && $sSearch == null) {
                $this->cache()->saveBoth($sCacheId, $aPageAdmins);
            }
        }
        return $aPageAdmins;
    }

    public function getPageAdminsCount($iPageId)
    {
        $sCacheId = $this->cache()->set('pages_' . $iPageId . '_admins');
        if (false === ($aPageAdmins = $this->cache()->get($sCacheId))) {
            $aPage = $this->getPage($iPageId);
            return db()->select('COUNT(*)')->from(':pages_admin')->where([
                    'page_id' => $iPageId,
                    ' AND user_id != ' . $aPage['user_id']
                ])->executeField() + 1;
        }
        return count($aPageAdmins);
    }

    /**
     * Move items to another category
     * @param $iOldCategoryId
     * @param $iNewCategoryId
     * @param $bOldIsSub , true if old category is sub category
     * @param $bNewIsSub , true if new category is sub category
     * @param $iItemType
     */
    public function moveItemsToAnotherCategory($iOldCategoryId, $iNewCategoryId, $bOldIsSub, $bNewIsSub, $iItemType)
    {
        $aItems = Phpfox::getService('pages')->getItemsByCategory($iOldCategoryId, $bOldIsSub, $iItemType, 0, false, 'move');
        if ($bNewIsSub) {
            // get type id
            $parentCategory = Phpfox::getService('pages.category')->getById($iNewCategoryId);
            $iTypeId = $parentCategory ? $parentCategory['type_id'] : 0;
            $aUpdates = [
                'type_id' => $iTypeId,
                'category_id' => $iNewCategoryId
            ];
        } else {
            $aUpdates = [
                'type_id' => $iNewCategoryId
            ];
        }
        foreach ($aItems as $aItem) {
            db()->update(Phpfox::getT('pages'), $aUpdates, 'page_id = ' . $aItem['page_id']);
        }
    }

    /**
     * Get upload photo params, support dropzone
     *
     * @return array
     */
    public function getUploadPhotoParams()
    {
        $iMaxFileSize = Phpfox::getUserParam('pages.max_upload_size_pages');
        $iMaxFileSize = $iMaxFileSize > 0 ? $iMaxFileSize / 1024 : 0;
        $iMaxFileSize = Phpfox::getLib('file')->getLimit($iMaxFileSize);

        return [
            'max_size' => ($iMaxFileSize === 0 ? null : $iMaxFileSize),
            'type_list' => ['jpg', 'jpeg', 'gif', 'png'],
            'upload_dir' => Phpfox::getParam('pages.dir_image'),
            'upload_path' => Phpfox::getParam('pages.url_image'),
            'thumbnail_sizes' => $this->getPhotoPicSizes(),
        ];
    }

    /**
     * Get photo sizes
     *
     * @return array
     */
    public function getPhotoPicSizes()
    {
        (($sPlugin = Phpfox_Plugin::get('pages.service_pages_getphotopicsizes')) ? eval($sPlugin) : false);

        return $this->_aPhotoPicSizes;
    }

    /**
     * Get pages in the same category
     * @param $iPageid
     * @param int $iLimit
     * @return array|bool
     */
    public function getSameCategoryPages($iPageid, $iLimit = 0)
    {
        $aPage = db()->select('type_id, category_id')->from($this->_sTable)->where(['page_id' => $iPageid])->executeRow();
        if (!$aPage) {
            return false;
        }

        $iPageid && db()->limit($iLimit);

        return db()->select('p.*, pc.name as category, pu.vanity_url, u.*, ph.destination as cover_image_path, ph.server_id as cover_image_server_id')
            ->from($this->_sTable, 'p')
            ->leftJoin(':pages_category', 'pc', 'p.category_id = pc.category_id')
            ->leftJoin(':pages_url', 'pu', 'p.page_id = pu.page_id')
            ->leftJoin(':user', 'u', 'u.profile_page_id = p.page_id')
            ->leftJoin(':photo', 'ph', 'ph.photo_id = p.cover_photo_id')
            ->where("p.page_id != $iPageid AND p.type_id = $aPage[type_id] AND p.view_id = 0")
            ->order('rand()')
            ->executeRows();
    }

    public function getForEdit($iId)
    {
        $aRow = $this->database()->select('p.*, pu.vanity_url, pt.text, pc.page_type, p_type.item_type')
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('pages_text'), 'pt', 'pt.page_id = p.page_id')
            ->leftJoin(Phpfox::getT('pages_category'), 'pc', 'p.category_id = pc.category_id')
            ->leftJoin(Phpfox::getT('pages_type'), 'p_type', 'p_type.type_id = pc.type_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
            ->where('p.page_id = ' . (int)$iId . ' AND p.item_type = 0')
            ->execute('getSlaveRow');

        if (!isset($aRow['page_id'])) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('unable_to_find_the_page_you_are_trying_to_edit'));
        }

        if (!$this->isAdmin($aRow) && !Phpfox::getUserParam('pages.can_edit_all_pages')) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('you_are_unable_to_edit_this_page'));
        }

        $this->_aRow = $aRow;

        $this->getFacade()->getItems()->buildWidgets($aRow['page_id']);

        $aRow['admins'] = $this->database()->select(Phpfox::getUserField())
            ->from(Phpfox::getT('pages_admin'), 'pa')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = pa.user_id')
            ->where('pa.page_id = ' . (int)$aRow['page_id'])
            ->execute('getSlaveRows');

        $aRow['admin_ids'] = [];
        foreach ($aRow['admins'] as $aAdmin) {
            $aRow['admin_ids'][] = $aAdmin['user_id'];
        }

        $aRow['admin_ids'] = json_encode($aRow['admin_ids']);

        $aMenus = $this->getMenu($aRow);
        foreach ($aMenus as $iKey => $aMenu) {
            $aMenus[$iKey]['is_selected'] = false;
        }
        if (!empty($aRow['landing_page'])) {
            foreach ($aMenus as $iKey => $aMenu) {
                if ($aMenu['landing'] == $aRow['landing_page']) {
                    $aMenus[$iKey]['is_selected'] = true;
                }
            }
        }

        $aRow['landing_pages'] = $aMenus;

        if ($aRow['app_id']) {
            if ($aRow['aApp'] = Phpfox::getService('apps')->getForPage($aRow['app_id'])) {
                $aRow['is_app'] = true;
                $aRow['title'] = $aRow['aApp']['app_title'];
            }
        } else {
            $aRow['is_app'] = false;
        }
        if ($sPlugin = Phpfox_Plugin::get($this->getFacade()->getItemType() . '.service_pages_getforedit_1')) {
            eval($sPlugin);
            if (isset($mReturnFromPlugin)) {
                return $mReturnFromPlugin;
            }
        }
        !defined('PHPFOX_PAGES_EDIT_ID') && define('PHPFOX_PAGES_EDIT_ID', $aRow['page_id']);
        $aRow['location']['name'] = $aRow['location_name'];
        $aRow['image_path_200'] = Phpfox::getLib('image.helper')->display([
            'file' => $aRow['image_path'],
            'path' => 'pages.url_image',
            'server_id' => $aRow['image_server_id'],
            'return_url' => true,
            'suffix' => '_200_square'
        ]);

        return $aRow;
    }

    public function hasPerm($iPage = null, $sPerm)
    {
        if (defined('PHPFOX_IS_PAGES_VIEW') && $sPerm == 'pf_video.share_videos' && ($aIntegrate = storage()->get('pages_integrate'))) {
            $aIntegrate = (array)$aIntegrate->value;
            if (array_key_exists('v', $aIntegrate) && !$aIntegrate['v']) {
                return false;
            }
        }
        if (Phpfox::isAdmin()) {
            return true;
        }
        if (defined('PHPFOX_IS_PAGES_VIEW') && Phpfox::getUserParam('core.can_view_private_items')) {
            return true;
        }

        if (defined('PHPFOX_POSTING_AS_PAGE')) {
            return true;
        }

        if (empty($this->_aRow['page_id'])) {
            $this->getPage($iPage);
        }

        if (!$iPage && !empty($this->_aRow['page_id'])) {
            $iPage = $this->_aRow['page_id'];
        }

        if (empty($iPage) && empty($this->_aRow['page_id'])) {
            return false;
        }

        $aPerms = $this->getPermsForPage($iPage);
        if (isset($aPerms[$sPerm])) {
            switch ((int)$aPerms[$sPerm]) {
                case 1:
                    if (!$this->isMember($iPage)) {
                        return false;
                    }
                    break;
                case 2:
                    if (!$this->isAdmin($iPage)) {
                        return false;
                    }
                    break;
            }
        }

        return true;
    }

    public function canPurchaseSponsorItem($iItemId)
    {
        $aSponsorIds = $this->getPendingSponsor();
        return in_array($iItemId, $aSponsorIds) ? false : true;
    }

    public function getPendingSponsor()
    {
        $cacheId = $this->cache()->set('pages_pending_sponsor');
        if (false === ($aSponsorIds = $this->cache()->get($cacheId))) {
            $aSponsors = db()->select('m.page_id')
                ->from(Phpfox::getT('pages'), 'm')
                ->join(Phpfox::getT('better_ads_sponsor'), 's', 's.item_id = m.page_id')
                ->where('m.item_type = 0 AND m.is_sponsor = 0 AND s.is_custom = 2 AND s.module_id = "pages"')
                ->execute('getSlaveRows');
            $aSponsorIds = array_column($aSponsors, 'page_id');
            $this->cache()->save($cacheId, $aSponsorIds);
        }
        return $aSponsorIds;
    }


    public function getActionsPermission(&$aPage, $sView = '')
    {
        $aPage['canApprove'] = $sView == 'pending' && $aPage['view_id'] == 1 && Phpfox::getUserParam('pages.can_approve_pages');
        $aPage['canEdit'] = Phpfox::getService('pages')->isAdmin($aPage) || Phpfox::getUserParam('pages.can_edit_all_pages');
        $aPage['canDelete'] = Phpfox::getUserId() == $aPage['user_id'] || Phpfox::getUserParam('pages.can_delete_all_pages');
        $aPage['canSponsor'] = $aPage['canPurchaseSponsor'] = false;
        if (Phpfox::isAppActive('Core_BetterAds')) {
            $aPage['canSponsor'] = $aPage['view_id'] == 0 && Phpfox::getUserParam('pages.can_sponsor_pages');
            $bCanPurchaseSponsor = $this->canPurchaseSponsorItem($aPage['page_id']);
            $aPage['canPurchaseSponsor'] = $aPage['view_id'] == 0 && $aPage['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('pages.can_purchase_sponsor_pages') && $bCanPurchaseSponsor;
        }
        $aPage['canFeature'] = $aPage['view_id'] == 0 && Phpfox::getUserParam('pages.can_feature_page');
        $aPage['showItemActions'] = $aPage['canApprove'] || $aPage['canEdit'] || $aPage['canDelete'] || $aPage['canSponsor'] || $aPage['canPurchaseSponsor'] || $aPage['canFeature'];
    }

    /**
     * Check if user is login as page
     * @return bool
     */
    public function isLoginAsPage()
    {
        $iProfilePageId = Phpfox::getUserBy('profile_page_id');
        $bIsLoginAsPage = $iProfilePageId > 0 && Phpfox::getLib('pages.facade')->getPageItemType($iProfilePageId) == 'pages';

        return $bIsLoginAsPage;
    }

    public function getForEditWidget($iId)
    {
        $aWidget = $this->database()->select('pw.*, pwt.text_parsed AS text')
            ->from(Phpfox::getT('pages_widget'), 'pw')
            ->join(Phpfox::getT('pages_widget_text'), 'pwt', 'pwt.widget_id = pw.widget_id')
            ->where('pw.widget_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aWidget['widget_id'])) {
            return false;
        }

        $aPage = $this->getPage($aWidget['page_id']);

        if (!isset($aPage['page_id'])) {
            return false;
        }

        if (!$this->isAdmin($aPage) && !$this->getFacade()->getUserParam('can_moderate_pages') && !Phpfox::getUserParam('pages.can_edit_all_pages')) {
            return false;
        }

        return $aWidget;
    }

    public function getFeatured($iLimit = 4, $iCacheTime = 5)
    {
        $sCacheId = $this->cache()->set('pages_featured');
        if (($sPageIds = $this->cache()->get($sCacheId, $iCacheTime)) === false || !$iCacheTime) {
            $sPageIds = '';
            $sWhere = 'p.view_id = 0 AND p.is_featured = 1 AND p.item_type = ' . $this->getFacade()->getItemTypeId();
            $aPageIds = db()->select('p.page_id')
                ->from($this->_sTable, 'p')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
                ->where($sWhere)
                ->order('rand()')
                ->limit(Phpfox::getParam('core.cache_total'))
                ->execute('getSlaveRows');

            foreach ($aPageIds as $key => $aId) {
                if ($key != 0) {
                    $sPageIds .= ',' . $aId['page_id'];
                } else {
                    $sPageIds = $aId['page_id'];
                }
            }
            if ($iCacheTime) {
                $this->cache()->save($sCacheId, $sPageIds);
            }
        }
        if (empty($sPageIds)) {
            return [];
        }
        $aPageIds = explode(',', $sPageIds);
        shuffle($aPageIds);
        $aPageIds = array_slice($aPageIds, 0, round($iLimit * Phpfox::getParam('core.cache_rate')));
        $aPages = $this->database()->select('p.*, pt.name as type_name, pc.name as category_name, ph.destination as cover_image_path, ph.server_id as cover_image_server_id, pu.vanity_url, u.server_id, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'p')
            ->join(':user', 'u', 'u.profile_page_id = p.page_id')
            ->leftJoin(':pages_url', 'pu', 'pu.page_id = p.page_id')
            ->leftJoin(':photo', 'ph', 'ph.photo_id = p.cover_photo_id')
            ->leftJoin(':pages_type', 'pt', 'p.type_id = pt.type_id')
            ->leftJoin(':pages_category', 'pc', 'p.category_id = pc.category_id')
            ->where('p.page_id IN (' . implode(',', $aPageIds) . ')')
            ->limit($iLimit)
            ->execute('getSlaveRows');

        foreach ($aPages as $iKey => $aPage) {
            $aPages[$iKey]['is_app'] = false;
            $aPages[$iKey]['is_user_page'] = true;
            $aPages[$iKey]['user_image'] = sprintf($aPage['image_path'], '_200_square');
            $aPages[$iKey]['url'] = $this->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
        }

        shuffle($aPages);

        return $aPages;
    }

    public function getSponsored($iLimit = 4, $iCacheTime = 5)
    {
        $sCacheId = $this->cache()->set('pages_sponsored');
        if (($sPageIds = $this->cache()->get($sCacheId, $iCacheTime)) === false || !$iCacheTime) {
            $sPageIds = '';
            $sWhere = 'p.view_id = 0 AND p.is_sponsor = 1 AND s.module_id = \'pages\' AND s.is_active = 1';
            $aPageIds = db()->select('p.page_id')
                ->from($this->_sTable, 'p')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
                ->join(Phpfox::getT('better_ads_sponsor'), 's', 's.item_id = p.page_id AND s.is_custom = 3')
                ->where($sWhere)
                ->order('rand()')
                ->limit(Phpfox::getParam('core.cache_total'))
                ->execute('getSlaveRows');
            foreach ($aPageIds as $key => $aId) {
                if ($key != 0) {
                    $sPageIds .= ',' . $aId['page_id'];
                } else {
                    $sPageIds = $aId['page_id'];
                }
            }
            if ($iCacheTime) {
                $this->cache()->save($sCacheId, $sPageIds);
            }
        }
        if (empty($sPageIds)) {
            return [];
        }

        $aPageIds = explode(',', $sPageIds);
        shuffle($aPageIds);
        $aPageIds = array_slice($aPageIds, 0, round($iLimit * Phpfox::getParam('core.cache_rate')));
        $aPages = $this->database()->select('p.*, pt.name as type_name, pc.name as category_name, ph.destination as cover_image_path, ph.server_id as cover_image_server_id, pu.vanity_url, u.server_id, ' . Phpfox::getUserField() . ', s.*')
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
            ->join(Phpfox::getT('better_ads_sponsor'), 's', 's.item_id = p.page_id AND s.module_id = \'pages\' AND s.is_active = 1 AND s.is_custom = 3')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
            ->leftJoin(':photo', 'ph', 'ph.photo_id = p.cover_photo_id')
            ->leftJoin(':pages_type', 'pt', 'p.type_id = pt.type_id')
            ->leftJoin(':pages_category', 'pc', 'p.category_id = pc.category_id')
            ->where('p.page_id IN (' . implode(',', $aPageIds) . ')')
            ->limit($iLimit)
            ->execute('getSlaveRows');

        if (Phpfox::isAppActive('Core_BetterAds')) {
            $aPages = Phpfox::getService('ad')->filterSponsor($aPages);
        }
        foreach ($aPages as $iKey => $aPage) {
            $aPages[$iKey]['is_app'] = false;
            $aPages[$iKey]['is_user_page'] = true;
            $aPages[$iKey]['user_image'] = sprintf($aPage['image_path'], '_200_square');
            $aPages[$iKey]['url'] = Phpfox::getLib('url')->makeUrl('ad.sponsor', ['view' => $aPage['sponsor_id']]);
        }
        shuffle($aPages);

        return $aPages;
    }
}
