<?php
defined('PHPFOX') or exit('NO DICE!');

class Ynchat_Service_Database_Ynmysql extends Phpfox_Database_Driver_Mysql
{
	public function ping(){
		if (!(@(mysql_ping($this->_hMaster)))) {
			return false;
		}

		return true;
	}

	public function checkAlive(){
		return $this->ping();
	}

	public function reconnect($force_reconnect = false){
		$this->close();
		$this->connect(Phpfox::getParam(array('db', 'host')), Phpfox::getParam(array('db', 'user')), Phpfox::getParam(array('db', 'pass')), Phpfox::getParam(array('db', 'name')), Phpfox::getParam(array('db', 'port')));
	}
}

?>