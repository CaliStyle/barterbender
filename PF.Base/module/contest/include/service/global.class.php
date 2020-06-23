<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Service_Global extends Phpfox_service {
	private $_aItemId = array();

	public function setItemId($iItemId)
	{
		$this->_aItemId[] = $iItemId;
	}

	public function getFirstItemId()
	{
		return isset($this->_aItemId[0]) ? $this->_aItemId[0] : false;
	}
}