<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Donationpages
 * @version 		$Id: sample.class.php 1 2012-02-15 10:33:17Z YOUNETCO $
 */
class Suggestion_Component_Block_Config extends Phpfox_Component
{
	/**
	 * Class process method which is used to execute this component.
	 */
	public function process()
	{
		  	$suggestion=array('suggestion.enable_system_suggestion' => 
                    array(
                        'phrase' => _p('suggestion.enable_system_suggestion'),
                        'default' => 1
                        ),
                    'suggestion.enable_system_recommendation' => array(
                        'phrase' => _p('suggestion.enable_system_recommendation'),
                        'default' => 1
                    ),
                    'suggestion.enable_content_suggestion_popup' => array(
                        'phrase' => _p('suggestion.enable_content_suggestion_popup'),
                        'default' => 1
                    )
                );
				$aNotifications1=phpfox::getService("user.privacy")->getUserSettings();
				
				
			
		$aRows=phpfox::getLib("database")->select('user_notification')
			->from(phpfox::getT('suggestion_setting'))
			->where('user_id='.phpfox::getUserId())
			->execute('getSlaveRows');
		foreach($aRows as $Rows)
		{
			if(isset($suggestion[$Rows['user_notification']]))
			{
				$suggestion[$Rows['user_notification']]['default']=0;
			}
		}

		
				$this->template()->assign(array(
					'aPrivacySuggestionNotifications' => $suggestion,
					
				));
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('donationpages.component_block_config_clean')) ? eval($sPlugin) : false);
	}
}

?>