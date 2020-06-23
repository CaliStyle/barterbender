<?php

function contactimporter_402p2()
{
    db()->delete(':contactimporter_providers', 'name = "yahoo" OR name = "linkedin"');

    //change user group setting name
    $userGroupSettingTable = Phpfox::getT('user_group_setting');
    $where = [
        'name' => 'points_sent_invitations',
        'module_id' => 'contactimporter'
    ];
    $count = db()->select('COUNT(*)')
                ->from($userGroupSettingTable)
                ->where($where)
                ->execute('getSlaveField');

    if($count) {
        db()->update($userGroupSettingTable, ['name' => 'points_contactimporter_sentinvitations'], $where);
        $activityPointSettingTable = Phpfox::getT('activitypoint_setting');
        if(db()->tableExists($activityPointSettingTable)) {
            $where = [
                'var_name' => 'points_sent_invitations',
                'module_id' => 'contactimporter'
            ];
            $count = db()->select('COUNT(*)')
                ->from($activityPointSettingTable)
                ->where($where)
                ->execute('getSlaveField');
            if($count) {
                db()->update($activityPointSettingTable, [
                    'var_name' => 'points_contactimporter_sentinvitations',
                    'phrase_var_name' => 'user_setting_points_contactimporter_sentinvitations'
                ], $where);
            }
        }
    }
}

contactimporter_402p2();

