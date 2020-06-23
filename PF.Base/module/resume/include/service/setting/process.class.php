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
class Resume_Service_Setting_Process extends Phpfox_Service
{
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('resume_settings');
	}
	
	public function removeSetting(){
		$this->database()->delete($this->_sTable,"1=1");
	}
	
	public function removePermission(){
		$this->database()->delete(Phpfox::getT('resume_setting_permission'),"1");
	}
	
	public function updateSetting($aVals)
	{
		$this->removeSetting();

		foreach($aVals['whoview'] as $key => $whoview){

			$this->database()->insert($this->_sTable,array(
				'begin_group' => $key,
				'end_group' => !empty($whoview) ? $whoview : 0,
				'type_id' => 1
			));	
		}
		
		foreach($aVals['viewme'] as $key => $viewme){
			$this->database()->insert($this->_sTable,array(
				'begin_group' => $key,
				'end_group' => !empty($viewme)?$viewme:0,
				'type_id' => 2
			));	
		}
				
		return true;
	}
	
	public function addPermission($name, $value){
		$this->database()->insert(Phpfox::getT('resume_setting_permission'),array(
			'name' => $name,
			'value' => $value
		));
	}
}

?>	