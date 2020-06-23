
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

interface Socialad_Service_Ad_Item_Item_Abstract {

	/**
	 * @return: 
	 * id, title, description, image_path 
	 */
	public function getAll($iUserId);
	public function getByName($iUserId, $name);
	public function getItem($iItemId);
	public function getItemUrl($iItemId);
	
	public function getActionData($iItemId, $iUserId);

}
