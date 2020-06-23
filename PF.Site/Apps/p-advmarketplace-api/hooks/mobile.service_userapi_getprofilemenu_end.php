<?php
/**
 * Add user profile menu
 */

if (isset($query) && isset($local) && isset($result) && Phpfox::isModule('advancedmarketplace') && isset($user)) {
    if (isset($showEmpty) && $showEmpty || (isset($user['total_advlisting']) && $user['total_advlisting'])) {
        $result['advancedmarketplace'] = [
            'label'  => $local->translate('menu_advancedmarketplace'),
            'path'   => 'advancedmarketplace/list-item',
            'params' => [
                'headerTitle' => $local->translate('full_name_s_item', ['full_name' => $user['full_name'], 'item' => $local->translate('advancedmarketplace')]),
                'query'       => $query,
            ],
        ];
    }
}