<?php
defined('PHPFOX') or exit('NO DICE!');

$db = db();

//delete section menu : sell new product and open new store
$menuTable = Phpfox::getT('menu');
$unusedMenus = $db->select('menu_id')
    ->from($menuTable)
    ->where('(url_value = "ynsocialstore.add" OR url_value = "ynsocialstore.store.storetype") AND module_id = "ynsocialstore"')
    ->execute('getSlaveRows');
if(!empty($unusedMenus)) {
    $db->delete($menuTable, 'menu_id IN ('. implode(',', array_column($unusedMenus, 'menu_id')) .')');
}

//convert location of block product.dashboard from 7 to 2
$blockTable = Phpfox::getT('block');
$movedBlocks = ['ynsocialstore.manage-photos', 'ynsocialstore.manage-attributes'];

$existingBlocks = $db->select('block_id')
                    ->from($blockTable)
                    ->where('component = "product.dashboard" AND m_connection IN ("' . implode('","', $movedBlocks) .'") AND location = "7" AND module_id = "ynsocialstore"')
                    ->execute('getSlaveRows');
if($existingBlocks) {
    $db->update($blockTable, ['location' => "2"], 'block_id IN ('. implode(',', array_column($existingBlocks, 'block_id')) . ')');
}

//delete unused setting
$removedSettings = [
    'user_can_add_a_comment_on_their_product_or_disable_comment'
];
$userGroupSettingTable = Phpfox::getT('user_group_setting');
$checkedSettings = $db->select('setting_id, name')
                    ->from($userGroupSettingTable)
                    ->where('module_id = "ynsocialstore" AND name IN ("'. implode('","', $removedSettings). '")')
                    ->execute('getSlaveRows');
if(!empty($checkedSettings)) {
    $removedSettingIds = [];
    $removedSettingPhrases = [];
    $checkedSettings = array_combine(array_column($checkedSettings, 'name'), array_column($checkedSettings, 'setting_id'));
    foreach($removedSettings as $removedSetting) {
        if(!empty($checkedSettings[$removedSetting])) {
            $removedSettingIds[] = $checkedSettings[$removedSetting];
            $removedSettingPhrases[] = 'user_setting_' . $removedSetting;
        }
    }
    if(!empty($removedSettingIds)) {
        $db->delete($userGroupSettingTable, 'module_id = "ynsocialstore" AND setting_id IN ('. implode(',', $removedSettingIds) . ')');
        $db->delete(Phpfox::getT('language_phrase'), 'var_name IN ("' . implode('","', $removedSettingPhrases) . '")');
    }
}

if($db->isField(':ecommerce_product_ynstore', 'discount_percentage')) {
    $field = $db->getSlaveRow(('SHOW FIELDS FROM `' . Phpfox::getT('ecommerce_product_ynstore')  .'` WHERE Field = \'discount_percentage\''));
    if(!empty($field) && preg_match('/int/', $field['Type'])) {
        $db->changeField(':ecommerce_product_ynstore', 'discount_percentage', [
            'type' => 'DECIMAL(14,2) UNSIGNED',
            'null' => false,
            'default' => '0.00',
        ]);
    }
}


$removedProductSettings = ['max_item_block_new_arrivals', 'setting_max_item_block_weekly_hot_selling_in_store', 'max_item_block_featured_products', 'max_item_block_weekly_hot_selling', 'max_item_block_new_products', 'max_item_weekly_hot_seller_store', 'max_item_block_featured_stores'];
$checkedProductSettings = $db->select('setting_id, phrase_var_name')
                            ->from(':setting')
                            ->where('module_id=\'ynsocialstore\' AND var_name IN ("' . implode('","', $removedProductSettings) . '")')
                            ->execute('getSlaveRows');
if($checkedProductSettings) {
    $productSettingIds = array_column($checkedProductSettings, 'setting_id');
    $productSettingPhrases = array_column($checkedProductSettings, 'phrase_var_name');

    $db->delete(':setting', 'setting_id IN (' . implode(',' , $productSettingIds) . ')');
    $db->delete(':language_phrase', 'var_name IN ("' . implode('","', $productSettingPhrases) . '")');
}