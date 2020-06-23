<?php

require_once 'cli.php';

updateUserStatus();

function updateUserStatus()
{
    $file = YNCHAT_DIR . 'cache/online-users.txt';
    if (false === ($userIds = file_get_contents($file))) {
        echo 'Can not read cache file.';
        return false;
    }

    if (!empty($userIds)) {
        Phpfox::getLib('database')->update('ynchat_status', array('status' => 'available'), 'user_id IN (' . $userIds . ')');
        Phpfox::getLib('database')->update('ynchat_status', array('status' => 'offline'), 'user_id NOT IN (' . $userIds . ')');
    } else {
        Phpfox::getLib('database')->update('ynchat_status', array('status' => 'offline'));
    }

    echo 'Users status have been updated.';
    return true;
}
