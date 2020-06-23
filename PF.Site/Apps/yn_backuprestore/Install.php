<?php
namespace Apps\yn_backuprestore;

use Core\App;
use Core\App\Install\Setting;

/**
 * Class Install
 * @author  Neil
 * @version 4.5.0
 * @package Apps\Younet_BackupRestore
 */
class Install extends App\App
{
    private $_app_phrases = [

    ];

    protected function setId()
    {
        $this->id = 'yn_backuprestore';
    }

    protected function setAlias()
    {
        $this->alias = 'ynbackuprestore';
    }

    protected function setName()
    {
        $this->name = _p('module_ynbackuprestore');
    }

    protected function setVersion()
    {
        $this->version = '4.02p3';
    }

    protected function setSupportVersion()
    {
        $this->start_support_version = '4.6.1';
        $this->end_support_version = '';
    }

    protected function setSettings()
    {
        $this->settings = [
            'ynbackuprestore_auto_remove'   => [
                'var_name'    => 'ynbackuprestore_auto_remove',
                'info'        => 'Automatically remove backups older than',
                'type'        => Setting\Site::TYPE_TEXT,
                'value'       => '0',
                'description' => '(days)'
            ],
            'ynbackuprestore_item_per_page' => [
                'var_name' => 'ynbackuprestore_item_per_page',
                'info'     => 'Maximum number of item per page (Manage Backup, Manage Schedule)',
                'type'     => Setting\Site::TYPE_TEXT,
                'value'    => '8',
            ]
        ];
    }

    protected function setUserGroupSettings()
    {
        $this->user_group_settings = [

        ];
    }

    protected function setComponent()
    {
        $this->component = [
        ];
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
        $this->admincp_route = "/ynbackuprestore/admincp";
        $this->admincp_menu = [
            'Backup Now'          => 'ynbackuprestore.backup',
            'Manage Backups'      => 'ynbackuprestore.manage-backup',
            'Add New Destination' => 'ynbackuprestore.add-destination',
            'Manage Destinations' => 'ynbackuprestore.destination',
            'Add New Schedule'    => 'ynbackuprestore.add-schedule',
            'Manage Schedules'    => 'ynbackuprestore.manage-schedule',
            'Restore Now'         => 'ynbackuprestore.restore',
        ];
        $this->_admin_cp_menu_ajax = false;
        $this->_publisher = 'YouNetCo';
        $this->_publisher_url = 'https://phpfox.younetco.com/';
    }
}