<?php
namespace Apps\YNC_Reaction;

use Core\App;

/**
 * Class Install
 * @version 4.01
 * @package Apps\YNC_Reaction
 */
class Install extends App\App
{
    private $_app_phrases = [

    ];

    protected function setId()
    {
        $this->id = 'YNC_Reaction';
    }

    protected function setAlias()
    {
        $this->alias = 'yncreaction';
    }

    protected function setName()
    {
        $this->name = _p('Reaction');
    }

    protected function setVersion()
    {
        $this->version = '4.01p2';
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
        $this->admincp_route = '/admincp/yncreaction/manage-reactions';

        $this->_publisher = 'YouNetCo';
        $this->_publisher_url = 'https://phpfox.younetco.com/';
        $this->admincp_menu = [
            _p('manage_reactions') => '#',
        ];
        $this->admincp_action_menu = [
            '/admincp/yncreaction/add-reaction' => _p('add_new_reaction')
        ];
        $this->_apps_dir = 'ync-reaction';
        $this->_admin_cp_menu_ajax = false;
        $this->database = [
            'Yncreaction_Reactions',
        ];
        $this->_writable_dirs = [
            'PF.Base/file/pic/yncreaction/'
        ];
    }
}