<?php
Phpfox_Error::skip(true);
defined('PHPFOX') or exit('NO DICE!');

function remove_plugin_younet_feed_hooking()
{
    $sTable = Phpfox::getT('plugin');
    $sql = "DELETE FROM `" . $sTable . "` WHERE title LIKE 'YouNet Feed Hooking'";
    Phpfox::getLib('phpfox.database')->query($sql);
}

remove_plugin_younet_feed_hooking();
?>

