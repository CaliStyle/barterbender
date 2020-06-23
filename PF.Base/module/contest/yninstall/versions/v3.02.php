<?php

function yncontest302install()
{
    $oDb = Phpfox::getLib('phpfox.database');

    if (!$oDb->isField(Phpfox::getT('contest'), 'is_deleted'))
    {
        $oDb->query("ALTER TABLE  `".Phpfox::getT('contest')."` ADD  `is_deleted` tinyint( 1 ) UNSIGNED NOT NULL DEFAULT  '0'");
    }

    if (!$oDb->isField(Phpfox::getT('contest'), 'begin_time'))
    {
        $oDb->query("ALTER TABLE  `".Phpfox::getT('contest')."` ADD  `begin_time` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `time_stamp`");
        $oDb->query("UPDATE  `".Phpfox::getT('contest')."` SET  `begin_time` = `start_time`");
    }

    if (!$oDb->isField(Phpfox::getT('contest'), 'start_vote'))
    {
        $oDb->query("ALTER TABLE  `".Phpfox::getT('contest')."` ADD  `start_vote` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `stop_time`");
        $oDb->query("UPDATE  `".Phpfox::getT('contest')."` SET  `start_vote` = `start_time`");
    }

    if (!$oDb->isField(Phpfox::getT('contest'), 'stop_vote'))
    {
        $oDb->query("ALTER TABLE  `".Phpfox::getT('contest')."` ADD  `stop_vote` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `start_vote`");
        $oDb->query("UPDATE  `".Phpfox::getT('contest')."` SET  `stop_vote` = `end_time`");
    }

    if (!$oDb->isField(Phpfox::getT('contest'), 'closed_by'))
    {
        $oDb->query("ALTER TABLE  `".Phpfox::getT('contest')."` ADD  `closed_by` int(11) unsigned DEFAULT NULL");
    }

    if (!$oDb->isField(Phpfox::getT('contest'), 'vote_without_join'))
    {
        $oDb->query("ALTER TABLE  `".Phpfox::getT('contest')."` ADD  `vote_without_join` tinyint(1) NOT NULL DEFAULT '0'");
    }

    if (!$oDb->isField(Phpfox::getT('contest_entry'), 'song_path'))
    {
        $oDb->query("ALTER TABLE  `".Phpfox::getT('contest_entry')."` ADD  `song_path` VARCHAR( 255 ) NULL AFTER  `image_path`");
    }

    if (!$oDb->isField(Phpfox::getT('contest_entry'), 'song_server_id'))
    {
        $oDb->query("ALTER TABLE  `".Phpfox::getT('contest_entry')."` ADD  `song_server_id` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `song_path`");
    }
    
    $oDb->delete(Phpfox::getT('component'), 'product_id="younet_contest4" AND module_id="contest" AND m_connection="contest.entry.view"');
    $oDb->delete(Phpfox::getT('component'), 'product_id="younet_contest4" AND module_id="contest" AND component="entry.entry-vote"');
    $oDb->delete(Phpfox::getT('component'), 'product_id="younet_contest4" AND module_id="contest" AND component="contest.contest-owner"');
    
    $oDb->delete(Phpfox::getT('block'), 'product_id="younet_contest4" AND module_id="contest" AND m_connection="contest.entry.view"');
}

yncontest302install();

?>