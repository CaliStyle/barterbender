<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
require_once dirname(dirname(__file__)).'/item/item_abstract.class.php'; 

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		MinhTA
 * @package  		Module_socialad
 */

class Socialad_Service_Ad_Item_Item extends Phpfox_Service
{
	private $_aItemTypes;
	private $_oItem;

	public function __construct() {
		$this->_aItemTypes = array ( 
			'external_url' => array( 
				'phrase' => _p('external_url'),
				'name' => 'external_url', // name is used in html field to distinguish external url with others 
				'id' => 1
			),
			'blog' => array( 
				'phrase' => _p('blog'),
				'name' => 'blog',
				'id' => 2
			),
			'event' => array( 
				'phrase' => _p('event'),
				'name' => 'event',
				'id' => 3,
			),
			'page' => array( 
				'phrase' => _p('page'),
				'name' => 'page',
				'id' => 4
			),
		);
	}

	public function getItemTypesFromIds($aIds) {
		$aResult = array();
		foreach($aIds as $iId) {
			$aResult[] = Phpfox::getService('socialad.helper')->getAllById('ad.itemtype', $iId);
		}

		return $aResult;

	}

	public function getTypeId($sName) {
		foreach($this->_aItemTypes as $aItemType) {
			if($aItemType['name'] == $sName) {
				return $aItemType['id'];
			}
		}

		return 0;
	}

	public function getAllItemTypes() {
		return $this->_aItemTypes;
	}

	public function getAllItems($iItemTypeId, $iUserId, $name) {
		$this->setItemType($iItemTypeId);
		return $this->_oItem->getByName($iUserId, $name);

	}

	public function getItem($iItemId, $iItemTypeId) {
		$this->setItemType($iItemTypeId);
		return $this->_oItem->getItem($iItemId);
	}

	public function getItemUrl($iItemId, $iItemTypeId) {
		$this->setItemType($iItemTypeId);
		return $this->_oItem->getItemUrl($iItemId);
	}

	/**
	 * @return array( 
	 *   data => array,
	 *   template => string corresponds to template to use with {template file='...'}, ex: block.ad.item.like
	 * )
	 */
	public function getActionData($iItemId, $iItemTypeId, $iUserId) {
		$this->setItemType($iItemTypeId);
		return $this->_oItem->getActionData($iItemId, $iUserId);
	}

	public function doItem($iItemId, $iItemTypeId, $iUserId) {
		$this->setItemType($iItemTypeId);
		return $this->_oItem->doItem($iItemId, $iUserId);
	}

	public function undoItem($iItemId, $iItemTypeId, $iUserId) {
		$this->setItemType($iItemTypeId);
		return $this->_oItem->undoItem($iItemId, $iUserId);
	}

	public function setItemType($iItemTypeId) { 
		$sName = Phpfox::getService('socialad.helper')->getNameById('ad.itemtype', $iItemTypeId);

		switch($sName) {
		case 'blog':
			$this->_oItem = Phpfox::getService('socialad.ad.item.blog');
			break;
		case 'event':
			$this->_oItem = Phpfox::getService('socialad.ad.item.event');
			break;
		case 'page':
			$this->_oItem = Phpfox::getService('socialad.ad.item.page');
			break;
		case 'default':
			// defualt is page 
			$this->_oItem = Phpfox::getService('socialad.ad.item.page');
			break;
		}
	}

}



