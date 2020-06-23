<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
class Fevent_Component_Controller_Profile extends Phpfox_Component
{
    public function curPageURL() {
        return phpfox::getLib('url')->getFullUrl();
      
        if(isset($_SERVER['HTTP_REFERER']))
            return $_SERVER['HTTP_REFERER'];
        else
        {
          $pageURL = 'http';
            if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
                $pageURL .= "s";
            }
            $pageURL .= "://";
            if ($_SERVER["SERVER_PORT"] != "80") {
                $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
            } else {
                $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            }
            return $pageURL;
        }
    }

    private function __getLayoutSetting()
    {
        $itemWidth = 0; 
        $gap = 0; 
        $numberOfItemsInRow = 0; 
        // $itemWidth = Phpfox::getParam('fevent.fevent_profile_item_width'); 
        // $gap = Phpfox::getParam('fevent.fevent_profile_gap');  
        // $numberOfItemsInRow = Phpfox::getParam('fevent.fevent_profile_number_of_items_on_row');  

        // return array(intval($itemWidth), intval($gap), intval($numberOfItemsInRow));
        return array(intval($itemWidth), intval($gap));
    }

    public function process() {
       
        Phpfox::getUserParam('fevent.can_access_event', true);
       
        if (!$this->request()->get("when")) { 
            if ($this->curPageURL() == phpfox::getLib("url")->makeUrl("fevent"))
            {
				echo "";
				phpfox::getLib("url")->send(phpfox::getLib("url")->makeUrl("fevent") . "when_upcoming/");
			}
            if ($this->request()->get('req2') == "category") {
                phpfox::getLib("url")->send($this->curPageURL() . "when_upcoming/");
            }
        }
       
        if($this->request()->get("date")!="" && $this->request()->get('req2')=="category")
        {
            phpfox::getLib("url")->send(phpfox::getLib("url")->makeUrl("fevent") . "date_".$this->request()->get("date")."/when_all-time/");
        }
        $aParentModule = $this->getParam('aParentModule');
        $bIsPage = $aParentModule['module_id'] == 'pages' ? $aParentModule['item_id'] : 0;

        if ($aParentModule === null && $this->request()->getInt('req2') > 0) {
            return Phpfox::getLib('module')->setController('fevent.view');
        }

        if (($sLegacyTitle = $this->request()->get('req2')) && !empty($sLegacyTitle)) {
            if ($this->request()->get('req3') != '') {
                $sLegacyTitle = $this->request()->get('req3');
            }

            $aLegacyItem = Phpfox::getService('core')->getLegacyItem(array(
                'field' => array('category_id', 'name'),
                'table' => 'fevent_category',
                'redirect' => 'fevent.category',
                'title' => $sLegacyTitle,
                'search' => 'name_url'
                    )
            );
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
                    $this->url()->send('fevent', null, _p('event_successfully_deleted'));
                } else {
                    $this->url()->forward($mDeleteReturn, _p('event_successfully_deleted'));
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
        $sView = $this->request()->get('view', 'all');
        $aCallback = $this->getParam('aCallback', false);

        $this->search()->set(array(
            'type' => 'fevent',
            'field' => 'm.event_id',
            'search_tool' => array(
                'default_when' => 'all-time',
                'when_field' => 'start_time',
                'when_end_field' => 'end_time',
                'table_alias' => 'm',
                'search' => array(
                    'action' => ($aParentModule === null ? ($bIsUserProfile === true ? $this->url()->makeUrl($aUser['user_name'], array('fevent', 'view' => $this->request()->get('view', 'all'))) : $this->url()->makeUrl('fevent', array('view' => $this->request()->get('view', 'all')))) : $aParentModule['url'] . 'event/view_' . $this->request()->get('view', 'all') . '/'),
                    'default_value' => _p('search_events'),
                    'name' => 'search',
                    'field' => array('m.title', 'ft.description')
                ),
                'sort' => array(
                    'latest' => array('m.time_stamp', _p('latest'), 'DESC'),
                    'most-viewed' => array('m.total_view', _p('most_viewed')),
                    'most-liked' => array('m.total_like', _p('most_liked')),
                    'most-talked' => array('m.total_comment', _p('most_discussed'))
                ),
                'show' => array(10, 15, 18, 21)
            )
                )
        );
        //d($this->search()->getConditions());exit;
        if ($sWhen = $this->request()->get('when')) {
            //if($sWhen=="upcoming")
            //{
            //print_r($this->search()->getConditions());die();
            //}

            $this->template()->assign(array("sWhen" => $sWhen));
        }

        $aBrowseParams = array(
            'module_id' => 'fevent',
            'alias' => 'm',
            'field' => 'event_id',
            'table' => Phpfox::getT('fevent'),
            'hide_view' => array('pending', 'my')
        );

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

                    if ($sView == 'attending') {
                        $this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%)');
                    } else {
                        $this->search()->setCondition('AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) AND m.item_id = ' . ($aCallback !== false ? (int) $aCallback['item'] : 0) . '');
                    }

                    if ($this->request()->getInt('user') && ($aUserSearch = Phpfox::getService('user')->getUser($this->request()->getInt('user')))) {
                        $this->search()->setCondition('AND m.user_id = ' . (int) $aUserSearch['user_id']);
                        $this->template()->setBreadcrumb($aUserSearch['full_name'] . '\'s Events', $this->url()->makeUrl('fevent', array('user' => $aUserSearch['user_id'])), true);
                    }
                }
                break;
        }
         $sponsor = 0;
        if ($this->request()->getInt('sponsor') == 1) {
            $this->search()->setCondition('AND m.is_sponsor != 1');
            Phpfox::addMessage(_p('sponsor_help'));
            $sponsor = 1;
        }

        if ($this->request()->get('req2') == 'category') {
            $sCategory = $this->request()->getInt('req3');
            $this->search()->setCondition('AND mcd.category_id = ' . (int) $sCategory);
        }

        if ($sView == 'featured') {
            $this->search()->setCondition('AND m.is_featured = 1');
        }

        $this->setParam('sCategory', $sCategory);
       
        $oServiceEventBrowse->callback($aCallback)->category($sCategory);

        $this->search()->browse()->params($aBrowseParams);
        // Custom execute
        $aRows = Phpfox::getService('fevent')->execute($bIsPage);


        Phpfox::getService('fevent.helper')->buildSectionMenu();

        $sImageOnError = "this.src='" . Phpfox::getLib('template')->getStyle('image', 'noimage/item.png') . "';";

        // list($itemWidth, $gap, $numberOfItemsInRow) = $this->__getLayoutSetting(); 
        list($itemWidth, $gap) = $this->__getLayoutSetting(); 

        //  CHECK WARNING UPDATE FOR OLD REPEAT DATA
        $showWarningUpdate = false;
        if('my' == $sView)
        {
            $showWarningUpdate = true;
        }
        $iPage = $this->request()->getInt('page');
        $this->template()->setPhrase(
                        array('fevent.event', 'fevent.time', 'fevent.location', 'fevent.view_this_event')
                )
                ->setTitle(
                    ($bIsUserProfile ? _p('full_name_s_events', array('full_name' => $aUser['full_name'])) : _p('events')))
                ->setBreadcrumb(
                    _p('events')
                    , ($aCallback !== false 
                            ? $this->url()->makeUrl($aCallback['url_home'][0]
                                , array_merge($aCallback['url_home'][1]
                                        , array('fevent')
                                    )
                                ) 
                            : ($bIsUserProfile 
                                    ? $this->url()->makeUrl($aUser['user_name'], 'fevent') 
                                    : $this->url()->makeUrl('fevent')
                                )
                        )
                )
                ->setHeader('cache', array(
                    'pager.css' => 'style_css',                    
                    'comment.css' => 'style_css',
                    'country.js' => 'module_core',
                    'index.js' => 'module_fevent',
                    'feed.js' => 'module_feed',
                       'fevent.js' => 'module_fevent',
                    )
                )
             
                ->assign(array(
                    'iPage' => $iPage,
                    'sponsor' => $sponsor,
                    'aEvents' => $aRows,
                    'sImageOnError' => $sImageOnError,
                    'sView' => $sView,
                    'itemWidth' => $itemWidth,
                    'gap' => $gap,
                    // 'numberOfItemsInRow' => $numberOfItemsInRow,
                    'showWarningUpdate' => $showWarningUpdate,
                    'aCallback' => $aCallback,
                    'sParentLink' => ($aCallback !== false ? $aCallback['url_home'][0] . '.' . implode('.', $aCallback['url_home'][1]) . '.event' : 'fevent'),
                    'sApproveLink' => $this->url()->makeUrl('fevent', array('view' => 'pending'))
                )
        );
        //d($aRows,true);exit;

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

                $this->template()->setBreadcrumb($aCategory[0], $aCategory[1], (empty($sView) ? true : false));
            }
        }

        if ($aCallback !== false) {
            $this->template()->rebuildMenu('fevent.index', $aCallback['url_home']);
        }

        Phpfox::getLib('pager')->set(array('page' => $this->search()->getPage(), 'size' => $this->search()->getDisplay(), 'count' => Phpfox::getService('fevent')->getCount()));

        $this->setParam('global_moderation', array(
            'name' => 'fevent',
            'ajax' => 'fevent.moderation',
            'menu' => array(
                array(
                    'phrase' => _p('delete'),
                    'action' => 'delete'
                ),
                array(
                    'phrase' => _p('approve'),
                    'action' => 'approve'
                )
            )
                )
        );


        // featured events
        list($iTotal, $aFeatured) = Phpfox::getService('fevent')->getFeatured($bIsPage, false);
        if ($iTotal) {
            $this->template()->assign(array(
                'sCorePath' => Phpfox::getParam('core.path'),
                'aFeatured' => $aFeatured
            ));
        }
    }
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fevent.component_controller_profile_clean')) ? eval($sPlugin) : false);
	}
}

?>