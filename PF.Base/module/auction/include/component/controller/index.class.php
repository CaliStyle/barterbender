<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_Index extends Phpfox_Component {

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

    private function _checkIsInAjaxControllerAndInUserProfile()
    {
        if (defined('PHPFOX_IS_AJAX_CONTROLLER'))
        {
            $this->_bIsProfile = true;
            $this->_aProfileUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $this->_aProfileUser);
        }
        else
        {
            $this->_bIsProfile = $this->getParam('bIsProfile');
            if ($this->_bIsProfile === true)
            {
                $this->_aProfileUser = $this->getParam('aUser');
            }
        }
    }

    private function _checkIsInPageModule()
    {
        $bIsInPages = FALSE;
        if ($this->request()->get('req1') == 'pages')
        {
            $bIsInPages = TRUE;
        }
        return $bIsInPages;
    }

    /**
     * check if we are in home page or profile page
     * @return bool
     */
    private function _checkIsInHomePage()
    {
        $bIsInHomePage = false;
        $aParentModule = $this->getParam('aParentModule');
        $sTempSearch = $this->request()->get('s', 0);
		$sTempView = $this->request()->get('view', '');
        if ($sTempSearch == 0 
        	&& $sTempView == '' 
        	&& !isset($aParentModule['module_id']) 
        	&& !$this->request()->get('search-id') 
        	&& !$this->request()->get('sort') 
        	&& !$this->request()->get('show') 
			&& !$this->request()->get('advsearch') 
        	&& $this->request()->get('req2') == '')
        {
            if (!defined('PHPFOX_IS_USER_PROFILE'))
            {
                $bIsInHomePage = true;
            }
        }

        return $bIsInHomePage;
    }

    private function _checkIsInSearch()
    {
        $bIsSearch = false;
        if ($this->request()->get('search-id') || $this->request()->get('sort') || $this->request()->get('show'))
        {
            $bIsSearch = true;
        }
        return $bIsSearch;
    }

    /**
     * @return array param to browse campaign list
     */
    private function _initializeSearchParams()
    {
        $this->search()->set(array(
            'type' => 'auction',
            'field' => 'ep.product_id',
            'search_tool' => array(
                'table_alias' => 'ep',
                'search' => array(
                    'action' => $this->_aParentModule != null ? $this->url()->makeUrl($this->_aParentModule['module_id'], array($this->_aParentModule['item_id'], 'auction')) : ($this->_bIsProfile === true ? $this->url()->makeUrl($this->_aProfileUser['user_name'], array('auction', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('auction', array('view' => $this->request()->get('view')))),
                    'default_value' => _p('search_auctions'),
                    'name' => 'search',
                    'field' => array('ep.name'),
                ),
                'sort' => array(
                    'top-orders' => array('ep.total_orders', _p('top_orders')),
                    'newest' => array('ep.product_creation_datetime', _p('newest')),
                    'oldest' => array('ep.product_creation_datetime', _p('oldest'), 'ASC'),
                    'a-z' => array('ep.name', _p('a_z'), 'ASC'),
                    'z-a' => array('ep.name', _p('z_a')),
                    'most-liked' => array('ep.total_like', _p('most_liked'))
                ),
                'show' => array(12, 24, 36),
                'when_field' => 'product_creation_datetime'
            )
                )
        );


        $aBrowseParams = array(
            'module_id' => 'auction',
            'alias' => 'ep',
            'table' => Phpfox::getT('ecommerce_product'),
            'hide_view' => array('pending', 'my'),
        );
        $sView = $this->request()->get('view');

        if($sView != 'pending'){
            $aBrowseParams['select'] = 'ep.auction_total_bid,ep.auction_latest_bid_price, ep.auction_item_reserve_price,';
        }
        
        if (Phpfox::getParam('core.section_privacy_item_browsing'))
        {
            $aBrowseParams['field'] = 'product_id';
            
            if ($this->search()->get('advsearch') || $this->request()->get('advsearch'))
            {
                $aBrowseParams['distinct'] = 'ep.product_id'; //condition in buildPrivacy is very different core.browse
            }
        }
        else
        {
            $aBrowseParams['field'] = 'ep.product_id';
            $aBrowseParams['distinct'] = true;
        }

        return $aBrowseParams;
    }

    private function _setAdvSearchConditions()
    {
        $sKeyword = $this->search()->get('keyword');
        $aCategory = $this->search()->get('category');
        $sSort = $this->search()->get('sort');
        $sView = $this->request()->get('view');
        
        $aCategory = array_filter(array_unique($aCategory));
        $sCategories = '';
        if ($aCategory)
        {
            $sCategories = implode(',', $aCategory);
        }
        switch ($sView) {
        	case 'pending':
                $this->_setConditionAndHandlePendingAuctionsView();
                break;
            case 'myauctions':
                $this->_setConditionAndHandleMyView();
                break;
            case 'upcoming':
                $this->_setConditionAndHandleUpcomingView();
                break;
            case 'endtoday':
                $this->_setConditionAndHandleEndingTodayView();
                break;
            case 'todaylive':
                $this->_setConditionAndHandleTodayLiveView();
                break;
            case 'bidden-by-my-friends':
                $this->_setConditionAndHandleBiddenByMyFriendsView();
                break;
            case 'won-by-my-friends':
                $this->_setConditionAndHandleWonByMyFriendsView();
                break;
            case 'buyers-also-viewed':
                $this->_setConditionAndHandleBuyersAlsoViewedView();
                break;
        }

        $aForms = array(
            'keyword' => $sKeyword,
            'categories' => $sCategories,
            'sort' => $sSort,
            'advancedsearch' => true
        );
		
        $this->template()
                ->setHeader(array(
							'<script type="text/javascript">$Behavior.initAdvancedSearchForCategory = function(){  var aCategories = explode(\',\', \'' . $sCategories . '\'); for (i in aCategories) { $(\'#js_mp_holder_\' + aCategories[i]).show(); $(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true); } }</script>'
						)
					)
				->assign('aForms', $aForms)
				;
        if (!empty($sKeyword))
        {
            $this->search()->setCondition('AND ep.name LIKE "%' . Phpfox::getLib('parse.input')->clean($sKeyword) . '%" ');
        }

        $sViewCategory = $this->request()->get('category');
        $aViewCategory = explode(",", $sViewCategory);
        

        if (is_array($aCategory) && !empty($aCategory))
        {
            $iChild = $aCategory[0];
            foreach ($aCategory as $k => $iCategory)
            {
                if (Phpfox::getService('ecommerce.category')->isChild($iCategory, $iChild))
                {
                    $iChild = $iCategory;
                }
            }
            
            $this->_setConditionByCategory($iChild);
            $this->setParam('category', $iChild);
        }

        if (count($aCategory))
        {
            Phpfox::getService('auction.process')->saveLastingSearch($aCategory);
        }
    }
	
	private function _setConditionAndHandlePendingAuctionsView()
    {
        Phpfox::getUserParam('auction.can_approve_auction', true);
        $this->search()->setCondition("AND ep.product_status = 'pending'");
        $this->search()->setCondition("AND u.user_group_id != 5");
    }
	
    private function _setConditionAndHandleMyView()
    {
        Phpfox::isUser(true);
        $this->search()->setCondition('AND ep.user_id = ' . Phpfox::getUserId() . ' AND ep.product_status != \'deleted\'');
    }

    private function _setConditionAndHandleUpcomingView()
    {
        $this->search()->setCondition("AND ( ep.product_status = 'approved' || ep.product_status = 'running' || ep.product_status = 'bidden' )");
        $this->search()->setCondition("AND ep.module_id = 'auction' AND ep.privacy IN(%PRIVACY%) ");
        $this->search()->setCondition('AND ep.start_time > ' . PHPFOX_TIME);
    }
    
    private function _setConditionAndHandleEndingTodayView()
    {   

        $iBeginOfDay = strtotime("midnight", PHPFOX_TIME);
        $iEndOfDay = strtotime("tomorrow", $iBeginOfDay) - 1;

        $this->search()->setCondition("AND ( ep.product_status = 'approved' || ep.product_status = 'running' || ep.product_status = 'bidden' )");
        $this->search()->setCondition("AND ep.module_id = 'auction' AND ep.privacy IN(%PRIVACY%) ");
        $this->search()->setCondition('AND ep.end_time >= ' . $iBeginOfDay . ' AND ep.end_time <= ' .$iEndOfDay  );
    
    }

    private function _setConditionAndHandleTodayLiveView()
    {   

        $iBeginOfDay = strtotime("midnight", PHPFOX_TIME);
        $iEndOfDay = strtotime("tomorrow", $iBeginOfDay) - 1;

        $this->search()->setCondition("AND ( ep.product_status = 'approved' || ep.product_status = 'running' || ep.product_status = 'bidden' )");
        $this->search()->setCondition("AND ep.module_id = 'auction' AND ep.privacy IN(%PRIVACY%) ");
        $this->search()->setCondition('AND ep.start_time >= ' . $iBeginOfDay . ' AND ep.start_time <= ' .$iEndOfDay  );
    
    }
    

    private function _setConditionAndHandleBiddenByMyFriendsView()
    {
        if (!Phpfox::isModule('friend'))
        {
            $this->url()->send('auction');
        }
        
        $this->search()->setCondition("AND ( ep.product_status = 'approved' || ep.product_status = 'running'|| ep.product_status = 'bidden' )");
        $this->search()->setCondition("AND ep.module_id = 'auction' AND ep.privacy IN(%PRIVACY%) ");
        $this->search()->setCondition('AND ep.start_time < ' . PHPFOX_TIME);
        $this->search()->setCondition('AND ep.end_time > ' . PHPFOX_TIME);
    }

    private function _setConditionAndHandleBuyersAlsoViewedView()
    {
        $this->search()->setCondition("AND ( ep.product_status = 'approved' || ep.product_status = 'running' || ep.product_status = 'bidden' )");
        $this->search()->setCondition("AND ep.module_id = 'auction' AND ep.privacy IN(%PRIVACY%) ");
        $sViewedAuctionId = Phpfox::getCookie('ynauction_viewed_auction_id');
        if (empty($sViewedAuctionId))
        {
            $this->search()->setCondition('AND false');
        }
        else
        {
            $this->search()->setCondition('AND ep.product_id IN (' . $sViewedAuctionId . ')');
        }
    }

    private function _setConditionAndHandleWonByMyFriendsView()
    {
        $this->search()->setCondition("AND ep.product_status = 'completed'");
        $this->search()->setCondition("AND ep.module_id = 'auction' AND ep.privacy IN(%PRIVACY%) ");
        $this->search()->setCondition('AND ep.end_time < ' . PHPFOX_TIME);
    }
    
    private function _setConditionAndHandleFriendAuction(){

    }
    /**
     *
     * @param string $sView name of the view we are going to see
     */
    private function _setConditionAndHandleDefaultView($sView)
    {
        $bViewOwnProfileAuction = false;
        if ($this->_bIsProfile === true)
        {
            $this->search()->setCondition("AND ep.module_id = 'auction' AND ep.user_id = " . $this->_aProfileUser['user_id']);

            if ($this->_aProfileUser['user_id'] != Phpfox::getUserId() && !Phpfox::isAdmin())
            {
                $this->search()->setCondition(" AND ( ep.product_status = 'running' || ep.product_status = 'bidden' || ep.product_status = 'approved' || ep.product_status = 'bidden' )AND ep.privacy IN(" . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('core')->getForBrowse($this->_aProfileUser)) . ")");
            }
            else{
                $this->search()->setCondition(" AND ( ep.product_status != 'deleted')AND ep.privacy IN(" . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('core')->getForBrowse($this->_aProfileUser)) . ")");
                $bViewOwnProfileAuction = true;
            }
        }
        else if ($this->_aParentModule != null && defined('PHPFOX_IS_PAGES_VIEW'))
        {
            $this->search()->setCondition("AND ep.module_id = '" . $this->_aParentModule['module_id'] . "' AND ep.item_id  = " . $this->_aParentModule['item_id'] . " AND ep.privacy IN(%PRIVACY%) ");

            if (Phpfox::getService('pages')->isAdmin($this->_aParentModule['item_id']) || Phpfox::isAdmin())
            {
                $this->search()->setCondition("AND ( ( ep.product_status = 'approved' || ep.product_status = 'running' || ep.product_status = 'bidden' ) || ep.user_id = " . Phpfox::getUserId() . ")");
            }
            else
            {
                $this->search()->setCondition("AND ( ( ep.product_status = 'approved' || ep.product_status = 'running' || ep.product_status = 'bidden' ) || ep.user_id = " . Phpfox::getUserId() . ")");
            }
        }
        else
        {
            $this->search()->setCondition("AND ep.module_id = 'auction' AND ep.privacy IN(%PRIVACY%) ");
        }

        if (!$this->_bIsProfile && $this->_aParentModule == null && !defined('PHPFOX_IS_PAGES_VIEW'))
        {
            $this->search()->setCondition("AND ( ep.product_status = 'approved' || ep.product_status = 'running' || ep.product_status = 'bidden' )");
        }
    
    }

    private function _checkIsThisACategoryRequestAndHandleIt()
    {
        // check category request and set corresponding condition
        if ($this->request()->get(($this->_bIsProfile === true ? 'req3' : 'req2')) == 'category')
        {
            if ($aCategory = Phpfox::getService('ecommerce.category')->getForEdit($this->request()->getInt(($this->_bIsProfile === true ? 'req4' : 'req3'))))
            {
                if ($aParentCategory = Phpfox::getService('ecommerce.category')->getForEdit($aCategory['parent_id']))
                {
                    $sPhrase = (Phpfox::isPhrase($aParentCategory['title']) ? _p($aParentCategory['title']) : Phpfox::getLib('locale')->convert($aParentCategory['title']));
                    $this->template()->setBreadCrumb($sPhrase, $this->url()->permalink(array('auction.category', 'view' => $this->request()->get('view')), $aParentCategory['category_id'], $sPhrase));
                }

                $iCategory = $this->request()->getInt(($this->_bIsProfile === true ? 'req4' : 'req3'));
                $this->_setConditionByCategory($iCategory);

                $sPhrase = (Phpfox::isPhrase($aCategory['title']) ? _p($aCategory['title']) : Phpfox::getLib('locale')->convert($aCategory['title']));

                $this->template()->setTitle($sPhrase);
                $this->template()->setBreadCrumb($sPhrase, $this->url()->makeUrl('current'), true);

                $this->search()->setFormUrl($this->url()->permalink(array('auction.category', 'view' => $this->request()->get('view')), $aCategory['category_id'], $aCategory['title']));
            }
        }
    }

    private function _setGlobalModeration($aMenu)
    {
        $this->setParam('global_moderation', array(
            'name' => 'auction',
            'ajax' => 'auction.moderation',
            'menu' => $aMenu,
                )
        );
    }

    private function _setConditionByCategory($iCategory)
    {
        $sCategories = $iCategory;

        $sChildIds = Phpfox::getService('ecommerce.category')->getChildIds($iCategory);
        if (!empty($sChildIds))
        {
            $sCategories .= ',' . $sChildIds;
        }

        $this->search()->setCondition('AND ecd.category_id IN(' . $sCategories . ')');
    }

    private function _initFunctions()
    {
        $iAuctionId = $this->request()->getInt('auctionId');
        $sStatus = $this->request()->get('status');
        $sDetailHeaderSubMenu = $this->request()->get('detailheadersubmenu');
        
        $aProductStatus = array('draft', 'unpaid', 'pending', 'denied', 'bidden', 'running', 'paused', 'completed', 'deleted', 'approved', 'other');
        if (!in_array($sStatus, $aProductStatus))
        {
            return;
        }
        
        $aAuction = Phpfox::getService('auction')->getQuickAuctionById($iAuctionId);
        if (!$aAuction || ($aAuction['user_id'] != Phpfox::getUserId() && !Phpfox::isAdmin()))
        {
            $this->url()->send('auction', null, _p('auction_not_found'));
        }
        
        Phpfox::getService('auction.process')->updateAuctionStatus($iAuctionId, $sStatus);
        $sLink = $this->url()->permalink('auction.detail', $aAuction['product_id'], $aAuction['name']) . $sDetailHeaderSubMenu . '/';
        
        $this->url()->send($sLink);
    }

    private function _convertPrivacy($aConditions)
    {
        $aNewConditions = array();
        foreach ($aConditions as $sCond)
		{
			switch ($this->request()->get('view'))
			{
				case 'friend':
					$aNewConditions[] = str_replace('%PRIVACY%', '0,1,2', $sCond);
					break;
				case 'my':
					$aNewConditions[] = str_replace('%PRIVACY%', '0,1,2,3,4', $sCond);
					break;				
				case 'pages_member':
					$aNewConditions[] = str_replace('%PRIVACY%', '0,1', $sCond);
					break;
				case 'pages_admin':
					$aNewConditions[] = str_replace('%PRIVACY%', '0,1,2', $sCond);
					break;
				default:
					$aNewConditions[] = str_replace('%PRIVACY%', '0', $sCond);
					break;
			}
		}
        return $aNewConditions;
    }

    public function deleteProduct()
    {
        
        $iDeleteProductId = $this->request()->getInt('deleteProductId');
        if ($iDeleteProductId)
        {
            $aProduct = Phpfox::getService('auction')->getAuctionById($iDeleteProductId);
            if (!isset($aProduct['product_id']) || $aProduct['product_id'] == 'bidden' || $aProduct['product_id'] == 'completed' || $aProduct['end_time'] < PHPFOX_TIME)
            {
                return $this->url()->send('auction', NULL, _p('auction_is_not_valid_or_it_has_been_deleted'));
            }
            
            $bCanDeleteAuction = Phpfox::getService('auction.permission')->canDeleteAuction($aProduct['user_id']);
            if (!$bCanDeleteAuction)
            {
                return $this->url()->send('auction', NULL, _p('you_dont_have_permission_to_delete_this_auction'));
            }
            
            if (Phpfox::getService('auction.process')->delete($iDeleteProductId))
            {
                return $this->url()->send('auction', NULL, _p('delete_auction_successfully'));
            }
            else
            {
                return $this->url()->send('auction', NULL, _p('can_not_delete_this_auction'));
            }
        }
    }

    public function process()
    {
        $this->deleteProduct();

        Phpfox::getUserParam('auction.can_view_auction', true);

        $this->_initFunctions();
        
        $this->_checkIsInAjaxControllerAndInUserProfile();

        $bIsHomepage = $this->_checkIsInHomePage();
        $bIsSearch = $this->_checkIsInSearch();

        $this->_aParentModule = $this->getParam('aParentModule');

        $sView = $this->request()->get('view');
        
        $aBrowseParams = $this->_initializeSearchParams();
		
        if ($this->search()->get('advsearch'))
        {
            $this->_setAdvSearchConditions();
        }
        
        $this->template()->setBreadCrumb(_p('auctions'), $this->url()->makeUrl('auction'));

        $aModerateMenu = array();
		$sortTitle = "";
		$sortUrl = "";
        $bShowModerator = false;
        switch ($sView) {
			case 'pending':
				$this->_setConditionAndHandlePendingAuctionsView();
				$aModerateMenu = array(
                    array(
                        'phrase' => _p('approve'),
                        'action' => 'approve'
                    ),
                    array(
                        'phrase' => _p('deny'),
                        'action' => 'deny'
                    )
                );
                $bShowModerator = true;
				break;
            case 'myauctions':
                $this->_setConditionAndHandleMyView();
                $aModerateMenu = array(
                    array(
                        'phrase' => _p('delete_auction'),
                        'action' => 'delete'
                    )
                );
                $bShowModerator = true;
                break;
            case 'upcoming':
                $this->_setConditionAndHandleUpcomingView();
				$sortTitle = _p('upcoming_auctions');
				$sortUrl = Phpfox::getLib('url') -> makeUrl('auction', array('view' => 'upcoming'));
                break;
            case 'endtoday':
                $this->_setConditionAndHandleEndingTodayView();
				$sortTitle = _p('ending_today_auctions');
				$sortUrl = Phpfox::getLib('url') -> makeUrl('auction', array('view' => 'endtoday'));
                break;
             case 'todaylive':
                $this->_setConditionAndHandleTodayLiveView();
				$sortTitle = _p('today_live_auctions');
				$sortUrl = Phpfox::getLib('url') -> makeUrl('auction', array('view' => 'todaylive'));
                break;
            case 'bidden-by-my-friends':
                $this->_setConditionAndHandleBiddenByMyFriendsView();
                break;
            case 'won-by-my-friends':
                $this->_setConditionAndHandleWonByMyFriendsView();
                break;
            case 'buyers-also-viewed':
                $this->_setConditionAndHandleBuyersAlsoViewedView();
				$sortTitle = _p('buyers_also_viewed');
				$sortUrl = Phpfox::getLib('url') -> makeUrl('auction', array('view' => 'buyers-also-viewed'));
			    break;
			default:
                $this->_setConditionAndHandleDefaultView($sView);
                break;
        }

        $this->template()->assign(['bShowModerator' => $bShowModerator]);
        
		//for sort
		$sSort = $this->request()->get('sort');
		switch ($sSort) {
			case 'most-liked':
				$sortTitle = _p('most_liked_auctions');
				$sortUrl = Phpfox::getLib('url') -> makeUrl('auction', array('sort' => 'most-liked'));
				break;
		}
		
        $this->search()->setCondition("AND ep.product_status != 'deleted'");
        $this->search()->setCondition('AND ep.product_creating_type = "auction"');
        
        $this->template()->setTitle(_p('auctions'));

        Phpfox::getService('auction.helper')->buildMenu();
        
        $this->_checkIsThisACategoryRequestAndHandleIt();

        if ('tag' == $this->request()->get('req2'))
        {
            if ($aTag = Phpfox::getService('tag')->getTagInfo('auction', $this->request()->get('req3')))
            {
                $this->setParam('sTagType', 'auction');
                $this->template()->setBreadCrumb(_p('tag.topic') . ': ' . $aTag['tag_text'] . '', $this->url()->makeUrl('current'), true);
                $this->search()->setCondition('AND tag.tag_text = \'' . Phpfox::getLib('database')->escape($aTag['tag_text']) . '\'');
                $bIsValidTag = true;
                $sHeadline = "";
            }
        }
        
        $aRows = array();
        $iCnt = 0;
        
        if ($sView == 'bidden-by-my-friends')
        {
            $aConds = $this->search()->getConditions();
            $iLimit = $this->search()->getDisplay();

            list($iCnt, $aRows) = Phpfox::getService('auction')->getBiddenByMyFriendsAuctionsList($this->_convertPrivacy($aConds), $this->search()->getSort(), $this->search()->getPage(), $iLimit);
        }
        else
        if (!$bIsHomepage)
        {
            $this->search()->browse()
                ->params($aBrowseParams)
                ->setPagingMode(Phpfox::getParam('auction.paging_mode', 'loadmore'))
                ->execute();
            $aRows = $this->search()->browse()->getRows();
        }
        
        $sSearch = $this->search()->get('search');
        $sKeyword = $this->search()->get('keyword');
        
        if (!empty($sSearch) || !empty($sKeyword))
        {
            $iLimit = (Phpfox::getParam('auction.max_items_block_auctions_you_may_like') > 0 ? Phpfox::getParam('auction.max_items_block_auctions_you_may_like') : 0);
            
            $aSearchResult = array_slice($aRows, 0, $iLimit);
            $aTemp = array();
            foreach ($aSearchResult as $aItem)
            {
                $aTemp[] = $aItem['product_id'];
            }
            $sAuctionsYouMayLikeId = implode(',', $aTemp);
            Phpfox::setCookie('ynauction_auctions_you_may_like', $sAuctionsYouMayLikeId);
        }
        
        $sType = '';
        
        foreach ($aRows as $key => $aProduct)
        {
            $aRows[$key] = Phpfox::getService('auction')->retrieveInfoFromAuction($aProduct);
        }

        $aItems = $aRows;
        foreach ($aItems as $iKey => $auction ) {
            if (empty($aItems[$iKey]['logo_path'])) {
                $aItems[$iKey]['default_logo_path'] = Phpfox::getParam('core.path') . 'module/auction/static/image/default_ava.png';
            }
        }

        /* load view block hompage */
        $iPage = $this->search()->getPage();
        
        if (!$bIsHomepage || $bIsSearch)
        {
            if ((int) $this->search()->getPage() > 1)
            {
                $sViewType = Phpfox::getCookie('ynauction_menu_viewtype');
            }
        }
        else
        {

            $sViewType = $this->request()->get('viewtype');
            Phpfox::setCookie('ynauction_menu_viewtype', '', -1);
        }

        if (empty($sViewType))
        {
            $default_view = strtolower(Phpfox::getParam('auction.default_view'));
            switch ($default_view) {
                case 'grid':
                    $sViewType = 'gridview';
                    break;
                
                case 'pinboard':
                    $sViewType = 'pinboardview';
                    break;
                
                case 'list':
                default:
                    $sViewType = 'listview';
                    break;
            }
        }


        if ($sView == 'bidden-by-my-friends')
        {

            $aParamsPager = array(
                'page' => $iPage,
                'size' => $iLimit,
                'count' => $this->search()->getSearchTotal($iCnt),
                'paging_mode' => $this->search()->browse()->getPagingMode()
            );

            Phpfox::getLib('pager')->set($aParamsPager);
        }
        else
        {
            $iCnt = $this->search()->browse()->getCount();

            $aParamsPager = array(
                'page' => $this->search()->getPage(),
                'size' => $this->search()->getDisplay(),
                'count' => $iCnt,
                'paging_mode' => $this->search()->browse()->getPagingMode()
            );

            Phpfox::getLib('pager')->set($aParamsPager);

        }

        $aCondition = $this->search()->getConditions();
        $this->template()->assign(array(
        			'sortUrl' => $sortUrl,
        			'sortTitle' => $sortTitle,
                    'aProfileUser' => $this->_aProfileUser,
                    'corepath' => Phpfox::getParam('core.path'),
                    'iCnt' => $iCnt,
                    'iPage' => $iPage,
                    'aItems' => $aItems,
                    'sSearchBlock' => _p('search_auctions'),
                    'bIsProfile' => $this->_bIsProfile,
                    'bIsHomepage' => $bIsHomepage,
                    'bIsInPages' => $this->_checkIsInPageModule(),
                    'sView' => $sView,
                    'sViewType' => $sViewType,
                    'sNoimageUrl' => Phpfox::getLib('template')->getStyle('image', 'noimage/' . 'profile_50.png'),
                    'sCondition' => base64_encode(json_encode($aCondition)),
                ))
                ->setHeader('cache', array(
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'quick_edit.js' => 'static_script',
                    'comment.css' => 'style_css',
                    'pager.css' => 'style_css',
                    'jquery.rating.css' => 'style_css',
                    'feed.js' => 'module_feed'
                ))
                ->setMeta('keywords', Phpfox::getParam('auction.auction_meta_keywords'))
                ->setMeta('description', Phpfox::getParam('auction.auction_meta_description'));

        $this->setParam('bInHomepageFr', $bIsHomepage);

        Phpfox::getService('auction.helper')->loadAuctionJsCss();
        $this->_setGlobalModeration($aModerateMenu);
    }

    /**
     * This function is used to add plugin. Do not delete.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynauction.component_controller_index_clean')) ? eval($sPlugin) : false);
    }

}

?>