<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Suggestion_Component_Controller_Reminder extends Phpfox_Component
{
	/**
	 * Class process method which is used to execute this component.
         * 
	 */
         
	public function process()
	{   
        $support_modules = array('coupon','jobposting','contest');
		
		
        $aRows = array();
        foreach ($support_modules as $support_module) {

            $aDatas = phpfox::getService("suggestion.reminder")
            ->getReminder(Phpfox::getUserId(),$support_module,0,Phpfox::getParam('suggestion.number_item_on_other_block'));
                    
            if($aDatas != false) {
                foreach ($aDatas as $key => &$aData) {
                    $aRows[$aData['module_id']][] = $aData;
                }
            }
        }

        
        $this->template() ->assign(array(
            'sFullUrl' => Phpfox::getParam('core.path'),
            'aRows' => $aRows             
        ));
        
        $this->template()->setBreadcrumb(_p('suggestion.suggestion'), $this->url()->makeUrl('suggestion'));

        
	}


}
?>