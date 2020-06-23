<?php
namespace Apps\Core_Subscriptions;

use Core\App;


class Install extends App\App
{
    private $_app_phrases = [
    ];
    protected function setId()
    {
        $this->id = 'Core_Subscriptions';
    }
    protected function setAlias()
    {
        $this->alias = 'subscribe';
    }
    protected function setName()
    {
        $this->name = _p('module_subscribe');
    }
    protected function setVersion()
    {
        $this->version = '4.6.5';
    }
    protected function setSupportVersion()
    {
        $this->start_support_version = '4.7.2';
    }
    protected function setSettings()
    {
        $this->settings = [
            'enable_subscription_packages' => [
                'var_name' => 'enable_subscription_packages',
                'info' => 'Enable Subscription Packages',
                'description' => 'Enable Subscription Packages',
                'type' => 'boolean',
                'value' => '1',
                'ordering' => 1,

            ],
            'subscribe_is_required_on_sign_up' => [
                'var_name' => 'subscribe_is_required_on_sign_up',
                'info' => 'Subscription on registration is required ?',
                'description' => 'If members should be required to select a subscription package when they register set this to <b>Yes</b>.',
                'type' => 'boolean',
                'value' => '1',
                'ordering' => 2,
            ],
        ];
    }
    protected function setUserGroupSettings()
    {

    }
    protected function setComponent()
    {
        $this->component = [
            'block' => [
                'message' => '',
            ],
            'controller' => [
                'index' => 'subscribe.index',
            ],
            'ajax' => [

            ]
        ];
    }
    protected function setComponentBlock()
    {

    }
    protected function setPhrase()
    {
        $this->addPhrases($this->_app_phrases);
    }
    protected function setOthers()
    {
        $this->admincp_route = "admincp.subscribe";
        $this->admincp_menu = [
            _p('admin_menu_manage_packages') => 'subscribe',
            _p('subscribe_menu_subscriptions_title') => 'subscribe.list',
            _p('admin_menu_comparison') => 'subscribe.compare',
            _p('subscribe_menu_cancel_reason_title') => 'subscribe.reason'
        ];
        $this->admincp_action_menu = [
            '/admincp/subscribe/add' => _p('subscribe_new_package_title')
        ];
        $this->menu = [

        ];
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
        $this->_apps_dir = "core-subscriptions";
        $this->_writable_dirs = [
            'PF.Base/file/pic/subscribe/'
        ];

        $this->database = [
            'Subscribe_Package', 'Subscribe_Purchase', 'Subscribe_Compare', 'Subscribe_Recent_Payment', 'Subscribe_Reason', 'Subscribe_Cancel_Reason'
        ];

        $this->_admin_cp_menu_ajax = false;
    }
}