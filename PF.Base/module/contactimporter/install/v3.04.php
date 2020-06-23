<?php

function contactimporter_migrateDataFromQueueTableToNewQueueTable() {
	if(contactimporter_checkTableExist(Phpfox::getT('contactimporter_invitation_queue_list')) &&
		   !contactimporter_doesTableHaveData(Phpfox::getT('contactimporter_invitation_queue_list')))
	{
		$aQueues = Phpfox::getLib('database')->select('*')
				->from(Phpfox::getT('contactimporter_queue'))
				->execute('getSlaveRows');

		$aField = array(
			'queue_id',
			'provider',
			'is_sent',
			'friend_id'
		);

	// thousands of query doesn't matter
		foreach ($aQueues as $aQueue) {
			if ($aQueue['friend_ids']) {
				if ($aQueue['provider'] == 'facebook' || $aQueue['provider'] == 'twitter') {
					$aFriends = unserialize($aQueue['friend_ids']);
					$iQueueId = $aQueue['queue_id'];
					$sProvider = $aQueue['provider'];
				} else if ($aQueue['provider'] == 'yahoo' || $aQueue['provider'] == 'gmail' || $aQueue['provider'] == 'hotmail') {
					$aTemp = unserialize($aQueue['friend_ids']);
					$iQueueId = $aQueue['queue_id'];
					$sProvider = $aQueue['provider'];
					$aFriends = array();
					foreach ($aTemp as $t) {
						$aFriends[] = $t->email;
					}
				} else {
					return false;
					// don't know what to do yet
				}

				$aInserts = array();
				foreach ($aFriends as $iFriendId) {
					$aInsert = array(
						$iQueueId,
						$sProvider,
						0,
						$iFriendId
					);
					$aInserts[] = $aInsert;
				}

				Phpfox::getLib('database')->multiInsert(Phpfox::getT('contactimporter_invitation_queue_list'), $aField, $aInserts);
			}
		}
	}
}

function contactimporter_checkTableExist($sTable) {
	$aRow = Phpfox::getLib('database')->query('SHOW TABLES LIKE \'' . $sTable . '\'');

	if (count($aRow) > 0) {
		return true;
	} else {
		return false;
	}
}

function contactimporter_doesTableHaveData($sTable) {
	$aRow = Phpfox::getLib('database')->select('*')
			->from($sTable)
			->limit(1)
			->execute('getSlaveRow');

	if (count($aRow) > 0) {
		return true;
	} else {
		return false;
	}
}





function contactimporter_install304() {
	$sTable = Phpfox::getT('contactimporter_invitation_queue_list');

	$sql = "CREATE TABLE IF NOT EXISTS `$sTable` (
			`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`time_stamp` int(11) unsigned NOT NULL,
			`queue_id` int(10) unsigned NOT NULL ,
			`is_sent` TINYINT(1) NOT NULL DEFAULT '0',
			`is_failed` TINYINT(1) NOT NULL DEFAULT '0',
			`error_message` varchar(512),
			`provider` varchar(20) NOT NULL,
			`friend_id` varchar(100) NOT NULL,
			PRIMARY KEY (`id`),
			KEY `queue_failed` (`queue_id`, `is_failed`),
			KEY `queue_sent_failed` (`queue_id`, `is_sent`, `is_failed`),
			KEY `is_sent` (`is_sent`),
			KEY `time_stamp` (`time_stamp`),
			KEY `queue_id` (`queue_id`)
		)  AUTO_INCREMENT=1;";

	Phpfox::getLib('database')->query($sql);


	contactimporter_migrateDataFromQueueTableToNewQueueTable();


}



contactimporter_install304();


