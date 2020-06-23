<?php

if (isset($query) && isset($local) && isset($result) && Phpfox::isModule('fevent') && isset($page)) {
    $result['fevent'] = [
        'label' => $local->translate('menu_fevent_events'),
        'path' => 'fevent/list-item',
        'params' => [
            'headerTitle' => $local->translate('full_name_s_item', ['full_name' => $page['title'], 'item' => $local->translate('menu_fevent_events')]),
            'query' => $query,
            // Add create button
            'headerRightButtons' => [
                [
                    'icon' => 'plus',
                    'action' => Apps\Core_MobileApi\Adapter\MobileApp\Screen::ACTION_ADD,
                    'params' => [
                        'resource_name' => 'fevent',
                        'module_name' => 'fevent',
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