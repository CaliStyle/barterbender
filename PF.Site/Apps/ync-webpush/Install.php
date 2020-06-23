<?php
namespace Apps\YNC_WebPush;

use Core\App;

/**
 * Class Install
 * @author  Neil J. <neil@phpfox.com>
 * @version 4.6.0
 * @package Apps\YNC_WebPush
 */
class Install extends App\App
{
    private $_app_phrases = [

    ];

    protected function setId()
    {
        $this->id = 'YNC_WebPush';
    }

    protected function setAlias()
    {
        $this->alias = 'yncwebpush';
    }

    protected function setName()
    {
        $this->name = _p('Web Push Notification');
    }

    protected function setVersion()
    {
        $this->version = '4.01';
    }

    protected function setSupportVersion()
    {
        $this->start_support_version = '4.6.0';
    }

    protected function setSettings()
    {
        $iIndex = 1;
        $this->settings = [
            'yncwebpush_notification_expiration_time' => [
                'var_name' => 'yncwebpush_notification_expiration_time',
                'info' => 'Notification expiration time (seconds)',
                'description' => 'Define how many seconds before notifications expire. Default is 86400 seconds (24 hours)',
                'type' => 'integer',
                'value' => '86400',
                'ordering' => $iIndex++
            ],
            'yncwebpush_time_period_to_appear_banner' => [
                'var_name' => 'yncwebpush_time_period_to_appear_banner',
                'info' => 'The fix time period for the request banner to reappear',
                'description' => '(eg. 3h 4m 12s) Define the time period for the request banner to reappear after user skipped it. Default is 10 minutes',
                'type' => 'string',
                'value' => '10m',
                'ordering' => $iIndex++
            ],
            'yncwebpush_skip_times_to_stop_request_banner' => [
                'var_name' => 'yncwebpush_skip_times_to_stop_request_banner',
                'info' => 'How many time(s) user skip the request banner that make it stop appearing',
                'description' => 'Define the maximum skip time that make the request banner to stop appearing. Default is 3 times',
                'type' => 'integer',
                'value' => '3',
                'ordering' => $iIndex++
            ],
            'yncwebpush_text_of_banner_for_guest' => [
                'var_name' => 'yncwebpush_text_of_banner_for_guest',
                'info' => 'Text shown on request banner (For Guest)',
                'description' => 'Enter the message to be showed to guest users on request banner. Maximum 250 characters',
                'type' => 'large_string',
                'value' => 'Allow Web Push Notification to get notified from our website even when it is not currently opened',
                'ordering' => $iIndex++
            ],
            'yncwebpush_text_of_banner_for_user' => [
                'var_name' => 'yncwebpush_text_of_banner_for_user',
                'info' => 'Text shown on request banner (For Logged User)',
                'description' => 'Enter the message to be showed to logged users on request banner. Maximum 250 characters',
                'type' => 'large_string',
                'value' => 'Allow Web Push Notification to get notified from our website. You can unsubscribe notifications from site Admin in Push Notification Settings',
                'ordering' => $iIndex++
            ],
            'yncwebpush_server_key' => [
                'var_name' => 'yncwebpush_server_key',
                'info' => 'Project Server Key',
                'description' => 'The server key of your firebase project.<br/>Notice: You must config FCM settings so your site would be able to push notification. Follow the instruction below for how to do it: <a href="https://firebase.google.com/docs/web/setup" target="_blank" class="no_ajax">Add Firebase to your web app</a>.<br/>You can found your server key in your <b>Firebase App console > Project Settings > Cloud Messaging</b>',
                'type' => 'string',
                'value' => '',
                'ordering' => $iIndex++
            ],
            'yncwebpush_auth_code_snippet' => [
                'var_name' => 'yncwebpush_auth_code_snippet',
                'info' => 'FCM Auth Code Snippet',
                'description' => 'The snippet got from your firebase project for adding firebase to your web app.<br/>Notice: You can found this snippet code in your <b>Firebase App console > Project Settings > General > Add Firebase to your web app</b>',
                'type' => 'large_string',
                'value' => '',
                'ordering' => $iIndex++
            ]
        ];
        unset($iIndex);
    }

    protected function setUserGroupSettings()
    {
    }

    protected function setComponent()
    {
    }

    protected function setComponentBlock()
    {
    }

    protected function setPhrase()
    {
        $this->phrase = $this->_app_phrases;
    }

    protected function setOthers()
    {
        $this->admincp_route = '/admincp/yncwebpush/manage-notifications';

        $this->_publisher = 'YouNetCo';
        $this->_publisher_url = 'https://phpfox.younetco.com/';
        $this->admincp_menu = [
            _p('send_push_notification') => 'yncwebpush.send-push-notification',
            _p('manage_notifications') => '#',
            _p('manage_subscribers') => 'yncwebpush.manage-subscribers',
            _p('manage_templates') => 'yncwebpush.manage-templates',
        ];
        $this->_apps_dir = 'ync-webpush';
        $this->_admin_cp_menu_ajax = false;
        $this->database = [
            'Yncwebpush_User_Token',
            'Yncwebpush_Browser_Token',
            'Yncwebpush_User_Notification',
            'Yncwebpush_User_Setting',
            'Yncwebpush_Notification',
            'Yncwebpush_Notification_Audience',
            'Yncwebpush_Template'
        ];
        $this->_writable_dirs = [
            'PF.Base/file/pic/yncwebpush/'
        ];
    }
}