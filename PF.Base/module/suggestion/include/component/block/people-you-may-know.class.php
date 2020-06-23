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
class Suggestion_Component_Block_People_You_May_Know extends Phpfox_Component
{
	/**
	 * Class process method which is used to execute this component.
	 */
	public function process()
	{
		

      $view = $this->request()->get('view');
     
      if($view != 'friendsfriend'){
      			 
     	 	return false;
      }
	  
	        
      $aPeopleYouMayKnows = Phpfox::getService('suggestion')->getPeopleYouMayKnow(Phpfox::getUserId(),0);

      $aPeopleYouMayKnows = array_slice($aPeopleYouMayKnows,0,Phpfox::getParam('suggestion.number_item_on_other_block'));
      $total = Phpfox::getService('suggestion')->getCountPeopleYouMayKnow();
       $this->template()->assign(array(
         'aData' => $aPeopleYouMayKnows,
         'total' =>  $total              
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