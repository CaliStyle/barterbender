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

class Socialad_Service_Ad_Item_Page extends Phpfox_Service implements Socialad_Service_Ad_Item_Item_Abstract {
	public function __construct() {
		$this->_sTable = Phpfox::getT('pages');
		$this->_sTableAlias = Phpfox::getT('p');
		$this->_sLikeTypeId = 'pages';
	}

	public function getTotalLike($iItemId) {
		$sConds = "type_id = '" . $this->_sLikeTypeId . "' " . 
			      ' AND item_id = ' . $iItemId;

		$iCnt = $this->database()->select('COUNT(*)')
			->from(Phpfox::getT('like'), 'b')
			->where($sConds)
			->execute('getSlaveField');	

		return $iCnt;
		
	}


	public function getItemName($iItemId) {
		$aItem = $this->getItem($iItemId);

		return $aItem['title'];
	}

	public function getActionData($iItemId, $iUserId) {
		$bIsLiked = Phpfox::getService('like')->didILike($this->_sLikeTypeId, $iItemId);
		$iTotalLike = $this->getTotalLike($iItemId);
		$sItemName = $this->getItemName($iItemId);

		if(strlen($sItemName) > 20) {
			$sItemName = _p('this');
		}

		$sPhrase = '';

		if($iTotalLike == 1) {
			$sPhrase = _p('one_person_likes_item_name', array(
				'item_name' => ""
			));
		} else if ($iTotalLike > 1) {
			$sPhrase = _p('number_people_likes_item_name', array(
				'item_name' => "",
				'number' => $iTotalLike	
			));
		}

		return array(
			'data' => array(
				'sPhrase' => $sPhrase,
				'bIsLiked' => $bIsLiked,
				'sActionTypeId' => $this->_sLikeTypeId,
				'iItemId' => $iItemId,
				'iItemTypeId' => Phpfox::getService('socialad.helper')->getConst('ad.itemtype.page', 'id')
			), 
			'template' => 'socialad.block.ad.action.like'
		);
	}
	public function getItemUrl($iItemId) {
		return Phpfox::getLib('url')->makeUrl('pages.' . $iItemId);
	}

	public function getItem($iItemId) {

		$aConds = array( 
			'p.page_id = ' . $iItemId
		);

		list($iCnt, $aRows) = $this->get($aConds);
		return (count($aRows) > 0) ? $aRows[0] : false;
	}

	public function get($aConds) {
		$sConds = implode(' AND ', $aConds);
		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTable, 'p')
			->where($sConds)
			->execute('getSlaveField');	
			
		$aItems = $this->database()->select('p.page_id as id,
		   									 p.title as title,
											 pt.text as description,
											 p.image_path')
				->from($this->_sTable, 'p')
				->leftJoin(Phpfox::getT('pages_text'), 'pt', 'p.page_id = pt.page_id')
				->where($sConds)
				->order('time_stamp DESC')
				->execute('getSlaveRows');

		foreach($aItems as &$aItem) { 
			$aItem['is_have_image'] = 0;

			if($aItem['image_path']) {
				$aItem['is_have_image'] = 1;
				$aItem['image_original_full_path'] = sprintf(Phpfox::getParam('pages.dir_image') . $aItem['image_path'] , "");
			}
		}
		return array($iCnt, $aItems);

	}
	public function getAll($iUserId, $iPage = 0 , $iLimit = 0 ) {
		$aConds = array( 
			'p.user_id = ' . $iUserId, 
		);

		return $this->get($aConds);

	}

	public function getByName($iUserId, $name, $iPage = 0 , $iLimit = 0 ) {
		$aConds = array( 
			'p.user_id = ' . $iUserId, 
		);
		if(empty($name)){
			return $this->getLatestItems($aConds);
		}

		$aConds[] = 'p.title LIKE \'%' . $name . '%\' ';

		return $this->get($aConds);
	}	

	public function getLatestItems($aConds){
		$sConds = implode(' AND ', $aConds);
		$iCnt = 10;
		$aItems = $this->database()->select('p.page_id as id,
		   									 p.title as title,
											 pt.text as description,
											 p.image_path')
				->from($this->_sTable, 'p')
				->leftJoin(Phpfox::getT('pages_text'), 'pt', 'p.page_id = pt.page_id')
				->where($sConds)
				->limit($iCnt)
				->order('time_stamp DESC')
				->execute('getSlaveRows');

		foreach($aItems as &$aItem) { 
			$aItem['is_have_image'] = 0;

			if($aItem['image_path']) {
				$aItem['is_have_image'] = 1;
				$aItem['image_original_full_path'] = sprintf(Phpfox::getParam('pages.dir_image') . $aItem['image_path'] , "");
			}
		}

		return array($iCnt, $aItems);
	}

}



