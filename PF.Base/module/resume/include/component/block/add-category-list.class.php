<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Blog
 * @version 		$Id: add-category-list.class.php 328 2009-03-29 12:26:31Z Raymond_Benc $
 */
class Resume_Component_Block_Add_Category_List extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	 
	public function process()
	{		
		$resume_id = $this->getParam('resume_id');
		
		$aItems = Phpfox::getService('resume.category')->getCategories();
		
		
		$this->template()->assign(array(
			'aItems' => $aItems,
			
		));	
		
			
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