<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class TourGuides_Component_Controller_Admincp_Index extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		 $iId  = $this->request()->get('id');
         $sSessionId = $this->request()->get('ss');
         $sCom = $this->request()->get('com');
         if($iId && $sSessionId && $sCom !=1)
         {
             $aSession = phpfox::getLib('session')->get('yntour_current_selected'); 
             if(isset($aSession['url']))   
             {
                Phpfox::getService('user.auth')->logout();
                $this->url()->send($aSession['url']);
             }
             
         }
         phpfox::getLib('session')->remove('yntour_current_selected');   
		 $this->url()->send('admincp.tourguides.manage');
	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('tourguides.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}

?>