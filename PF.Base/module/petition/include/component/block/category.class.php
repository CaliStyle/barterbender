<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Block_Category extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$bIsProfile = false;
		if ($this->getParam('bIsProfile') === true && ($aUser = $this->getParam('aUser')))
		{
		   $bIsProfile = true;
		}
				
		$aCategories = Phpfox::getService('petition.category')->getCategories('c.user_id = 0');
		if (!is_array($aCategories))
		{
			return false;
		}
		
		if (!$aCategories)
		{
			return false;
		}

		foreach ($aCategories as $iKey => $aCategory)
		{
			$aCategories[$iKey]['url'] = ($bIsProfile ? $this->url()->permalink(array($aUser['user_name'] . '.petition.category', 'view' => $this->request()->get('view')), $aCategory['category_id'], $aCategory['name']) : $this->url()->permalink(array('petition.category', 'view' => $this->request()->get('view')), $aCategory['category_id'], $aCategory['name']));
		}
		
		$this->template()->assign(array(
				'sHeader' => Phpfox::getPhrase('petition.categories'),
				'aCategories' => $aCategories,
				'iCategoryPetitionView' => $this->request()->getInt('req3')
			)
		);	

		(($sPlugin = Phpfox_Plugin::get('petition.component_block_categories_process')) ? eval($sPlugin) : false);
		
		return 'block';
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		$this->template()->clean(array(
				'aCategories'
			)
		);
	
		(($sPlugin = Phpfox_Plugin::get('petition.component_block_categories_clean')) ? eval($sPlugin) : false);
	}	
}

?>
