<?php
Phpfox_Error::skip(true);
defined('PHPFOX') or exit('NO DICE!');

function remove_old_socialbridge_token()
{
    $sTable = Phpfox::getT('socialbridge_agents');

    $sql = "TRUNCATE TABLE `" . $sTable . "`";

    Phpfox::getLib('phpfox.database')->query($sql);
}
remove_old_socialbridge_token();
Phpfox_Error::skip(false);
?>
