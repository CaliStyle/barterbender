<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */

class Fevent_Component_Block_Glogin extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
        $event_id = $this->request()->get('id');
        $sCorePath = Phpfox::getParam('core.path') ;
        $sCorePath = str_replace("index.php".PHPFOX_DS,"",$sCorePath);
        $sCorePath .= 'PF.Base'.PHPFOX_DS;
        $this->template()->assign(array(
            'core_path' => $sCorePath,
            'event_id' => $event_id
        ));
	}
}