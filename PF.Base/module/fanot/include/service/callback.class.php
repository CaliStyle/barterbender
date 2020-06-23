<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Fanot Callbacks
 * 
 * @copyright		[YOUNETCO_COPYRIGHT]
 * @author  		AnNT
 * @package  		Advanced Notification
 * @version 		3.02
 */
class Fanot_Service_Callback extends Phpfox_Service 
{
	/**
	 * Class constructor
	 *
	 */	
	public function __construct()
	{	
		
	}
    
    public function getNotificationSettings()
    {
        (($sPlugin = Phpfox_Plugin::get('fanot.component_service_callback_getnotificationsettings__start')) ? eval($sPlugin) : false);
        return array(
            'fanot.sound_alert_for_notification' => array(
                'phrase' => _p('fanot.sound_alert_for_notification'), 
                'default' => 1
            )
        );
    }
	
	/**
	 * If a call is made to an unknown method attempt to connect
	 * it to a specific plug-in with the same name thus allowing 
	 * plug-in developers the ability to extend classes.
	 *
	 * @param string $sMethod is the name of the method
	 * @param array $aArguments is the array of arguments of being passed
	 */
	public function __call($sMethod, $aArguments)
	{
		/**
		 * Check if such a plug-in exists and if it does call it.
		 */
		if ($sPlugin = Phpfox_Plugin::get('fanot.service_callback__call'))
		{
			return eval($sPlugin);
		}
			
		/**
		 * No method or plug-in found we must throw a error.
		 */
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}

?>