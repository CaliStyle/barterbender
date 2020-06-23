<?php
namespace Apps\YNC_Affiliate;

use Core\App;
use Core\App\Install\Setting;

/**
 * Class Install
 * @author  Neil
 * @version 4.5.0
 * @package Apps\YNC_Affiliate
 */
class Install extends App\App
{
    private $_app_phrases = [

    ];

    protected function setId()
    {
        $this->id = 'YNC_Affiliate';
    }

    protected function setAlias()
    {
        $this->alias = 'yncaffiliate';
    }

    protected function setName()
    {
        $this->name = 'Affiliate';
    }

    protected function setVersion()
    {
        $this->version = '4.02';
    }

    protected function setSupportVersion()
    {
        $this->start_support_version = '4.6.0';
    }

    protected function setSettings()
    {
        $this->settings = [
            'ynaf_auto_approve' => [
                'var_name'    => 'ynaf_auto_approve',
                'info' => '<b>Auto Approve</b> <br> Do you want to approve member automatically?',
                'type' => Setting\Site::TYPE_RADIO,
                'value' => '1',
                'js_variable' => true
            ],
            'ynaf_intergrate_invitation' => [
                'var_name' => 'ynaf_intergrate_invitation',
                'info' => '<b>Intergration with invitation</b> <br> Do you want to intergrate with invitations?',
                'type' => Setting\Site::TYPE_RADIO,
                'value' => '1',
                'js_variable' => true
            ],
            'ynaf_number_commission_levels' => [
                'var_name' => 'ynaf_number_commission_levels',
                'info' => '<b>Number of Commission Levels</b>',
                'type' => Setting\Site::TYPE_SELECT,
                'value'       => '5',
                'options'       => [
                    '0' => '0',
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                ],
                'js_variable' => true
            ],
            'ynaf_number_users_per_level_network_clients' => [
                'var_name' => 'ynaf_number_users_per_level_network_clients',
                'info' => '<b>Number of users per level on Network Clients page</b>',
                'type' => Setting\Site::TYPE_TEXT,
                'value' => '3',
                'js_variable' => true
            ],
            'ynaf_minimum_request_points' => [
                'var_name' => 'ynaf_minimum_request_points',
                'info' => '<b>Minimum Request Points</b>',
                'type' => Setting\Site::TYPE_TEXT,
                'value' => '1',
                'js_variable' => true
            ],
            'ynaf_maximum_request_points' => [
                'var_name' => 'ynaf_maximum_request_points',
                'info' => '<b>Maximum Request Points</b>',
                'type' => Setting\Site::TYPE_TEXT,
                'value' => '1000',
                'js_variable' => true
            ],
            'ynaf_delay_time_refunds_and_disputes' => [
                'var_name' => 'ynaf_delay_time_refunds_and_disputes',
                'info' => '<b>Delay time for refunds and disputes (days)</b> <br> Each commission of new transaction will have a delay time to allow for refunds and disputes',
                'type' => Setting\Site::TYPE_TEXT,
                'value' => '0',
                'js_variable' => true
            ],
        ];
    }

    protected function setUserGroupSettings()
    {
        $this->user_group_settings = [
            'ynaf_can_register_affiliate' => [
                'var_name' => 'ynaf_can_register_affiliate',
                'info' => 'Can register to be an Affiliate?',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0',],
                'options' => Setting\Groups::$OPTION_YES_NO,
            ],
            'ynaf_auto_approve_commission' => [
                'var_name' => 'ynaf_auto_approve_commission',
                'info' => 'Auto approve commission after delay time?',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0',],
                'options' => Setting\Groups::$OPTION_YES_NO,
            ],
        ];
    }

    protected function setComponent()
    {
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
        $this->admincp_route = '/yncaffiliate/admincp';

        $this->admincp_menu = [
            "Manage Affiliates" => "yncaffiliate.manage-affiliate",
            "Commission Rules" => "yncaffiliate.commission-rule",
            "Manage Commissions" => "yncaffiliate.manage-commissions",
            "Module Statistics" => "#",
            "Manage FAQs" => "yncaffiliate.manage-faq",
            "Add New FAQs" => "yncaffiliate.add-faq",
            "Manage Request" => "yncaffiliate.manage-request",
            "Manage Codes" => "yncaffiliate.affiliate-materials",
            "Affiliate Points Conversion Rate" => "yncaffiliate.conversion-rate",
            "Term of Service" => "yncaffiliate.term-service",
            "Affiliate's Client" => "yncaffiliate.affiliate-client",
        ];

        $this->menu = [
            "name" => "Affiliate",      // Menu label
            "phrase_var_name" => "Affiliate",      // Menu label
            "url" => "/affiliate",     // Menu Url
            "icon" => "share-alt-square"
        ];
        $this->_publisher = 'YouNetCo';
        $this->_publisher_url = 'https://phpfox.younetco.com/';

        $this->_apps_dir = 'ync-affiliate';
        $this->_admin_cp_menu_ajax = false;
        $this->icon = 'https://static.younetco.com/ynicons/fox4/affiliate.png';
        $this->database = [
            'yncaffiliate_accounts',
            'yncaffiliate_commissions',
            'yncaffiliate_faqs',
            'yncaffiliate_links',
            'yncaffiliate_assoc',
            'yncaffiliate_materials',
            'yncaffiliate_requests',
            'yncaffiliate_rulemap_details',
            'yncaffiliate_rulemaps',
            'yncaffiliate_rules',
            'yncaffiliate_suggests',
        ];
    }
}