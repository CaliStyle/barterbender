<?php

namespace Apps\P_AdvMarketplaceAPI;

use Core\App;

class Install extends App\App
{
    private $_app_phrases = [

    ];

    protected function setId()
    {
        $this->id = 'P_AdvMarketplaceAPI';
    }

    protected function setAlias()
    {
        $this->alias = 'advmarketplaceapi';
    }

    protected function setName()
    {
        $this->name = 'Advanced Marketplace API';
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
        return;
        $iIndex = 1;
        $this->settings = [
            'sort_type_by_default' => [
                'var_name' => 'sort_type_by_default',
                'info' => 'Sort type by default',
                'description' => 'Sort type that you want to apply as default.',
                'type' => 'select',
                'value' => 'latest',
                'options' => [
                    'latest' => 'Latest',
                    'most_viewed' => 'Most viewed',
                    'most_liked' => 'Most liked',
                    'most_discussed' => 'Most discussed',
                    'low_high_price' => 'Low to high price',
                    'high_low_price' => 'High to low price'
                ],
                'ordering' => $iIndex++,
            ],
            'period_time_by_default' => [
                'var_name' => 'period_time_by_default',
                'info' => 'Period time by default',
                'description' => 'Period time that you want to apply as default.',
                'type' => 'select',
                'value' => 'all-time',
                'options' => [
                    'all-time' => 'All time',
                    'today' => 'Today',
                    'this-week' => 'This week',
                    'this-month' => 'This month',
                    'featured' => 'Featured',
                    'sponsored' => 'Sponsored',
                ],
                'ordering' => $iIndex++,
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
        $this->notifications = [];
        $this->_admin_cp_menu_ajax = false;

        $this->_publisher = 'YouNetCo';
        $this->_publisher_url = 'https://phpfox.younetco.com/';

        $this->_apps_dir = 'p-advmarketplace-api';
    }
}