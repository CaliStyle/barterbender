<?php

namespace Apps\P_AdvEvent\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class IndexController extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function curPageURL()
    {
        return phpfox::getLib('url')->getFullUrl();
    }

    private function _checkIsInHomePage()
    {
        $bIsInHomePage = false;
        $aParentModule = $this->getParam('aParentModule');
        $sTempSearch = $this->request()->get('s', 0);
        $sTempView = $this->request()->get('view', false);
        if ($sTempSearch == '' && $sTempView == '' && !isset($aParentModule['module_id']) && !$this->request()->get('advsearch')
            && !$this->request()->get('sort')
            && !$this->request()->get('when')
            && !$this->request()->get('type')
            && !$this->request()->get('show')
            && !$this->request()->get('search')
            && $this->request()->get('req2') == '') {
            if (!defined('PHPFOX_IS_USER_PROFILE')) {
                $bIsInHomePage = true;
            }
        }
        return $bIsInHomePage;
    }

    private function _checkIsInPage() {
        $aParentModule = $this->getParam('aParentModule');
        $bIsInPage = false;
        $sTempView = $this->request()->get('view', false);
        $sTempSearch = $this->request()->get('s', 0);
        if (isset($aParentModule['module_id']) && $sTempView == "" && $sTempSearch == 0
            && !$this->request()->get('search-id')
            && !$this->request()->get('sort')
            && !$this->request()->get('when')
            && !$this->request()->get('type')
            && !$this->request()->get('show')
            && $this->request()->get('req4') == '') {
            if (!defined('PHPFOX_IS_USER_PROFILE')) {
                $bIsInPage = true;
            }
        }

        return $bIsInPage;
    }

    private function __getLayoutSetting($bInHomepage = true, $bInPage = false, $bInProfile = false)
    {
        $itemWidth = 0;
        $gap = 0;
        if($bInHomepage
            || ($bInHomepage == false && $bInPage == false && $bInProfile == false)){
        } else if($bInPage){
        } else if($bInProfile){
        }

        return array(intval($itemWidth), intval($gap));
    }

    public function process()
    {
        Phpfox::getUserParam('fevent.can_access_event', true);

        Phpfox::getService('fevent.helper')->updateDurationOfRepeatEvents();

        $currentUrl = $this->url()->getFullUrl();

        $sView = !empty($this->request()->get('view')) ? $this->request()->get('view') : 'all';
        $pages = Phpfox::getService('fevent.helper')->getListOfPagesWhichJoinedByUserID(Phpfox::getUserId());
        if ($sView == 'pagesevents') {
            Phpfox::isUser(true);
        }

        $searchParams = !empty($this->request()->get('search')) ? $this->request()->get('search') : [];

        // Replace old address input by new location input that supports autocomplete
        // get params from location_input component: country_iso, location, location_lat, location_lng, country_child_id
        $locationParams = !empty($this->request()->get('val')) ? $this->request()->get('val') : [];
        // search with radius using lat, lng
        $searchParams['glat'] = !empty($locationParams['location_lat']) ? $locationParams['location_lat'] : 0;
        $searchParams['glong'] = !empty($locationParams['location_lng']) ? $locationParams['location_lng'] : 0;

        $vals = $this->request()->get('val');
        if(!empty($vals['country_iso'])) {
            $searchParams['country_iso'] = $vals['country_iso'];
        }

        // Check if we are on advanced search mode
        $bIsAdvSearch = false;
        if(!empty($searchParams['advsearch']) && empty($this->request()->get('when')))
        {
            $bIsAdvSearch = true;
        }
        if($this->request()->get("date") != "" && $this->request()->get('req2') == "category")
        {
            phpfox::getLib("url")->send(phpfox::getLib("url")->makeUrl("fevent") . "?date=".$this->request()->get("date")."&when=all-time&view=all");
        }

        $aParentModule = $this->getParam('aParentModule');
        $bIsPage = $aParentModule['module_id'] == 'pages' ? $aParentModule['item_id'] : 0;

        if ($aParentModule === null && $this->request()->getInt('req2') > 0) {
            return Phpfox::getLib('module')->setController('fevent.view');
        }

        if (($iRedirectId = $this->request()->getInt('redirect'))
            && ($aEvent = Phpfox::getService('fevent')->getEvent($iRedirectId, true))
            && $aEvent['module_id'] != 'fevent'
            && Phpfox::hasCallback($aEvent['module_id'], 'getEventRedirect')
        ) {
            if (($sForward = Phpfox::callback($aEvent['module_id'] . '.getEventRedirect', $aEvent['event_id']))) {
                Phpfox::getService('notification.process')->delete('event_invite', $aEvent['event_id'], Phpfox::getUserId());

                $this->url()->forward($sForward);
            }
        }

        if (($iDeleteId = $this->request()->getInt('delete'))) {
            if (($mDeleteReturn = Phpfox::getService('fevent.process')->delete($iDeleteId))) {
                if (is_bool($mDeleteReturn)) {
                    $position = strpos($currentUrl, "delete=");
                    if ($position !== false) {
                        $currentUrl = substr($currentUrl, 0, $position - 1);
                    }
                    $this->url()->send($currentUrl, null, _p('fevent.event_successfully_deleted'));
                } else {
                    $this->url()->forward($mDeleteReturn, null, _p('fevent.event_successfully_deleted'));
                }
            }
        }

        if (($iRedirectId = $this->request()->getInt('redirect')) && ($aEvent = Phpfox::getService('fevent')->getEvent($iRedirectId, true))) {
            Phpfox::getService('notification.process')->delete('event_invite', $aEvent['event_id'], Phpfox::getUserId());

            $this->url()->permalink('fevent', $aEvent['event_id'], $aEvent['title']);
        }

        $bIsUserProfile = false;
        if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            $bIsUserProfile = true;
            $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $aUser);
        }

        if (defined('PHPFOX_IS_USER_PROFILE')) {
            $bIsUserProfile = true;
            $aUser = $this->getParam('aUser');
        }

        $oServiceEventBrowse = Phpfox::getService('fevent.browse');
        $sCategory = null;
        $aCallback = $this->getParam('aCallback', false);
        $when = $this->request()->get('when');

        $latestSort = [
            'm.start_time', _p('fevent.latest'), 'DESC'
        ];

        if(!empty($searchParams['stime']) && !empty($searchParams['etime']) && empty($searchParams['status'])) {
            $latestSort = [
                'm.start_time', _p('fevent.latest'), 'ASC'
            ];
        }

        if($when) {
            switch ($when) {
                case 'upcoming':
                {
                    $latestSort = [
                        'm.start_time', _p('fevent.latest'), 'ASC'
                    ];
                    break;
                }
                case 'past':
                {
                    $latestSort = [
                        'm.end_time', _p('fevent.latest'), 'DESC'
                    ];
                    break;
                }
            }

            $this->template()->assign(array("sWhen" => $when));
            if($when == 'past')
            {
                $this->search()->setCondition('AND ( m.end_time < ' . (int)Phpfox::getService('fevent.helper')->convertFromUserTimeZone(PHPFOX_TIME) . ' ) ');
            }
        }
        elseif ($bIsAdvSearch && !empty($searchParams['status'])) {
            switch ($searchParams['status']) {
                case 'upcoming':
                {
                    $latestSort = [
                        'm.start_time', _p('fevent.latest'), 'ASC'
                    ];
                    break;
                }
                case 'past':
                {
                    $latestSort = [
                        'm.end_time', _p('fevent.latest'), 'DESC'
                    ];
                    break;
                }
            }
        }

        $sDefaultSort = Phpfox::getParam('fevent.fevent_default_sort_time', 'all-time');
        $aSearch = array(
            'type' => 'fevent',
            'field' => 'm.event_id',
            'search_tool' => array(
                'default_when' => (in_array($sView, ['pending', 'my', 'all']) ? 'all-time' : $sDefaultSort),
                'when_field' => 'start_time',
                'when_end_field' => 'end_time',
                'when_upcoming' => true,
                'when_ongoing' => true,
                'table_alias' => 'm',
                'search' => array(
                    'action' => ($aParentModule === null ? ($bIsUserProfile === true ? $this->url()->makeUrl($aUser['user_name'], array('fevent', 'view' => $this->request()->get('view', ''))) : $this->url()->makeUrl('fevent', array('view' => $this->request()->get('view', '')))) : $aParentModule['url'] . 'fevent/view_' . $this->request()->get('view', '') . '/'),
                    'default_value' => _p('fevent.search_events'),
                    'name' => 'search',
                    'field' => array('m.title', 'ft.description')
                ),
                'sort' => array(
                    'latest' => $latestSort,
                    'most-viewed' => array('m.total_view', _p('fevent.most_viewed')),
                    'most-liked' => array('m.total_like', _p('fevent.most_liked')),
                    'most-talked' => array('m.total_comment', _p('fevent.most_discussed')),
                    'featured' => array('m.is_featured', _p('featured')),
                ),
                'show' => array(12, 15, 18, 21),
            )
        );

        $bInHomepage = $this->_checkIsInHomePage();
        $this->setParam('bInHomepage', $bInHomepage);
        $this->setParam('bIsSearch', !$bInHomepage);
        if ($bInHomepage) {
            $aSearch['search_tool']['no_filters'] = [_p('sort'), _p('show'), _p('when')];
            unset($aSearch['search_tool']['custom_filters']);
        }

        $this->search()->set($aSearch);

        $aBrowseParams = array(
            'module_id' => 'fevent',
            'alias' => 'm',
            'field' => 'event_id',
            'table' => Phpfox::getT('fevent'),
            'hide_view' => array('pending', 'my')
        );

        $urlObject = $this->url();
        switch ($sView) {
            case 'pending':
                if (Phpfox::getUserParam('fevent.can_approve_events')) {
                    $this->search()->setCondition('AND m.view_id = 1');
                }
                break;
            case 'my':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND m.user_id = ' . Phpfox::getUserId());
                break;
            default:
                if ($bIsUserProfile) {
                    $this->search()->setCondition('AND m.item_id = 0 AND m.view_id ' . ($aUser['user_id'] == Phpfox::getUserId() ? 'IN(0,2)' : '= 0') . ' AND m.privacy IN(' . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('core')->getForBrowse($aUser)) . ') AND m.user_id = ' . (int) $aUser['user_id']);
                } elseif ($aParentModule !== null) {
                    $this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) AND m.module_id = \'' . Phpfox::getLib('database')->escape($aParentModule['module_id']) . '\' AND m.item_id = ' . (int) $aParentModule['item_id'] . '');
                } else {
                    switch ($sView) {
                        case 'attending':
                            $oServiceEventBrowse->attending(1);
                            break;
                        case 'may-attend':
                            $oServiceEventBrowse->attending(2);
                            break;
                        case 'not-attending':
                            $oServiceEventBrowse->attending(3);
                            break;
                        case 'invites':
                            $oServiceEventBrowse->attending(0);
                            break;
                    }
                    if ($sView == 'attending' || $sView === 'invites' || $sView == 'may-attend') {
                        $this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%)');
                    } else {
                        if ($aCallback !== false) {
                            $this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) AND m.item_id = ' . $aCallback['item'] . '');
                        } else {
                            $this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%)');
                            $sModuleCond = Phpfox::getService('fevent')->getConditionsForSettingPageGroup('m');
                            $this->search()->setCondition($sModuleCond);
                        }
                    }
                    if (($sView == 'pagecalendar'))
                    {
                        if($this->request()->get('req2') != "category"){
                            Phpfox::getLib("url")->send(phpfox::getLib("url")->makeUrl("fevent.pagecalendar", array('view'=>'pagecalendar')));
                        }
                        else
                        {
                            Phpfox::getLib("url")->send(str_replace("/?view=pagecalendar", "", phpfox::getLib("url")->makeUrl('current')));
                        }
                    }

                    if ($this->request()->getInt('user') && ($aUserSearch = Phpfox::getService('user')->getUser($this->request()->getInt('user')))) {
                        $this->search()->setCondition('AND m.user_id = ' . (int) $aUserSearch['user_id']);
                        $this->template()->setBreadCrumb($aUserSearch['full_name'] . '\'s Events', $this->url()->makeUrl('fevent', array('user' => $aUserSearch['user_id'])), true);
                    }
                }
                break;
        }

        if ($this->request()->getInt('sponsor') == 1) {
            $this->search()->setCondition('AND m.is_sponsor != 1');
            Phpfox::addMessage(_p('fevent.sponsor_help'));
        }

        if ($this->request()->get('req2') == 'category') {
            $sCategory = $this->request()->getInt('req3');
            $childCategoryIds= Phpfox::getService('fevent.category')->getChildIds((int)$sCategory);
            $where = '('. (int)$sCategory . (!empty($childCategoryIds) ? ','. trim($childCategoryIds,',') : ''). ')';
            $this->search()->setCondition('AND mcd.category_id IN ' . $where);
        }

        if ($this->request()->get('sort') == 'featured') {
            $this->search()->setCondition('AND m.is_featured = 1');
        }

        $this->setParam('sCategory', $sCategory);

        $oServiceEventBrowse->callback($aCallback)->category($sCategory);

        $this->search()->browse()->params($aBrowseParams)
            ->setPagingMode(Phpfox::getParam('fevent.fevent_paging_mode', 'loadmore'));
        // Custom execute
        if ($sView == 'pagesevents') {
            if(isset($pages) && is_array($pages) && count($pages) > 0){
                $aRows = Phpfox::getService('fevent')->execute($aCallback, $searchParams);
            } else {
                $aRows = [];
            }
        }
        elseif(!$bIsAdvSearch && !$this->request()->get("date"))
        {

            $this->search()->browse()->params($aBrowseParams)->execute();
            $aRows = $this->search()->browse()->getRows();
        }
        else
        {
            $aRows = Phpfox::getService('fevent')->execute($aCallback, $searchParams);
        }


        $sImageOnError = "this.src='" . Phpfox::getLib('template')->getStyle('image', 'noimage/item.png') . "';";

        $bInPage = $this->_checkIsInPage();
        $bInProfile = $bIsUserProfile;
        list($itemWidth, $gap) = $this->__getLayoutSetting($bInHomepage, $bInPage, $bInProfile);

        //  CHECK WARNING UPDATE FOR OLD REPEAT DATA
        $showWarningUpdate = false;
        if('my' == $sView)
        {
            $showWarningUpdate = true;
        }
        $iPage = $this->request()->getInt('page');


        foreach ($aRows as $iKey=>$value) {

            $aRows[$iKey]['description_parsed'] = db()->select('et.description_parsed AS description')
                ->from(':fevent_text', 'et')
                ->where('event_id ='.(int)$aRows[$iKey]['event_id'])
                ->execute('getField');
            $timeNeedToFormatted = in_array($value['d_type'], ['ongoing', 'past']) ? $value['end_time'] : $value['start_time'];
            $aRows[$iKey]['date_formatted'] = Phpfox::getService('fevent.helper')->formatTimeToDate($value['d_type'], $timeNeedToFormatted, 0);

            if(Phpfox::getParam('core.allow_html') && !empty($aRows[$iKey]['description_parsed'])) {
                $oFilter = Phpfox::getLib('parse.input');
                $aRows[$iKey]['description_parsed'] = $oFilter->prepare(htmlspecialchars_decode($aRows[$iKey]['description_parsed']));
            }

        }

        $aSupportedViewModes = Phpfox::getService('fevent.helper')->getSupportedViewModes();
        $this->setParam('aSupportedViewModes', $aSupportedViewModes);
        $this->setParam('sModeViewDefault', 'list');
        $this->setParam('sModeViewId', 'p-fevent-listing');

        $this->template()
            ->setPhrase(['fevent_please_fill_in_range_to_search'])
            ->setTitle(($bInHomepage ? _p('discover_events') : ($bIsUserProfile ? _p('fevent.full_name_s_events', array('full_name' => $aUser['full_name'])) : _p('fevent.events'))))
            ->setBreadCrumb($bInHomepage ? _p('discover_events') : _p('menu_event'),
                $bInHomepage ? $this->url()->makeUrl('fevent') : ($aCallback !== false ? $this->url()->makeUrl($aCallback['url_home'][0], array_merge($aCallback['url_home'][1], array('fevent', 'when_upcoming'))) : ($bIsUserProfile ? $this->url()->makeUrl($aUser['user_name'], 'fevent', 'when_upcoming') : $this->url()->makeUrl('fevent'))))
            ->setHeader('cache', array(
                    'jscript/jquery.jdpicker.js' => 'app_p-advevent',
                    'pager.css' => 'style_css',
                    'comment.css' => 'style_css',
                    'country.js' => 'module_core',
                    'jscript/index.js' => 'app_p-advevent',
                    'feed.js' => 'module_feed',
                    'jscript/fevent.js' => 'app_p-advevent',
                    'jscript/jquery.magnific-popup.js' => 'app_p-advevent',
                    'jscript/picktim.js' => 'app_p-advevent',
                )
            )
            ->setMeta('keywords', Phpfox::getParam('fevent.fevent_meta_keywords'))
            ->setMeta('description', Phpfox::getParam('fevent.fevent_meta_description'))
            ->assign(array(
                    'apiKey' => Phpfox::getParam('core.google_api_key'),
                    'currentUrl' => $currentUrl,
                    'bInHomepage' => $bInHomepage,
                    'corepath' => phpfox::getParam('core.path'),
                    'sApproveLink' => $this->url()->makeUrl('fevent', array('view' => 'pending')),
                    'iPage' => $iPage,
                    'aEvents' => $aRows,
                    'bInPage' => $bInPage,
                    'bInProfile' => $bInProfile,
                    'bIsAdvSearch' => $bIsAdvSearch,
                    'sImageOnError' => $sImageOnError,
                    'sView' => $sView,
                    'itemWidth' => $itemWidth,
                    'gap' => $gap,
                    'showWarningUpdate' => $showWarningUpdate,
                    'aCallback' => $aCallback,
                    'sParentLink' => ($aCallback !== false ? $aCallback['url_home'][0] . '.' . implode('.', $aCallback['url_home'][1]) . '.event' : 'fevent'),
                    'rsvpActionType' => 'list'

                )
            );

        if(!$bInHomepage && $sView == 'all') {
            $this->template()->setBreadCrumb(_p('all_events'), $urlObject->makeUrl('fevent', ['view' => 'all']));
        }

        Phpfox::getService('fevent.helper')->buildSectionMenu();

        if ($sCategory !== null) {
            $aCategories = Phpfox::getService('fevent.category')->getParentBreadcrumb($sCategory);
            $iCnt = 0;
            foreach ($aCategories as $aCategory) {
                $iCnt++;

                $this->template()->setTitle($aCategory[0]);

                if ($aCallback !== false) {
                    $sHomeUrl = '/' . Phpfox::getLib('url')->doRewrite($aCallback['url_home'][0]) . '/' . implode('/', $aCallback['url_home'][1]) . '/' . Phpfox::getLib('url')->doRewrite('fevent') . '/';
                    $aCategory[1] = preg_replace('/^http:\/\/(.*?)\/' . Phpfox::getLib('url')->doRewrite('fevent') . '\/(.*?)$/i', 'http://\\1' . $sHomeUrl . '\\2', $aCategory[1]);
                }
                $this->template()->setBreadCrumb($aCategory[0], $aCategory[1], !$bInHomepage  ? true : false);
            }
        }

        if ($aCallback !== false) {
            $this->template()->rebuildMenu('fevent.index', $aCallback['url_home']);
        }

        Phpfox::getLib('pager')->set(array(
                'page' => $this->search()->getPage(),
                'size' => $this->search()->getDisplay(),
                'count' => Phpfox::getService('fevent')->getCount(),
                'paging_mode' => $this->search()->browse()->getPagingMode()
            )
        );
        $aModerationMenu = [];
        $bShowModerator = false;
        if ($sView == 'pending') {
            if (Phpfox::getUserParam('fevent.can_approve_events')) {
                $aModerationMenu[] = array(
                    'phrase' => _p('approve'),
                    'action' => 'approve'
                );
            }
        } elseif (Phpfox::getUserParam('fevent.can_feature_events')) {
            $aModerationMenu[] = array(
                'phrase' => _p('feature'),
                'action' => 'feature'
            );
            $aModerationMenu[] = array(
                'phrase' => _p('un_feature'),
                'action' => 'un-feature'
            );
        }
        if (Phpfox::getUserParam('fevent.can_delete_other_event')) {
            $aModerationMenu[] = array(
                'phrase' => _p('delete'),
                'action' => 'delete'
            );
        }
        if (count($aModerationMenu)) {
            $this->setParam('global_moderation', array(
                    'name' => 'fevent',
                    'ajax' => 'fevent.moderation',
                    'menu' => $aModerationMenu
                )
            );
            $bShowModerator = true;
        }

        $canDoModeration = $bShowModerator && !$bInHomepage;

        $this->template()->assign([
            'canDoModeration' => $canDoModeration,
            'bShowModerator' => $bShowModerator
        ]);

        if (!$bInHomepage) {
            $sCondition = $this->search()->getConditions();
            $this->template()->assign(array(
                'sCondition' => base64_encode(json_encode($sCondition)),
                'page' => $this->search()->getPage(),
                'limit' => $this->search()->getDisplay()
            ));
        }


        $this->template()->setHeader('<script type="text/javascript">
             $Behavior.removeBigCalendar = function(){
                fevent.removeBigCalendar();
             };
            </script>');

        //Special breadcrumb for pages
        if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW && defined('PHPFOX_PAGES_ITEM_TYPE')){
            if (Phpfox::hasCallback(PHPFOX_PAGES_ITEM_TYPE, 'checkPermission') && !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm($aParentModule['item_id'], 'fevent.view_browse_events')) {
                $this->template()->assign(['aSearchTool' => []]);
                return \Phpfox_Error::display(_p('Cannot display this section due to privacy.'));
            }
            $this->template()
                ->clearBreadCrumb();
            $this->template()
                ->setBreadCrumb(Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getTitle($aParentModule['item_id']), $aParentModule['url'])
                ->setBreadCrumb(_p('fevent.events'), $aParentModule['url'] . 'fevent/');
        }
        else {
            sectionMenu(_p('menu_fevent_add_new_event'), 'fevent.add');
        }

        // To display gmap_view on this controller
        $this->setParam('aGmapView', [
            'type' => 'fevent',
            'url' => $this->url()->makeUrl('fevent.map', ['type' => 'fevent', 'view' => $sView])
        ]);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('fevent.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}