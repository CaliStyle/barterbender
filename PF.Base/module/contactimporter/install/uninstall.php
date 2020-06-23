<?php

Phpfox::getLib('phpfox.database')->query("UPDATE `" . Phpfox::getT('menu') . "` SET is_active = 1 WHERE module_id = 'invite' AND url_value='invite'");
Phpfox::getLib('phpfox.database')->query("UPDATE `" . Phpfox::getT('menu') . "` SET is_active = 1 WHERE module_id = 'invite' AND url_value='invite.invitations'");

$sql = "UPDATE `" . Phpfox::getT('user_group_setting') . "` SET `default_admin` = '1', `default_user` = '1', `default_staff` = '1' WHERE `name` = \"points_invite\" AND `module_id` = \"invite\"";
Phpfox::getLib('phpfox.database')->query($sql);
?>