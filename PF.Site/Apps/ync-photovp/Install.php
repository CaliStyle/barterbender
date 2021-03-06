<?php

namespace Apps\YNC_PhotoViewPop;

use Core\App;

/**
 * Class Install
 * @author  YouNetCo
 * @version 4.01
 * @package Apps\YNC_PhotoViewPop
 */
class Install extends App\App
{
    private $_app_phrases = [

    ];

    protected function setId()
    {
        $this->id = 'YNC_PhotoViewPop';
    }

    protected function setAlias()
    {
        $this->alias = 'yncphotovp';
    }

    protected function setName()
    {
        $this->name = _p('yncphotovp_app');
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
        $this->_apps_dir = "ync-photovp";
    }
}