<?php


namespace Apps\P_AdvEventAPI;

use Core\App;

class Install extends App\App
{
    private $_app_phrases = [

    ];

    protected function setId()
    {
        $this->id = 'P_AdvEventAPI';
    }

    protected function setAlias()
    {
        $this->alias = 'adveventapi';
    }

    protected function setName()
    {
        $this->name = 'Advanced Event API';
    }

    protected function setVersion()
    {
        $this->version = '4.01';
    }

    protected function setSupportVersion()
    {
        $this->start_support_version = '4.7.3';
    }

    protected function setSettings()
    {
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
        $this->notifications = [];
        $this->_admin_cp_menu_ajax = false;

        $this->_publisher = 'YouNetCo';
        $this->_publisher_url = 'https://phpfox.younetco.com/';

        $this->_apps_dir = 'p-advevent-api';
    }
}