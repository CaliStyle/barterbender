<?php

namespace Apps\YNC_Core;

use Core\App;

/**
 * Class Install
 * @author  Neil J. <neil@phpfox.com>
 * @version 4.6.0
 * @package Apps\YNC_Core
 */
class Install extends App\App
{
    private $_app_phrases = [

    ];

    protected function setId()
    {
        $this->id = 'YNC_Core';
    }

    protected function setAlias()
    {
        $this->alias = 'ynccore';
    }

    protected function setName()
    {
        $this->name = _p('YouNetCo Core');
    }

    protected function setVersion()
    {
        $this->version = '4.01p1';
    }

    protected function setSupportVersion()
    {
        $this->start_support_version = '4.6.0';
    }

    protected function setSettings()
    {
        $this->settings = [];
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
        $this->_publisher = 'YouNetCo';
        $this->_publisher_url = 'https://phpfox.younetco.com/';
        $this->_apps_dir = 'ync-core';
        $this->_admin_cp_menu_ajax = false;
    }
}