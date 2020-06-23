<?php
$userGroupSettingTable = Phpfox::getT('user_group_setting');
$settingId = db()->select('setting_id')
                ->from($userGroupSettingTable)
                ->where(['name' => 'can_add_comment_on_feedback', 'module_id' => 'feedback'])
                ->execute('getSlaveField');
if(!empty($settingId)) {
    db()->delete($userGroupSettingTable, ['name' => 'can_add_comment_on_feedback', 'module_id' => 'feedback']);
    db()->delete(Phpfox::getT('language_phrase'), ['var_name' => 'user_setting_can_add_comment_on_feedback']);
}