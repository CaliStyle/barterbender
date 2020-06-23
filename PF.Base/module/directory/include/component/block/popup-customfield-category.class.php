<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Popup_Customfield_Category extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iCategoryId = (int)$this->getParam('category_id');
		
		$sCategory = Phpfox::getService('directory.category')->getForEdit($iCategoryId);
		$iParentCategoryId = $iCategoryId;
		if(isset($sCategory['parent_id']) && (int)$sCategory['parent_id'] > 0){
			$iParentCategoryId = (int)$sCategory['parent_id'];
		}

		$aGroupsInfo = Phpfox::getService('directory.category')->getCustomGroup($iParentCategoryId);
		
		foreach ($aGroupsInfo as $key => $aGroup) {
			$aGroupsInfo[$key]['phrase_var_name'] = _p($aGroup['phrase_var_name']);
		}

		$this->template()->assign(array(
				'sCategory'		=> $sCategory,
				'aGroupsInfo' => $aGroupsInfo,
			)
		);	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}	
}

?>
