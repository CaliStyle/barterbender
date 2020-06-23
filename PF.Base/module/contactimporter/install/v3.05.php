<?php
function contactimporter_admin_statistics()
{
	$sTable = Phpfox::getT('contactimporter_admin_statistics');
	$sql = "CREATE TABLE IF NOT EXISTS `$sTable` (
		`statistic_id` int(11) NOT NULL auto_increment,
  		`user_id` int(11) NOT NULL,
  		`provider` varchar(64) NOT NULL,
  		`date` date NOT NULL,
  		`total` int(11) NOT NULL,
		PRIMARY KEY (`statistic_id`)
	) ENGINE=MyISAM AUTO_INCREMENT=1";
	Phpfox::getLib('phpfox.database') -> query($sql);
}

function alter_table_queue_list_and_indexing()
{
	$sTable = Phpfox::getT('contactimporter_invitation_queue_list');
	$oDb = Phpfox::getLib('phpfox.database');

	if (!$oDb -> isField($sTable, 'uid'))
	{
		$sql = "ALTER TABLE `" . $sTable . "` ADD `uid` varchar(128) NOT NULL DEFAULT '0'";
		$oDb -> query($sql);
	}

	if (!$oDb -> isIndex($sTable, 'uid'))
	{
		$sql = "ALTER TABLE `" . $sTable . "` ADD INDEX `uid` (`uid`) ";
		$oDb -> query($sql);
	}
}

function delete_some_providers()
{
    $sTable = Phpfox::getT('contactimporter_providers');
    $aOldData = Phpfox::getLib('phpfox.database')->select('name')
        ->from($sTable,'b')
        ->where('b.name like \'%gmail%\' or b.name like \'%yahoo%\' or b.name like \'%linkedin%\' or b.name like \'%facebook%\' or b.name like \'%hotmail%\' or b.name like \'%twitter%\'')
        ->execute('getSlaveRows');
    $aSelected = [];
    foreach ($aOldData as $key => $aData)
    {
        if(isset($aData['name']))
        {
            $aSelected[] = '\''.$aData['name'].'\'';
        }
    }
    $sSelected = implode(',',$aSelected);
    if(!empty($sSelected))
        Phpfox::getLib('phpfox.database')->delete($sTable,'name NOT IN ('.$sSelected.')');
}

delete_some_providers();
contactimporter_admin_statistics();
alter_table_queue_list_and_indexing();
