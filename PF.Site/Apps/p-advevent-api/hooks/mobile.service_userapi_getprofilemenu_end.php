<?php
/**
 * Add user profile menu
 */

if (isset($query) && isset($local) && isset($result) && Phpfox::isModule('fevent') && isset($user)) {
    if (isset($showEmpty) && $showEmpty || (isset($user['total_fevent']) && $user['total_fevent'])) {
        $result['fevent'] = [
            'label'  => $local->translate('menu_fevent_events'),
            'path'   => 'fevent/list-item',
            'params' => [
                'headerTitle' => $local->translate('full_name_s_item', ['full_name' => $user['full_name'], 'item' => $local->translate('menu_fevent_events')]),
                'query'       => $query,
            ],
        ];
    }
}