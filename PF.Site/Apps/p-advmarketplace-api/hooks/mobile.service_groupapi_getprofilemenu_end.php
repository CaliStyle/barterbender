<?php

if (isset($query) && isset($local) && isset($result) && Phpfox::isModule('advancedmarketplace') && isset($page)) {
    $result['advancedmarketplace'] = [
        'label' => $local->translate('menu_advancedmarketplace'),
        'path' => 'advancedmarketplace/list-item',
        'params' => [
            'headerTitle' => $local->translate('full_name_s_item', ['full_name' => $page['title'], 'item' => $local->translate('menu_advancedmarketplace')]),
            'query' => $query,
            // Add create button
            'headerRightButtons' => [
                [
                    'icon' => 'plus',
                    'action' => Apps\Core_MobileApi\Adapter\MobileApp\Screen::ACTION_ADD,
                    'params' => [
                        'resource_name' => 'advancedmarketplace',
                        'module_name' => 'advancedmarketplace',
                        'query' => [
                            'item_id' => $page['page_id'],
                            'module_id' => 'groups'
                        ]
                    ]
                ]
            ]
        ],
    ];
}