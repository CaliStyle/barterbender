<?php
defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Controller_Index extends Phpfox_Component {

    private $_aParentModule = null;

    private function _buildSubsectionMenu() {
        if ($this->_aParentModule === null && !defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW')) {
            Phpfox::getService('contest.helper')->buildMenu();
        }
    }

    private function _checkIsInHomePage() {

        $bIsInHomePage = false;
        $aParentModule = $this->getParam('aParentModule');
        $sTempView = $this->request()->get('view', false);
        if ($sTempView == "" && !isset($aParentModule['module_id']) && !$this->request()->get('s')
                && !$this->request()->get('sort')
				&& !$this->request()->get('when')
				&& !$this->request()->get('type')
                && !$this->request()->get('show')
                && $this->request()->get('req2') == '') {
            if (!defined('PHPFOX_IS_USER_PROFILE')) {
                $bIsInHomePage = true;
            }
        }

        return $bIsInHomePage;
    }

    private function _checkIsThisAViewDetailRequest() {
        /**
         * Check if we are going to view an actual fundraising instead of the fundraising index page.
         * The 2nd URL param needs to be numeric.
         */
        if ($this->_aParentModule === null && $this->request()->getInt('req2') && !Phpfox::isAdminPanel()) {
            return true;
        } else {
            return false;
        }
    }

    private function _checkIsThisAViewEntriesRequest() {

        if ($this->request()->get('view') == 'pending_entries' || $this->request()->get('view') == 'my_entries') {
            return true;
        } else {
            return false;
        }
    }

    private function _view($sView, $bIsProfile, $aUser) {

        switch ($sView) {
            case 'my':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND ct.user_id = ' . Phpfox::getUserId());
                break;
			 case 'friend':
                Phpfox::isUser(true);
                $this->search()->setCondition(' AND ct.privacy IN(%PRIVACY%) AND (ct.contest_status = 5 or ct.contest_status = 4) ');
                break;
            case 'pending':
                Phpfox::isUser(true);
                $this->search()->setCondition(' AND ct.privacy IN(%PRIVACY%) AND ct.contest_status = 2');
                break;
            case 'featured':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND ct.privacy IN(%PRIVACY%) AND ct.is_feature = 1 and ct.contest_status=4');
                break;
            case 'premium':
                Phpfox::isUser(true);
                $this->search()->setCondition(' AND ct.privacy IN(%PRIVACY%) AND ct.is_premium = 1 and ct.contest_status=4');
                break;
            case 'ending_soon':
                Phpfox::isUser(true);
                $this->search()->setCondition(' AND ct.privacy IN(%PRIVACY%) AND ct.is_ending_soon = 1 and ct.contest_status=4');
				$setting = PHpfox::getParam('contest.ending_soon_setting');
				$day = Phpfox::getParam('contest.ending_soon_before');
				$time = $day*24*3600;
				if($setting=='End of Submission')
				{
						$where = ' AND '.(PHPFOX_TIME+$time)." >=ct.stop_time and ".PHPFOX_TIME."<=ct.stop_time";
				}
				else {
						$where = ' AND '.(PHPFOX_TIME+$time)." >=ct.end_time and ".PHPFOX_TIME."<=ct.end_time";
				}
				$this->search()->setCondition($where);
                break;
            case 'closed':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND ct.privacy IN(%PRIVACY%) AND ct.contest_status = 5');
                break;
            case 'my_following':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND ct.privacy IN(%PRIVACY%) AND (ct.contest_status = 5 or ct.contest_status = 4) AND pa.is_followed=1');
                break;
            case 'my_favorite':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND ct.privacy IN(%PRIVACY%) AND (ct.contest_status = 5 or ct.contest_status = 4) AND pa.is_favorite=1');
                break;
            default:
                if ($bIsProfile === true) {
                    $this->search()->setCondition("AND ct.user_id = " . $aUser['user_id'] . " AND ct.privacy IN(" . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('core')->getForBrowse($aUser)) . ")");
                } else {
                    $this->search()->setCondition("AND ct.privacy IN(%PRIVACY%) AND ct.contest_status=4");
                }
                break;
        }
    }

    private function _getCustomFilters()
    {
        $custom_filters = array();

        $sView = $this->request()->get('view', false);

		$custom_filters[_p('contest.type')] = array(
    		'data' => array(
                array(
    				'link' => 'all',
    				'phrase' => _p('contest.all_types')
    			),
    			array(
    				'link' => 'blog',
    				'phrase' => _p('contest.blog')
    			),
    			array(
    				'link' => 'music',
    				'phrase' => _p('contest.music')
    			),
    			array(
    				'link' => 'photo',
    				'phrase' => _p('contest.photo')
    			),
    			array(
    				'link' => 'video',
    				'phrase' => _p('contest.video')
    			)
            ),
			'param' => 'type',
    		'default_phrase' => _p('contest.all_types'),
		);

        return $custom_filters;
    }

    private function _setFilterCondition()
    {
        if ($sFilter = $this->request()->get('filter'))
        {
            switch ($sFilter)
            {
                case 'featured':
                    $this->search()->setCondition('AND ct.is_feature = 1 AND ct.contest_status != 5');
                    break;
                case 'premium':
                    $this->search()->setCondition('AND ct.is_premium = 1 AND ct.contest_status != 5');
                    break;
                case 'ending-soon':
                    $this->search()->setCondition('AND ct.is_ending_soon = 1 AND ct.contest_status != 5');
                    break;
                case 'closed':
                    $this->search()->setCondition('AND ct.contest_status = 5');
                    break;
            }
        }

        $sType = $this->request()->get('type', 'all');
        if ($sType != "all")
        {
            $iType = Phpfox::getService('contest.constant')->getContestTypeIdByTypeName($sType);
            $this->search()->setCondition('AND ct.type = '.$iType);
        }
    }

    private function _implementStyle($aContests)
    {
        $aStyleType = array(
            '1' => 'enblog',
            '2' => 'enphoto',
            '3' => 'envideo',
            '4' => 'enmusic',
        );

        if (!empty($aContests))
        {
            foreach ($aContests as $k => $aContest)
            {
                $aContests[$k]['style_type'] = $aStyleType[$aContest['type']];
            }
        }

        return $aContests;
    }

    public function process()
    {
        $this->search()->browse()->setPagingMode(Phpfox::getParam('contest.contest_paging_mode', 'loadmore'));
        Phpfox::getService('contest.contest.process')->checkAndUpdateStatusOfContests();
        if(!Phpfox::getService('contest.permission')->canViewBrowseContestModule())
        {
            $this->url()->send('contest.error', array('status' => Phpfox::getService('contest.constant')->getErrorStatusNumber('invalid_permission')));
        }

        if ($this->_checkIsThisAViewDetailRequest()) {
            return Phpfox::getLib('module')->setController('contest.view');
        }

        if ($this->_checkIsThisAViewEntriesRequest()) {
            return Phpfox::getLib('module')->setController('contest.entry.index');
        }

        $this->template()->setBreadcrumb(_p('contest.contest'), $this->url()->makeUrl('contest'));

        //check profile
        $bIsProfile = false;
        $aUser = null;
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

        // detect to show slide show and other blocks
        $bInHomepage = $this->_checkIsInHomePage();
        $this->_buildSubsectionMenu();

        //search contest
        $aSearchNumber = array(12, 24, 36, 48);
        $sActionUrl = ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'], array('contest', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('contest', array('view' => $this->request()->get('view'))));

        $this->search()->set(
            array(
                'type' => 'contest',
                'field' => 'ct.contest_id',
                'search' => 'search',
                'search_tool' => array(
                    'table_alias' => 'ct',
                    'search' => array(
                        'action' => $sActionUrl,
                        'default_value' => _p('contest.search_contests'),
                        'name' => 'search',
                        'field' => 'ct.contest_name'
                    ),
                    'custom_filters' => $this->_getCustomFilters(),
                    'sort' => array(
                        'latest' => array('ct.time_stamp', _p('contest.latest')),
                        'most-participant' => array('ct.total_participant', _p('contest.most_participants')),
                        'most-viewed' => array('ct.total_view', _p('contest.most_viewed')),
                        'most-favorited' => array('ct.total_favorite', _p('contest.most_favorited')),
                        'most-liked' => array('ct.total_like', _p('contest.most_liked')),
                    ),
                    'show' => $aSearchNumber
                )
            )
        );

        $sView = $this->request()->get('view', false);
        $this->_view($sView, $bIsProfile, $aUser);

        //removed contest
        $this->search()->setCondition(' AND ct.is_deleted = 0');
        if($sView!="my"){
            $this->search()->setCondition(' AND ct.is_active = 1');
        }
		$accessprofile = true;
		if($bIsProfile===true)
		{
			if($aUser['user_id']!=Phpfox::getUserId())
			{
				$this->search()->setCondition('AND (ct.contest_status = 4 || ct.contest_status = 5)' );
				$accessprofile = false;
			}
		}

		if ($this->request()->get(($bIsProfile === true ? 'req3' : 'req2')) == 'category') {
			if ($aContestCategory = Phpfox::getService('contest.category')->getForEdit($this->request()->getInt(($bIsProfile === true ? 'req4' : 'req3')))) {
                $this->setParam('sCurrentCategory', $aContestCategory['category_id']);
                $this->setParam('iParentCategoryId', $aContestCategory['parent_id']);
                if ($aContestCategory['parent_id'] && ($aContestParentCategory = Phpfox::getService('contest.category')->getForEdit($aContestCategory['parent_id']))) {
                    $this->template()->setBreadCrumb(Phpfox::getLib('locale')->convert(\Core\Lib::phrase()->isPhrase($aContestParentCategory['name']) ? _p($aContestParentCategory['name']) : $aContestParentCategory['name']), Phpfox::permalink('contest.category', $aContestParentCategory['category_id'], $aContestParentCategory['name']));
                }

				$this->search()->setCondition('AND rc.category_id = ' . $aContestCategory['category_id']);

				$this->template()->setTitle(Phpfox::getLib('locale')->convert(\Core\Lib::phrase()->isPhrase($aContestCategory['name']) ? _p($aContestCategory['name']) : $aContestCategory['name']));
				$this->template()->setBreadCrumb(Phpfox::getLib('locale')->convert($aContestCategory['name']), $this->url()->makeUrl('current'), true);

				$this->search()->setFormUrl($this->url()->permalink(array('contest.category', 'view' => $this->request()->get('view')), $aContestCategory['category_id'], $aContestCategory['name']));
			}
		}

        // Custom filter
        $this->_setFilterCondition();

        // Setup search params
        $aBrowseParams = array(
            'module_id' => 'contest',
            'alias' => 'ct',
            'field' => 'contest_id',
            'table' => Phpfox::getT('contest'),
            'hide_view' => array('my')
        );

        $this->search()->browse()->params($aBrowseParams)->execute();

        $aContests = Phpfox::getService('contest.contest')->implementsContestFields($this->search()->browse()->getRows());
		$aContests = $this->_implementStyle($aContests);

        foreach ($aContests as &$aContest) {
             $aContest = Phpfox::getService('contest.contest')->retrieveContestPermissions($aContest);
             $aContest['is_show_ending_soon_label'] = Phpfox::getService('contest.contest')->isShowContestEndingSoonLabel($aContest['contest_id']);
        }

        Phpfox::getLib('pager')->set(array('page' => $this->search()->getPage(), 'size' => $this->search()->getDisplay(), 'count' => $this->search()->browse()->getCount(), 'paging_mode' => $this->search()->browse()->getPagingMode()));
        $this->template()->assign(array(
            'bInHomepage' => $bInHomepage,
            'aContests' => $aContests,
			'corepath' => phpfox::getParam('core.path'),
			'sView' => $sView,
			'bIsProfile' => $bIsProfile,
			'accessprofile' => $accessprofile,
            'iPage' => $this->search()->getPage(),
            'sUrlNoImagePhoto' => Phpfox::getParam('core.path_file') . 'module/contest/static/image/no_photo_small.png',
        ));

        $this->template()->setHeader(
                array(
					'jquery.divslideshow-1.1.js' => 'module_contest',
                    'pager.css' => 'style_css',
                    'owl.carousel.min.js'  => 'module_contest',
					'block.css' => 'module_contest',
                    'yncontest.js' => 'module_contest',
                    'magnific-popup.css' => 'module_contest',
                    'jquery.magnific-popup.js' => 'module_contest',
                )
        )->setPhrase([
                'are_you_sure_info',
                'yes',
                'no'
            ])
            ->setMeta('keywords', Phpfox::getParam('contest.contest_meta_keywords'))
            ->setMeta('description', Phpfox::getParam('contest.contest_meta_description'));

        if ($sView == 'closed')
        {
      		$this->setParam('global_moderation', array(
    				'name' => 'contest',
    				'ajax' => 'contest.moderation',
    				'menu' => array(
    					array(
    						'phrase' => _p('contest.delete'),
    						'action' => 'delete'
    					)
    				)
    			)
    		);
        }
    }
}