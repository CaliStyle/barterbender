<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		MinhTA
 * @package  		Module_socialad
 */

class Socialad_Service_User extends Phpfox_Service
{
	private $_aUser;

	private function _getUserInfo($iUserId) {
		$aRow = $this->database()->select('u.*')
			->from(Phpfox::getT('user'), 'u')
			->where('u.user_id = ' . $iUserId)
			->execute('getRow');
		if(!$aRow) {
			return false;
		}

		$aRow['age'] = Phpfox::getService('user')->age(isset($aRow['birthday']) ? $aRow['birthday'] : '');
		$aRow['location'] = $aRow['country_iso']; // we will improve it later to deal with cities 
		$aRow['language'] = $aRow['language_id']; 
		return $aRow;
	}

	public function resetUser() {
		$this->_aUser = null;
	}

	public function getUserParam($sParam, $iUserId) {

		$aParts = explode('.', $sParam);

		$iGroupId = Phpfox::getService('socialad.user')->getUserBy('user_group_id', $iUserId);

		return Phpfox::getService('user.group.setting')->getGroupParam($iGroupId, $sParam);

	}


	public function getUserBy($sVar, $iUserId ) {

		$result = $this->_getUserInfo($iUserId);
		if (isset($result[$sVar]))
		{
			return $result[$sVar];
		}

		return false;
	}


}



