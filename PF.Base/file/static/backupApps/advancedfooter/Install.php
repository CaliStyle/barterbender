<?php
/**
 * @copyright		[FOXEXPERT_COPYRIGHT]
 * @author  		Belan Ivan
 * @package  		App_AdvancedFooter
 */
namespace Apps\Advancedfooter;

defined('PHPFOX') or exit('NO DICE!');

use Core\App;

/**
 * Class Install
 * @package Apps\Core_Blogs
 */
class Install extends App\App
{
    private $_app_phrases = [

    ];

    public $store_id = 0;

    protected function setId()
    {
        $this->id = 'Advancedfooter';
    }

    protected function setSupportVersion()
    {
        $this->start_support_version = '4.6.0';
        $this->end_support_version = '';
    }

    protected function setAlias()
    {
        $this->alias = 'advancedfooter';
    }

    protected function setName()
    {
        $this->name = _p('Advanced Footer');
    }

    protected function setVersion()
    {
        $this->version = '4.8';
    }

    protected function setSettings()
    {
        $this->settings = [
            'advancedfooterdesign' => [
                'var_name' => 'advancedfooterdesign',
                'info' => 'Select footer design',
                'description' => 'check sample of design on app purchase page',
                'type' => 'select',
                'value' => 'design1',
                'options' => [
                    'design1' => 'Design 1',
                    'design2' => 'Design 2',
                    'design3' => 'Design 3',
                    'design4' => 'Design 4',
                    'design5' => 'Design 5',
                ],
                'ordering' => 1,
            ],
            'footerbackgroundcolor' => [
                'var_name' => 'footerbackgroundcolor',
                'info' => 'Footer background hex code (leave empty if you want to use default) with #',
                'description' => 'if it light, you need to select light theme to show links and etc correctly, use transparent word to remove background',

            ],
            'footerbgopacity' => [
                'var_name' => 'footerbgopacity',
                'info' => 'Footer background image opacity',
                'description' => 'enter value between 0.01 up 1, leave empty if you want to use default 0.1',

            ],
            'enablejoinshareimage' => [
                'var_name' => 'enablejoinshareimage',
                'info' => 'Enable guest join image?',
                'description' => '',
                'type' => 'boolean',
                'value' => 1,
                'ordering' => 1,
            ],
            'userwidgetlogic' => [
                'var_name' => 'userwidgetlogic',
                'info' => 'Select user widget sorting',
                'description' => 'Select user widget sorting',
                'type' => 'select',
                'value' => 'recent',
                'options' => [
                    'recent' => 'Recent users',
                    'featured' => 'Featured users',
                    'popular' => 'Popular users (count friends)'
                ],
                'ordering' => 1,
            ],
            'advancedfootertheme' => [
                'var_name' => 'advancedfootertheme',
                'info' => 'Theme',
                'description' => 'Select theme for advanced footer',
                'type' => 'select',
                'value' => 'dark',
                'options' => [
                    'dark' => 'Dark theme',
                    'light' => 'Light theme'
                ],
                'ordering' => 1,
            ],
            'enablebgimage' => [
                'var_name' => 'enablebgimage',
                'info' => 'Enable transparent background image?',
                'description' => '',
                'type' => 'boolean',
                'value' => 1,
                'ordering' => 1,
            ],
            'footerbgimage' => [
                'var_name' => 'footerbgimage',
                'info' => 'Background image link (leave empty if you want to use default)',
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
                'main' => '',
            ]
        ];
    }

    protected function setComponentBlock()
    {
        $this->component_block = [
        ];
    }

    protected function setPhrase()
    {
        $this->addPhrases($this->_app_phrases);
    }

    protected function setOthers()
    {
        $this->database = [
            'Advancedfooter_Category',
            'Advancedfooter_Menu'
        ];
        $this->admincp_route = "/advancedfooter/admincp";
        $this->admincp_menu = [
            'Manage Menus' => 'advancedfooter.index',
            'Manage Social Links' => 'advancedfooter.social',
        ];
        $this->_publisher = 'Foxexpert';
        $this->_publisher_url = 'http://foxexpert.com';
        $this->_apps_dir = "advancedfooter";

        $this->_admin_cp_menu_ajax = false;
    }
}
