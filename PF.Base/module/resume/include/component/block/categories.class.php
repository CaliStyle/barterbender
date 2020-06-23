<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 * 
 */
class Resume_Component_Block_Categories extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		// Check if we are on the profile page or not then get user information
		$bIsProfile = false;
		if ($this->getParam('bIsProfile') === true && ($aUser = $this->getParam('aUser')))
		{
			$bIsProfile = true;
		}
		
		// Get curent category id
		$sCurrentCategoryId = '0';
		if($this->request()->get(($bIsProfile === true ? 'req3' : 'req2')) == 'category')
		{
			$sCurrentCategoryId = $this->request()->get(($bIsProfile === true ? 'req4' : 'req3'));
		}
		
		// Get view mode
		$sViewMode = $this->request()->get('view');
		// Get category items
		$sCatListLayout = phpFox::getService('resume.category')->toHTML($sCurrentCategoryId, $sViewMode);
		$aCats = PhpFox::getService('resume.category')->loadData();
		$this->template()->assign(array(
			'sHeader'  		 => _p('resume.categories'),
			'sCatListLayout' => $sCatListLayout
		));
		
		return 'block';
	}
}