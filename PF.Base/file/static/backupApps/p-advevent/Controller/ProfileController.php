<?php
namespace Apps\P_AdvEvent\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class ProfileController extends Phpfox_Component
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
        }

        $aUser = null;
        if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $aUser);
        }
        elseif (defined('PHPFOX_IS_USER_PROFILE')) {
            $aUser = $this->getParam('aUser');
        }

        $aSupportedViewModes = Phpfox::getService('fevent.helper')->getSupportedViewModes();
        $this->setParam('aSupportedViewModes', $aSupportedViewModes);
        $this->setParam('sModeViewDefault', 'list');
        $this->setParam('sModeViewId', 'p-fevent-my');

        $this->search()->set(array(
                'type' => 'fevent',
                'field' => 'm.event_id',
                'search_tool' => array(
                    'default_when' => 'all-time',
                    'when_field' => 'start_time',
                    'when_end_field' => 'end_time',
                    'table_alias' => 'm',
                    'search' => array(
                        'action' => $this->url()->makeUrl($aUser['user_name'].'.fevent'),
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

        if ($sWhen = $this->request()->get('when')) {
            $this->template()->assign(array("sWhen" => $sWhen));
            if($sWhen == 'past')
            {
                $this->search()->setCondition('AND ( m.end_time < ' . (int)Phpfox::getService('fevent.helper')->convertFromUserTimeZone(PHPFOX_TIME) . ' ) ');
            }
        }

        $aBrowseParams = array(
            'module_id' => 'fevent',
            'alias' => 'm',
            'field' => 'event_id',
            'table' => Phpfox::getT('fevent'),
            'hide_view' => array('pending', 'my')
        );

        $this->search()->setCondition('AND m.item_id = 0 AND m.view_id ' . ($aUser['user_id'] == Phpfox::getUserId() ? 'IN (0,2)' : ' = 0') . ' AND m.privacy IN(' . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('core')->getForBrowse($aUser)) . ') AND m.user_id = ' . (int) $aUser['user_id']);


        $this->search()->browse()->params($aBrowseParams)->execute();
        $aRows = $this->search()->browse()->getRows();

        $sImageOnError = "this.src='" . Phpfox::getLib('template')->getStyle('image', 'noimage/item.png') . "';";

        list($itemWidth, $gap) = $this->__getLayoutSetting();

        $aSupportedViewModes = Phpfox::getService('fevent.helper')->getSupportedViewModes();
        $this->setParam('aSupportedViewModes', $aSupportedViewModes);
        $this->setParam('sModeViewDefault', 'list');
        $this->setParam('sModeViewId', 'p-fevent-profile');

        $iPage = $this->request()->getInt('page');
        $this->template()->setPhrase(
            array('fevent.event', 'fevent.time', 'fevent.location', 'fevent.view_this_event')
        )
            ->setTitle(
                _p('full_name_s_events', array('full_name' => $aUser['full_name'])))
            ->setBreadcrumb(_p('events'), $this->url()->makeUrl($aUser['user_name'], 'fevent'))
            ->setHeader('cache', array(
                    'pager.css' => 'style_css',
                    'comment.css' => 'style_css',
                    'country.js' => 'module_core',
                    'jscript/index.js' => 'app_p-advevent',
                    'feed.js' => 'module_feed',
                    'jscript/fevent.js' => 'app_p-advevent',
                )
            )

            ->assign(array(
                    'iPage' => $iPage,
                    'aEvents' => $aRows,
                    'sImageOnError' => $sImageOnError,
                    'itemWidth' => $itemWidth,
                    'gap' => $gap,
                    'showWarningUpdate' => true,
                    'sApproveLink' => $this->url()->makeUrl('fevent', array('view' => 'pending')),
                    'rsvpActionType' => 'list'
                )
            );

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

        $aSupportedViewModes = Phpfox::getService('fevent.helper')->getSupportedViewModes();
        $this->setParam('aSupportedViewModes', $aSupportedViewModes);

        if(defined('PHPFOX_IS_USER_PROFILE') && Phpfox::getUserId() == $aUser['user_id']) {
            sectionMenu(_p('menu_fevent_add_new_event'), 'fevent.add');
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