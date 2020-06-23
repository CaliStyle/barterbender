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

class Socialad_Service_Ad_Item_Blog extends Phpfox_Service implements Socialad_Service_Ad_Item_Item_Abstract {
	public function __construct() {
		$this->_sTable = Phpfox::getT('blog');
		$this->_sTableAlias = Phpfox::getT('b');
		$this->_sLikeTypeId = 'blog';
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
	public function getItemById($iItemId) {
		$aConds = array();
		$aConds[] = 'b.blog_id = '  . $iItemId;
		list($iCont, $aRows) =  $this->query($aConds);

		return $aRows ? $aRows[0] : false;
	}

	public function getItemName($iItemId) {
		$aItem = $this->getItemById($iItemId);

		return $aItem['title'];
	}

	public function query($aConds) {
		$sConds = implode(' AND ', $aConds);
		//in case we encounter a post form, we know it is a search request
		
		$iCnt = $this->database()->select('COUNT(*)')
			->from($this->_sTable, 'b')
			->where($sConds)
			->execute('getSlaveField');	
			
		$aItems = $this->database()->select('b.blog_id as id,
		   									 b.title as title,
										 	 bt.text as description')
				->from($this->_sTable, 'b')
				->leftJoin(Phpfox::getT('blog_text'), 'bt', 'b.blog_id = bt.blog_id')
				->where($sConds)
				->order('time_stamp DESC')
				->execute('getSlaveRows');
		foreach($aItems as &$aItem) { 
			$aItem['is_have_image'] = 0;
		}

		return array($iCnt, $aItems);

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
				'iItemTypeId' => Phpfox::getService('socialad.helper')->getConst('ad.itemtype.blog', 'id')
			), 
			'template' => 'socialad.block.ad.action.like'
		);
	}

	public function getItemUrl($iItemId) {
		return Phpfox::getLib('url')->makeUrl('blog.' . $iItemId);
	}

	public function getAll($iUserId, $iPage = 0 , $iLimit = 0 ) {
		$aConds = array();
		$aConds[] = 'b.user_id = ' . Phpfox::getUserId() . ' ';
		
		return $this->query($aConds);
	}

	public function getByName($iUserId, $name, $iPage = 0 , $iLimit = 0 ) {
		$aConds = array();
		$aConds[] = 'b.user_id = ' . Phpfox::getUserId() . ' ';
		$aConds[] = ' b.is_approved = 1  ';
		$aConds[] = ' b.post_status = 1 ';

		if(empty($name)){
			return $this->getLatestItems($aConds);
		}

		$aConds[] = 'b.title LIKE \'%' . $name . '%\' ';
		
		return $this->query($aConds);
	}

	public function getItem($iItemId) {

	}

	public function getLatestItems($aConds){
		$iCnt = 10;
		$sConds = implode(' AND ', $aConds);
		//in case we encounter a post form, we know it is a search request

		$aItems = $this->database()->select('b.blog_id as id,
		   									 b.title as title,
										 	 bt.text as description')
				->from($this->_sTable, 'b')
				->leftJoin(Phpfox::getT('blog_text'), 'bt', 'b.blog_id = bt.blog_id')
				->where($sConds)
				->limit($iCnt)
				->order('time_stamp DESC')
				->execute('getSlaveRows');
		foreach($aItems as &$aItem) { 
			$aItem['is_have_image'] = 0;
		}

		return array($iCnt, $aItems);
	}
}



