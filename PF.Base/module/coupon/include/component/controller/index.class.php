<?php

defined('PHPFOX') or exit('NO DICE!');

class Coupon_Component_Controller_Index extends Phpfox_Component {

    /**
     * it is true if we are in profile of an user
     *
     * @var boolean
     */
    private $_bIsProfile = false;

    /**
     * this array contains all information about the user we are viewing profile
     *
     * @var array
     */
    private $_aProfileUser = array();

    /**
     *  an array contains information of parent module which calls this controller
     * array(
     *  'module_id' => string 'pages',
     *  'item_id' => string '1',
     *  'url' => string 'http://minhta.younetco.com/minhta/index.php?do=/pages/1/'
     * )
     *
     * @var array
     */
    private $_aParentModule = null;

    /**
     * array of featured campaign
     *
     * @var array
     */
    private $_aFeaturedCoupons = array();

    /**
     * this variable hold the status request, it will narrow search result base on the status
     *
     * @var int
     */
    private $_iStatus = 1;

    private function _checkIsInAjaxControllerAndInUserProfile() {
        if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            $this->_bIsProfile = true;
            $this->_aProfileUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $this->_aProfileUser);
        } else {
            $this->_bIsProfile = $this->getParam('bIsProfile');
            if ($this->_bIsProfile === true) {
                $this->_aProfileUser = $this->getParam('aUser');
            }
        }
    }

    /**
     * check if we are in home page or profile page
     * @return bool
     */
    private function _checkIsInHomePage() {
        $bIsInHomePage = false;
        $aParentModule = $this->getParam('aParentModule');
        $sTempView = $this->request()->get('view', false);
        if ($sTempView == "" && !isset($aParentModule['module_id']) && !$this->request()->get('search-id')
            && !$this->request()->get('sort')
            && !$this->request()->get('show')
            && $this->request()->get('req2') == '' && !$this->request()->get('s')) {
            if (!defined('PHPFOX_IS_USER_PROFILE')) {
                $bIsInHomePage = true;
            }
        }

        return $bIsInHomePage;
    }

    private function _checkIsInPageModule()
    {
        $bIsInPages = FALSE;
        if($this->request()->get('req1') == 'pages')
        {
            $bIsInPages = TRUE;
        }       
        return $bIsInPages;
    }
    
    private function _checkAndSetStatusRequest() {
        $this->_iStatus = 2;
        $sStatus = $this->request()->get('status');
        if ($sStatus != '') {
            $this->_iStatus = (int) $sStatus;
        }
    }


    private function _checkIsThisACategoryRequestAndHandleIt() {

        // check category request and set corresponding condition
        if ($this->request()->get(($this->_bIsProfile === true ? 'req3' : 'req2')) == 'category') {
            if ($aCouponCategory = Phpfox::getService('coupon.category')->getForEdit($this->request()->getInt(($this->_bIsProfile === true ? 'req4' : 'req3')))) {
                if($aParentCategory = Phpfox::getService('coupon.category')->getForEdit($aCouponCategory['parent_id']))
                {
                    $this->template()->setBreadCrumb(((\Core\Lib::phrase()->isPhrase($aParentCategory['title'])) ? _p($aParentCategory['title']) : Phpfox::getLib('locale')->convert($aParentCategory['title'])), $this->url()->permalink(array('coupon.category', 'view' => $this->request()->get('view')), $aParentCategory['category_id'], $aParentCategory['title']));
                }
                
                $iCategory = $this->request()->getInt(($this->_bIsProfile === true ? 'req4' : 'req3'));
                $this->_setConditionByCategory($iCategory);

                $this->template()->setTitle(((\Core\Lib::phrase()->isPhrase($aCouponCategory['title'])) ? _p($aCouponCategory['title']) : Phpfox::getLib('locale')->convert($aCouponCategory['title'])));
                $this->template()->setBreadCrumb(((\Core\Lib::phrase()->isPhrase($aCouponCategory['title'])) ? _p($aCouponCategory['title']) : Phpfox::getLib('locale')->convert($aCouponCategory['title'])), $this->url()->makeUrl('current'), true);

                $this->search()->setFormUrl($this->url()->permalink(array('coupon.category', 'view' => $this->request()->get('view')), $aCouponCategory['category_id'], $aCouponCategory['title']));
            }
        }

        // check
        if (($this->request()->get(($this->_bIsProfile === true ? 'req3' : 'req2')) !== 'tag') && !$this->_bIsProfile && !$this->search()->isSearch() && $this->_aParentModule === null && !isset($aCouponCategory)) {
            $this->_aFeaturedCoupons = array(true);
        }
    }

    private function _setGlobalModeration($aMenu) {
            $this->setParam('global_moderation', array(
                    'name' => 'coupon',
                    'ajax' => 'coupon.moderation',
                    'menu' => $aMenu,
                )
            );
    }

    private function _setMetaAndKeywordsOfPage($aItems) {
        $this->template()->setMeta('keywords', Phpfox::getParam('coupon.coupon_meta_keywords'));
        $this->template()->setMeta('description', Phpfox::getParam('coupon.coupon_meta_description'));
        if ($this->_bIsProfile) {
            $this->template()->setMeta('description', '' . $this->_aProfileUser['full_name'] . ' has ' . $this->search()->browse()->getCount() . ' coupons.');
        }

        foreach ($aItems as $iKey => $aItem) {
            $this->template()->setMeta('keywords', $this->template()->getKeywords($aItem['title']));
        }
    }

    /**
     * @return array param to browse campaign list
     */
    private function _initializeSearchParams() {
        $this->search()->set(array(
                'type' => 'coupon',
                'field' => 'c.coupon_id',
                'search_tool' => array(
                    'table_alias' => 'c',
                    'search' => array(
                        'action' => $this->_aParentModule != null ? $this->url()->makeUrl($this->_aParentModule['module_id'], array($this->_aParentModule['item_id'], 'coupon')) : ($this->_bIsProfile === true ? $this->url()->makeUrl($this->_aProfileUser['user_name'], array('coupon', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('coupon', array('view' => $this->request()->get('view')))),
                        'default_value' => _p('search_coupons_dot'),
                        'name' => 'search',
                        'field' => array('c.title', 'ct.description'),
                    ),
                    'sort' => array(
                        'latest' => array('c.start_time', _p('latest')),
                        'most-claimed' => array('c.total_claim', _p('most_claimed')),
                        'most-comment' => array('c.total_comment', _p('most_comment')),
                        'most-popular' => array('c.total_view', _p('most_popular')),
                        'most-liked' => array('c.total_like', _p('most_liked')),
                    ),
                    'show' => array(12, 24, 36),
                )
            )
        );

        $aBrowseParams = array(
            'module_id' => 'coupon',
            'alias' => 'c',
            'field' => 'coupon_id',
            'table' => Phpfox::getT('coupon'),
            'hide_view' => array('pending', 'my'),
        );

        return $aBrowseParams;
    }
    
    /**
     * Adv search
     * @by: annt
     */
    private function _setAdvSearchConditions()
    {
        $sKeyword = $this->search()->get('keyword');
        $aCategory = $this->search()->get('category');
        $sCity = $this->search()->get('city');
        $sCountry = $this->search()->get('country_iso');
        $sCountryChildId = $this->search()->get('country_child_id');
        $aReturn = [];

        $aForms = array(
            'keyword' => $sKeyword,
            'category' => $aCategory,
            'city' => $sCity,
            'country_iso' => $sCountry
        );
 
        $this->template()->assign('aForms', $aForms);
        
        if (!empty($sKeyword))
        {
            $this->search()->setCondition('AND c.title LIKE "%'.$sKeyword.'%"');
        }
        
        $aCategory = array_filter(array_unique($aCategory));
        
        if (is_array($aCategory) && !empty($aCategory))
        {
            $iChild = $aCategory[0];
            foreach ($aCategory as $k => $iCategory)
            {
                if (Phpfox::getService('coupon.category')->isChild($iCategory, $iChild))
                {
                    $iChild = $iCategory;
                }
            }
            
            $this->_setConditionByCategory($iChild);
            
            $this->setParam('category', $iChild);
            $aReturn['category'] = $iChild;
        }
        
        if (!empty($sCity))
        {
            $this->search()->setCondition('AND c.city LIKE "%'.$sCity.'%"');
        }
        
        if (!empty($sCountry) && $sCountry != '-1')
        {
            $this->search()->setCondition('AND c.country_iso = "'.$sCountry.'"');
            $this->setParam('country_iso', $sCountry);
            $aReturn['country_iso'] = $sCountry;
        }


         if (!empty($sCountryChildId) && $sCountryChildId != '-1')
        {
            $this->search()->setCondition('AND c.country_child_id = "'.$sCountryChildId.'"');
            $this->setParam('country_child_id', $sCountryChildId);
            $aReturn['country_child_id'] = $sCountryChildId;
        }
        return $aReturn;
    }
    
    private function _setConditionByCategory($iCategory)
    {
        $sCategories = $iCategory;
        
        $sChildIds = Phpfox::getService('coupon.category')->getChildIds($iCategory);
        if (!empty($sChildIds))
        {
            $sCategories .= ','.$sChildIds;
        }
        
        $this->search()->setCondition('AND ccd.category_id IN(' . $sCategories . ')');
    }    

    /**
     * build index left section menu
     * @by: datlv
     */
    private function _buildSubsectionMenu()
    {
        if ($this->_aParentModule === null && !defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW')) {
            $aFilterMenu = array(
                _p('all_coupons') => ''
            );

            if (Phpfox::isUser()) {
                $aFilterMenu[_p('my_coupons')] = 'my';
            }
            
            if (!Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend')) {
                $aFilterMenu[_p('friend_s_coupons')] = 'friend';
            }
            
            if (Phpfox::isAdmin())
            {
                $iTotalPending = Phpfox::getService('coupon')->getTotalPending();
				if ($iTotalPending)
                	$aFilterMenu[_p('pending_coupon') . '<span class="pending count-item">' . $iTotalPending . '</span>']     = 'pending';
            }

            $aFilterMenu[] = TRUE;
           if (Phpfox::isUser()) {
                $aFilterMenu[_p('my_claim_coupons')]   = 'my_claims';
                $aFilterMenu[_p('my_favorite_coupon')] = 'favorite';
                $aFilterMenu[_p('my_following_coupon')]= 'following';
            }

            $aFilterMenu[_p('featured_coupon')] = 'featured';
            $aFilterMenu[_p('upcoming_coupon')] = 'upcoming';
            $aFilterMenu[_p('ending_soon_coupon')] = 'endingsoon';
            $aFilterMenu[_p('faq_s')] = 'faq';

            Phpfox::getLib('template')->buildSectionMenu('coupon', $aFilterMenu);
        }
    }

    /**
     *
     * @param string $sView name of the view we are going to see
     */
    private function _setConditionAndHandleDefaultView($sView) {
        if ($this->_bIsProfile === true) {

            $this->search()->setCondition("AND c.is_removed = 0 AND c.module_id = 'coupon' AND c.user_id = " . $this->_aProfileUser['user_id']);

            if($this->_aProfileUser['user_id'] != Phpfox::getUserId() && !Phpfox::isAdmin())
            {
                $this->search()->setCondition(" AND c.is_approved IN(" . ($this->_aProfileUser['user_id'] == Phpfox::getUserId() ? '0,1' : '1') . ") AND c.privacy IN(" . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('core')->getForBrowse($this->_aProfileUser)) . ")");
            }
        } else if ($this->_aParentModule != null && defined('PHPFOX_IS_PAGES_VIEW')) {

            $this->search()->setCondition("AND c.module_id = '" . $this->_aParentModule['module_id'] . "' AND c.item_id  = " . $this->_aParentModule['item_id'] . " AND c.privacy IN(%PRIVACY%) AND c.is_removed = 0");

            if(Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->isAdmin($this->_aParentModule['item_id']) || Phpfox::isAdmin())
            {
                /**
                 * TODO: improve next version that page admin or site admin can do something with coupon in his page
                 * now just see like normal user in pages.
                 */
                $this->search()->setCondition("AND ( (c.is_approved = 1 && c.is_draft = 0) || c.user_id = " . Phpfox::getUserId() . ")");
            }
            else
            {
                $this->search()->setCondition("AND ( (c.is_approved = 1 && c.is_draft = 0) || c.user_id = " . Phpfox::getUserId() . ")");
            }

        } else {
            $this->search()->setCondition("AND c.module_id = 'coupon' AND c.privacy IN(%PRIVACY%) ");
        }
        if (!$this->_bIsProfile && $this->_aParentModule == null && !defined('PHPFOX_IS_PAGES_VIEW')) {
            $this->search()->setCondition("AND c.is_approved = 1 AND c.is_removed = 0");
        }

        if (!($this->search()->isSearch())  && !$this->_bIsProfile && $sView != 'friend' && !($this->_aParentModule != null && defined('PHPFOX_IS_PAGES_VIEW'))) {
            /*
             * @todo modify later
             */
        }

        if ($sView == 'friend' && $this->request()->get('status') == '') {
            $this->_iStatus = 0;
        }
    }

    private function _setConditionAndHandleMyView() {
        Phpfox::isUser(true);
        $this->search()->setCondition(' AND c.is_removed = 0 AND c.status > 0  AND c.user_id = ' . Phpfox::getUserId());

        if ($this->request()->get('status') == '') {
            $this->_iStatus = 0;
        }
    }

    private function _setConditionAndHandleMyClaimedView() {
        Phpfox::isUser(true);
        $this->search()->setCondition(' AND c.is_removed = 0 AND cc.user_id = ' . Phpfox::getUserId());

        if ($this->request()->get('status') == '') {
            $this->_iStatus = 0;
        }
    }

    private function _setConditionAndHandleFriendView() {
        Phpfox::isUser(true);
        $this->search()->setCondition('AND c.module_id = "coupon" AND c.is_removed = 0 AND c.is_closed = 0 AND c.is_approved = 1 AND c.privacy IN(%PRIVACY%)');
    }

    private function _setConditionAndHandleFavoriteView() {
        $this->search()->setCondition(' AND c.is_removed = 0 AND cf.user_id = '.Phpfox::getUserId().' AND c.is_approved = 1');
    }
    
    private function _setConditionAndHandleFollowingView() {
        $this->search()->setCondition(' AND c.is_removed = 0 AND fo.user_id = '.Phpfox::getUserId().' AND c.is_approved = 1');
    }

    private function _setConditionAndHandleFeaturedView() {
        $this->search()->setCondition(' AND c.is_removed = 0 AND c.is_approved = 1 AND c.is_closed = 0 AND c.is_featured = 1 AND c.privacy IN(%PRIVACY%)');
    }

    private function _setConditionAndHandlePendingView() {
        Phpfox::isUser(true);
        if (Phpfox::getUserParam('coupon.can_approve_coupon')) {
            $this->search()->setCondition('AND c.module_id = "coupon" AND c.is_approved = 0 AND c.status = ' . Phpfox::getService('coupon')->getStatusCode('pending'));
        }
    }
    
    private function _setConditionAndHandleUpcomingView() {
        $this->search()->setCondition(' AND c.is_removed = 0 AND  c.privacy IN(%PRIVACY%) AND c.module_id = "coupon" AND  c.status = ' . Phpfox::getService('coupon')->getStatusCode('upcoming'));
    }

    private function _setConditionAndHandleEndingSoonView() {
        $this->search()->setCondition(' AND c.is_removed = 0 AND  c.privacy IN(%PRIVACY%) AND c.module_id = "coupon" AND  c.status = ' . Phpfox::getService('coupon')->getStatusCode('endingsoon'));
    }

    private function _setConditionAndHandleFAQView() {
        $this->search()->clearConditions();
        $this->search()->clear();
    }

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process() {

        Phpfox::getUserParam('coupon.can_view_coupon', true);

        Phpfox::getService('coupon.process')->CheckAllCouponsStatus();

        $this->_checkIsInAjaxControllerAndInUserProfile();
        
        $bIsHomepage = $this->_checkIsInHomePage();

        $this->_aParentModule = $this->getParam('aParentModule');
        
        $sView = $this->request()->get('view');

        $aBrowseParams = $this->_initializeSearchParams();

        $aAdvSearchParams = [];

        if ($this->search()->get('advsearch'))
        {
            $aAdvSearchParams = $this->_setAdvSearchConditions();
        }

        if(($this->_aParentModule === null && !defined('PHPFOX_IS_PAGES_VIEW')))
            $this->template()->setBreadCrumb(_p('coupon'), $this->url()->makeUrl('coupon'));

        $this->_buildSubsectionMenu();
        $aModerateMenu = [];

        switch ($sView) {
            case 'my':
                $this->_setConditionAndHandleMyView();
                $aModerateMenu = array(
                	array(
                        'phrase' => _p('pause'),
                        'action' => 'pause'
                    ),
                    array(
                        'phrase' => _p('resume'),
                        'action' => 'resume'
                    ),
                    array(
                        'phrase' => _p('close'),
                        'action' => 'close'
                    )

                );
                break;
            case 'my_claims':
                $this->_setConditionAndHandleMyClaimedView();
                break;
            case 'friend':
                $this->_setConditionAndHandleFriendView();
                break;
            case 'favorite':
                $this->_setConditionAndHandleFavoriteView();
                break;
            case 'following':
                $this->_setConditionAndHandleFollowingView();
                break;
            case 'featured':
                $this->_setConditionAndHandleFeaturedView();
                break;
            case 'pending':
                $this->_setConditionAndHandlePendingView();
                $aModerateMenu = array(
                    array(
                        'phrase' => _p('approve'),
                        'action' => 'approve'
                    ),
                    array(
                        'phrase' => _p('deny'),
                        'action' => 'deny'
                    ),
                    array(
                        'phrase' => _p('close'),
                        'action' => 'close'
                    ),
                    array(
                        'phrase' => _p('delete'),
                        'action' => 'delete'
                    )
                );
                break;
            case 'upcoming':
                $this->_setConditionAndHandleUpcomingView();
                break;
            case 'endingsoon':
                $this->_setConditionAndHandleEndingSoonView();
                break;
            case 'faq':
                $this->_setConditionAndHandleFAQView();
                break;
            default:
                $this->_setConditionAndHandleDefaultView($sView);
                break;
        }

        $this->_checkIsThisACategoryRequestAndHandleIt();

        $this->search()->browse()->params($aBrowseParams)->execute();
        $this->search()->browse()->setPagingMode(Phpfox::getParam('coupon.paging_mode', 'loadmore'));
        
        $aRows = $this->search()->browse()->getRows();

        foreach($aRows as &$aRow)
        {
            $aRow = Phpfox::getService('coupon')->retrieveMoreInfoFromCoupon($aRow);
        }

        $aItems = $aRows;

        // Set pager
        $aParamsPager = array(
            'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $this->search()->browse()->getCount(),
            'paging_mode' => $this->search()->browse()->getPagingMode()
        );

        Phpfox::getLib('pager')->set($aParamsPager);

        $this->_setMetaAndKeywordsOfPage($aItems);

        $this->template()->assign(array(
            'corepath' => Phpfox::getParam('core.path'),
            'aFeatured' => $this->_aFeaturedCoupons,
            'iCnt' => $this->search()->browse()->getCount(),
            'aItems' => $aItems,
            'sSearchBlock' => _p('search_coupons_dot'),
            'bIsProfile' => $this->_bIsProfile,
            'bIsHomepage' => $bIsHomepage,
            'bIsInPages'=> $this->_checkIsInPageModule(),
            'aCouponStatus' => Phpfox::getService('coupon')->getAllStatus(),
            'sView' => $sView,
            'iPage' => $this->search()->getPage(),
            'sDefaultLink' => Phpfox::getParam('core.url_module') . "coupon/static/image/default/noimage.png"
        ));

        $this->setParam('bInHomepageFr', $bIsHomepage);

        $this->template()->setHeader(
            array(
	            'comment.css' => 'style_css',
	            'pager.css' => 'style_css',
	            'global.css' => 'module_coupon',
	            'owl.carousel.css' => 'module_coupon',
	            'jquery/plugin/jquery.highlightFade.js' => 'static_script',
	            'quick_edit.js' => 'static_script',
	            'feed.js' => 'module_feed',
	            'country.js' => 'module_core',
	            'add.js' => 'module_coupon',
	            'yncoupon.js' => 'module_coupon',
	            'owl.carousel.min.js' => 'module_coupon',
            )
        );

        // add support responsiveclean template
        if ( $this->template()->getThemeFolder() == 'ynresponsiveclean' ) {
             $this->template()->setHeader('cache', array(
                'jquery.flexslider.js' => 'module_coupon',
                'flexslider.css' => 'module_coupon',
            ));
        }

        if(Phpfox::isAdmin())
        {
            $bDraftCount = 0;
            foreach($aItems as $iKey => $aItem)
            {
                if($aItem['is_draft'])
                {
                    $bDraftCount ++;
                }
            }

            if($bDraftCount < count($aItems))
            {
                $this->_setGlobalModeration($aModerateMenu);
                $this->template()->assign([
                    'bShowModerator' => count($aModerateMenu)
                ]);
            }

        }

        // Get advanced search content block
        ob_clean();
        Phpfox::getBlock('coupon.search',$aAdvSearchParams);
        $sContent = ob_get_contents();
        $this->template()->assign('sAdvSearchContent', $sContent);
        ob_clean();

                //Special breadcrumb for pages
        //Special breadcrumb for pages
        if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW && defined('PHPFOX_PAGES_ITEM_TYPE')){
            if (Phpfox::hasCallback(PHPFOX_PAGES_ITEM_TYPE, 'checkPermission') && !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm($this->_aParentModule['item_id'], 'coupon.who_can_view_browse_coupons')) {
                $this->template()->assign(['aSearchTool' => []]);
                return \Phpfox_Error::display(_p('Cannot display this section due to privacy.'));
            }

            $this->template()
                ->clearBreadCrumb();
            $this->template()
                ->setBreadCrumb(Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getTitle($this->_aParentModule['item_id']), $this->_aParentModule['url'])
                ->setBreadCrumb(_p('coupons'), $this->_aParentModule['url'] . 'coupon/');
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {

    }

}

?>