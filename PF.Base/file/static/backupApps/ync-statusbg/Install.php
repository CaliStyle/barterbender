<?php
namespace Apps\YNC_StatusBg;

use Core\App;

class Install extends App\App
{
    private $_app_phrases = [

    ];

    protected function setId()
    {
        $this->id = 'YNC_StatusBg';
    }

    protected function setAlias()
    {
        $this->alias = 'yncstatusbg';
    }

    protected function setName()
    {
        $this->name = _p('Feed Status Background');
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
        $this->admincp_route = '/admincp/yncstatusbg/manage-collections';

        $this->_publisher = 'YouNetCo';
        $this->_publisher_url = 'https://phpfox.younetco.com/';
        $this->admincp_menu = [
            _p('manage_collections') => '#',
        ];
        $this->admincp_action_menu = [
            '/admincp/yncstatusbg/add-collection' => _p('Add New')
        ];
        $this->_apps_dir = 'ync-statusbg';
        $this->_admin_cp_menu_ajax = false;
        $this->database = [
            'Yncstatusbg_Collections',
            'Yncstatusbg_Backgrounds',
            'Yncstatusbg_Status_Background'
        ];
        $this->_writable_dirs = [
            'PF.Base/file/pic/yncstatusbg/'
        ];
    }
}