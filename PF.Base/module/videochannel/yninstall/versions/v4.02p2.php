<?php
defined('PHPFOX') or exit('NO DICE!');

$db = db();

$table = Phpfox::getT('setting');
$db->delete($table, 'var_name = "disable_youtube_related_videos" AND module_id = "videochannel"');

$settingId = $db->select('setting_id')
            ->from($table)
            ->where('var_name = "embed_auto_play" AND module_id = "videochannel"')
            ->execute('getSlaveField');
if(!empty($settingId)) {
    $db->update($table,['is_hidden' => 1, 'value_actual' => 0],'setting_id = '. (int)$settingId);
}