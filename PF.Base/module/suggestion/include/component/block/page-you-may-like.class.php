<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Suggestion
 * @version 		$Id: sample.class.php 1 2011-11-25 15:29:17Z YOUNETCO $
 */
class Suggestion_Component_Block_Page_You_May_Like extends Phpfox_Component
{
	/**
	 * Class process method which is used to execute this component.
	 */
	public function process()
	{
		

      $view = $this->request()->get('view');
     
      if($view != 'friendspages'){
     	 	return false;
      }
	   
     $aPagesYouMayLike = Phpfox::getService('suggestion')->getPageYouMayLike();
	  
	 
      $aPagesYouMayLike = array_slice($aPagesYouMayLike,0,Phpfox::getParam('suggestion.number_item_on_other_block'));
     	
	 	      

      $total = Phpfox::getService('suggestion')->getCountPagesYouMayLike();
		 
	
       $this->template()->assign(array(
         'aData' => $aPagesYouMayLike,
         'total' => $total                
       ));
	   
	   
		return 'block';  
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('suggestion.component_block_friends_clean')) ? eval($sPlugin) : false);
	}
}

?>