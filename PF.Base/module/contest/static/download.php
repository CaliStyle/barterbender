<?php

require_once 'cli.php';

$entry_id = trim($_GET['entry_id'], "/");

$aEntry = Phpfox::getService('contest.entry')->getContestEntryById($entry_id);

if ($aEntry) {
    $sPath = Phpfox::getParam('core.dir_pic') . sprintf($aEntry['image_path'], '_1024');
    $ext = @pathinfo($sPath, PATHINFO_EXTENSION);
    $name = empty($ext) ? str_replace(" ", "_", $aEntry['title']) . ".jpg" : str_replace(" ", "_", $aEntry['title']) . "." . $ext;
    \Phpfox_File::instance()->forceDownload($sPath, $name, null, null, $aEntry['server_id']);
} else {
    echo _p('contest.this_file_do_not_exist');
}
