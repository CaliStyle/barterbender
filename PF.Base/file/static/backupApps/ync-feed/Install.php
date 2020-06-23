<?php

namespace Apps\YNC_Feed;

use Core\App;
use Core\App\Install\Setting;
use Phpfox;


class Install extends App\App
{
    private $_app_phrases = [

    ];

    protected function setId()
    {
        $this->id = 'YNC_Feed';
    }

    protected function setAlias()
    {
        $this->alias = 'ynfeed';
    }

    protected function setName()
    {
        $this->name = _p('Advanced Feed');
    }

    protected function setVersion()
    {
        $this->version = '4.03p4';
    }

    protected function setSupportVersion()
    {
        $this->start_support_version = '4.7.6';
    }

    protected function setSettings()
    {
        $smanageFilterUrl = \Phpfox_Url::instance()->makeUrl('admincp.ynfeed.manage-filter');
        $this->settings = [
            'ynfeed_enable_auto_loading_by_scrolling_down' => [
                'info' => '<b>Enable auto-loading older newsfeed by scrolling down?</b> <br/> If YES, system will autoload newsfeed when user scroll down, else users have to manually reload it.',
                'type' => Setting\Site::TYPE_RADIO,
                'value' => '1',
                'js_variable' => true,
            ],
            'ynfeed_number_of_filter_to_show_out' => [
                'info' => '<b>Number of filter to be shown out?</b> <br/> Maximum number of filter to be shown to Newsfeed. The others will be contained in dropdown list. Highest priorities for filters on top of the <a href="' . $smanageFilterUrl . '">list</a>.',
                'value' => 6
            ],
        ];

    }

    protected function setUserGroupSettings()
    {
        $this->user_group_settings = [];
    }

    protected function setComponent()
    {
        $this->component = [
            "block" => [
                "checknew" => "",
                "comment" => "",
                "display" => "",
                "edit_user_status" => "",
                "form" => "",
                "form2" => "",
                "load_dates" => "",
                "mini" => "",
                "rating" => "",
                "share" => ""
            ],
            "controller" => [
                "comments" => "ynfeed.comments",
                "form" => "ynfeed.form",
                "stream" => "ynfeed.stream",
                "user" => "ynfeed.user",
                "view" => "ynfeed.view",
            ]
        ];
    }

    protected function setComponentBlock()
    {
        $this->component_block = [
            "Advanced Activity Feed" => [
                "type_id" => "0",
                "m_connection" => "core.index-member",
                "component" => "display",
                "location" => "2",
                "is_active" => "1",
                "ordering" => "9"
            ],
            "Profile Advanced Activity Feed" => [
                "type_id" => "0",
                "m_connection" => "profile.index",
                "component" => "display",
                "location" => "2",
                "is_active" => "1",
                "ordering" => "7"
            ],
            "Event Advanced Activity Feed" => [
                "type_id" => "0",
                "m_connection" => "event.view",
                "component" => "display",
                "location" => "4",
                "is_active" => "1",
                "ordering" => "7"
            ],
            "Page Advanced Feed Display" => [
                "type_id" => "0",
                "m_connection" => "pages.view",
                "component" => "display",
                "location" => "2",
                "is_active" => "1",
                "ordering" => "10"
            ],
            "Group Advanced Feed Display" => [
                "type_id" => "0",
                "m_connection" => "groups.view",
                "component" => "display",
                "location" => "2",
                "is_active" => "1",
                "ordering" => "10"
            ],
            "Advanced Event Advanced Activity Feed" => [
                "type_id" => "0",
                "m_connection" => "fevent.view",
                "component" => "display",
                "location" => "4",
                "is_active" => "1",
                "ordering" => "2"
            ],
        ];
    }

    protected function setPhrase()
    {
        $this->phrase = $this->_app_phrases;
    }

    protected function setOthers()
    {
        $this->_apps_dir = "ync-feed";
        $this->admincp_route = Phpfox::getLib('url')->makeUrl('admincp.app.settings', ['id' => 'YNC_Feed']);
        $this->_admin_cp_menu_ajax = false;
        $this->admincp_menu = [
            'Settings' => '#',
            'Add New Filter' => 'ynfeed.add-filter',
            'Manage Filters' => 'ynfeed.manage-filter',
        ];
        $this->_publisher = 'YouNetCo';
        $this->_publisher_url = 'https://phpfox.younetco.com/';
        $this->database = [
            'YnFeed_Emoticon',
            'YnFeed_Filter',
            'YnFeed_Hide',
            'YnFeed_Saved',
            'YnFeed_Feed_Map',
            'YnFeed_Feeling',
            'YnFeed_Extra_Info',
            'YnFeed_Notification_Map',
            'YnFeed_Turnoff_Notification'
        ];
        $this->icon = 'https://static.younetco.com/ynicons/fox4/advanced_feed.png';
    }

    /**
     * @param bool $bRemoveDb
     */
    public function uninstall($bRemoveDb = true)
    {
        parent::uninstall($bRemoveDb);
        // enable all blocks of feed
        db()->update(':block', array('is_active' => 1), "component = 'display' AND (module_id = 'feed' OR module_id = 'fevent')");
        db()->delete(':plugin', array(
            'module_id' => 'core',
            'product_id' => 'phpfox',
            'call_name' => 'admincp.service_module_process_updateactivity',
            'title' => 'Advanced Feed Hook Update App'
        ));
    }
}