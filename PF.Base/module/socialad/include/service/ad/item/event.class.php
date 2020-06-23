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

class Socialad_Service_Ad_Item_Event extends Phpfox_Service implements Socialad_Service_Ad_Item_Item_Abstract {
	public function __construct() {
		$this->_sTable = Phpfox::getT('event');
		$this->_sTableAlias = Phpfox::getT('e');
		$this->_iItemTypeId = Phpfox::getService('socialad.helper')->getConst('ad.itemtype.event', 'id');
		$this->_isUsingAdvEvent = Phpfox::getService('socialad.permission')->isUsingAdvEvent();

		if($this->_isUsingAdvEvent){
			$this->_sTable = Phpfox::getT('fevent');
		}
	}

	public function doItem($iItemId, $iUserId) {
		if($this->_isUsingAdvEvent){
			Phpfox::getService('fevent.process')->addRsvp($iItemId, $iRsvp = 1, $iUserId);			
		} else {
			Phpfox::getService('event.process')->addRsvp($iItemId, $iRsvp = 1, $iUserId);			
		}
		return true;
	}

	public function undoItem($iItemId, $iUserId) {
		if($this->_isUsingAdvEvent){
			Phpfox::getService('fevent.process')->removeInvite($iItemId);			
		} else {
			Phpfox::getService('event.process')->removeInvite($iItemId);			
		}
		return true;
	}

	public function getItemName($iItemId) {
		$aItem = $this->getItem($iItemId);

		return $aItem['title'];
	}

	public function checkIsJoined($iItemId, $iUserId) {
		$table = Phpfox::getT('event_invite');
		if($this->_isUsingAdvEvent){
			$table = Phpfox::getT('fevent_invite');
		}

		$sCond = 'event_id = ' . $iItemId . 
			' AND user_id = ' . $iUserId . 
			' AND rsvp_id = 1 ' ;
		$aRow = $this->database()->select('*')
			->from($table)
			->where($sCond)
			->execute('getRow');

		return $aRow ? true : false;
	}
	
	public function getTotalDo($iItemId) {
		$table = Phpfox::getT('event_invite');
		if($this->_isUsingAdvEvent){
			$table = Phpfox::getT('fevent_invite');
		}

		$sConds = 'event_id = ' . $iItemId . 
			' AND rsvp_id = 1 ' ;

		$iCnt = $this->database()->select('COUNT(*)')
			->from($table)
			->where($sConds)
			->execute('getSlaveField');	

		return $iCnt;

	}

	public function getActionData($iItemId, $iUserId) {
		$bIsDid = $this->checkIsJoined($iItemId, $iUserId);
		$iTotalDo = $this->getTotalDo($iItemId);
		$sItemName = $this->getItemName($iItemId);

		if(strlen($sItemName) > 20) {
			$sItemName = _p('this');
		}

		$sPhrase = _p('be_the_first_to_attend');

		if($iTotalDo == 1) {
			$sPhrase = _p('one_person_attends_item_name', array(
				'item_name' => ""
			));
		} else if ($iTotalDo > 1) {
			$sPhrase = _p('number_people_attend_item_name', array(
				'item_name' => "",
				'number' => $iTotalDo	
			));
		}

		if($bIsDid) { 
			$sOnclickEvent = "$.ajaxCall('socialad.undoItem', 'item_id={$iItemId}&item_type_id={$this->_iItemTypeId}'); return false;";
			
		} else {
			$sOnclickEvent = "$.ajaxCall('socialad.doItem', 'item_id={$iItemId}&item_type_id={$this->_iItemTypeId}'); return false;";
		}
		return array(
			'data' => array(
				'bIsDid' => $bIsDid,
				'sActionPhrase' => $bIsDid ? _p('leave') : _p('attend'),
				'sOnclickEvent' => $sOnclickEvent,
				'sPhrase' => $sPhrase,
				'sIconHtml' => '' ,
			), 
			'template' => 'socialad.block.ad.action.join'
		);
	}
	public function getItemUrl($iItemId) {
		if($this->_isUsingAdvEvent){
			return Phpfox::getLib('url')->makeUrl('fevent.' . $iItemId);
		} else {
			return Phpfox::getLib('url')->makeUrl('event.' . $iItemId);
		}
	}

	/**
	 * @return list(iCont, aRow) 
	 */
	public function get($aConds) {
		$sCond = implode(" AND " , $aConds);

		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTable, 'e')
			->where($sCond)
			->execute('getSlaveField');	

		$table = Phpfox::getT('event_text');
		if($this->_isUsingAdvEvent){
			$table = Phpfox::getT('fevent_text');
		}
		$aItems = $this->database()->select('e.event_id as id,
		   									 e.title as title,
											 e.image_path,
										 	 et.description as description')
				->from($this->_sTable, 'e')
				->leftJoin($table, 'et', 'e.event_id = et.event_id')
				->where($sCond)
				->order('e.time_stamp DESC')
				->execute('getSlaveRows');

		foreach($aItems as &$aItem) { 
			$aItem['is_have_image'] = 0;

			if($aItem['image_path']) {
				$aItem['is_have_image'] = 1;
				$aItem['image_original_full_path'] = sprintf(Phpfox::getParam('event.dir_image') . $aItem['image_path'] , "");
			}
		}
		return array($iCnt, $aItems);
	}
	public function getAll($iUserId, $iPage = 0 , $iLimit = 0 ) {

		$aConds = array( 
			'e.user_id = ' . Phpfox::getUserId() . ' '
		);

		return $this->get($aConds);
			
	}

	public function getItem($iItemId) {
		$aConds = array( 
			'e.event_id = ' . $iItemId
		);

		list($iCnt, $aRows) = $this->get($aConds);
		return (count($aRows) > 0) ? $aRows[0] : false;
	}

	public function getByName($iUserId, $name, $iPage = 0 , $iLimit = 0 ) {
		$aConds = array( 
			'e.user_id = ' . Phpfox::getUserId() . ' '
		);
		
		if(empty($name)){
			return $this->getLatestItems($aConds);
		}

		$aConds[] = 'e.title LIKE \'%' . $name . '%\' ';

		return $this->get($aConds);
	}	

	public function getLatestItems($aConds){
		$sConds = implode(' AND ', $aConds);
		$iCnt = 10;

		$table = Phpfox::getT('event_text');
		if($this->_isUsingAdvEvent){
			$table = Phpfox::getT('fevent_text');
		}

		$aItems = $this->database()->select('e.event_id as id,
		   									 e.title as title,
											 e.image_path,
										 	 et.description as description')
				->from($this->_sTable, 'e')
				->leftJoin($table, 'et', 'e.event_id = et.event_id')
				->where($sConds)
				->limit($iCnt)
				->order('e.time_stamp DESC')
				->execute('getSlaveRows');

		foreach($aItems as &$aItem) { 
			$aItem['is_have_image'] = 0;

			if($aItem['image_path']) {
				$aItem['is_have_image'] = 1;
				$aItem['image_original_full_path'] = sprintf(Phpfox::getParam('event.dir_image') . $aItem['image_path'] , "");
			}
		}
		return array($iCnt, $aItems);
	}	
}



