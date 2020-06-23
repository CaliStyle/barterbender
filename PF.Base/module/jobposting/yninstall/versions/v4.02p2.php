<?php
$db = db();

$userGroupSettingTable = Phpfox::getT('user_group_setting');
$languageTable = Phpfox::getT('language');
$subscribeTable = Phpfox::getT('jobposting_subscribe');

$settingId = $db->select('setting_id')
            ->from($userGroupSettingTable)
            ->where('name = "point_add_job" AND module_id = "jobposting"')
            ->execute('getSlaveField');
if(!empty($settingId)) {
    $db->update($userGroupSettingTable, ['name' => 'points_jobposting_job'] , 'setting_id = '. (int)$settingId);
    $pointSettingTable = Phpfox::getT('activitypoint_setting');
    if($db->tableExists($pointSettingTable)) {
        $pointSettingId = $db->select('setting_id')
            ->from($pointSettingTable)
            ->where('var_name = "points_jobposting_job" AND module_id = "jobposting"')
            ->execute('getSlaveField');
        if(empty($pointSettingId)) {
            $db->insert($pointSettingTable, [
               'var_name' => 'points_jobposting_job',
                'phrase_var_name' => 'user_setting_points_jobposting_job',
                'module_id' => 'jobposting',
            ]);
        }
    }
}

$settingId = $db->select('setting_id')
            ->from($userGroupSettingTable)
            ->where('name = "can_apply_job_without_fee" AND module_id = "jobposting"')
            ->execute('getSlaveField');
if(empty($settingId)) {
    $languageIds = $db->select('language_id')
                    ->from($languageTable)
                    ->execute('getSlaveRows');
    $text = [];
    foreach($languageIds as $languageId) {
        $text[$languageId] = 'Can apply job without fee?';
    }
    Phpfox::getService('user.group.setting.process')->addSetting([
        'module' => 'jobposting',
        'name' => 'can_apply_job_without_fee',
        'user_group' => [
            '1' => 1,
            '2' => 0,
            '3' => 0,
            '4' => 0,
            '5' => 0
        ],
        'product_id' => 'younet_jobposting4',
        'type' => 'boolean',
        'text' => $text
    ]);
}

$db->delete($userGroupSettingTable,'name = "can_post_updates_on_activity_company" AND module_id = "jobposting"');

//update text phrase
$languages = $this->database()->select('p.phrase_id, p.text, p.text_default')
    ->from($languageTable, 'l')
    ->leftJoin(Phpfox::getT('language_phrase'), 'p', "p.language_id = l.language_id AND p.var_name = '" .$db->escape('user_setting_approve_company_before_displayed') . "'")
    ->order('l.is_default DESC')
    ->execute('getSlaveRows');
if(!empty($languages)) {
    foreach($languages as $language) {
        $update = [];
        if ($language['text'] == "Approve companies before they are publicy displayed.") {
            $update['text'] = 'Approve companies before they are publicly displayed.';
        }
        if ($language['text_default'] == 'Approve companies before they are publicy displayed.') {
            $update['text_default'] = 'Approve companies before they are publicly displayed.';
        }
        if(!empty($update)) {
            $db->update(Phpfox::getT('language_phrase'),$update,'phrase_id = '. (int)$language['phrase_id']);
        }
    }
}

if(!$db->isField($subscribeTable,'subscribe_code')) {
    $db->query('ALTER TABLE `'. $subscribeTable . '` ADD  `subscribe_code` VARCHAR(100) DEFAULT NULL');
    $subscribers = $db->select('subscribe_id, user_id, time_stamp')
                    ->from($subscribeTable)
                    ->execute('getSlaveRows');
    foreach($subscribers as $subscriber) {
        $subscribeCode = md5($subscribers['user_id'] . '_' . $subscribers['time_stamp'] .'_subscribe_code');
        $db->update($subscribeTable, ['subscribe_code' => $subscribeCode], 'subscribe_id = '. (int)$subscriber['subscribe_id']);
    }
}
