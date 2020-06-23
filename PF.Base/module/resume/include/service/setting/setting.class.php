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
class Resume_Service_Setting_Setting extends Phpfox_Service
{
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('resume_settings');
	}
	/**
	 * Get Custom Group Information
	 * @return array list of group information
	 */
	 
	public function getCustomGroupInfo()
	{
		$aCustomGroups = $this->database()->select("*")
						->from(Phpfox::getT("user_group"))
						->execute("getRows");
		return $aCustomGroups;
	}
	
	public function getGroupTransfer($user_group_id, $type){
		$user_group_id = (int)$this->database()->select("end_group")
			->from($this->_sTable)
			->where("begin_group = ".$user_group_id. " and  type_id = ".$type)
			->execute('getField');
		return $user_group_id;
	}
	
	public function getUserGroupId($user_id,$type = 0){
		$user_group_id = (int)$this->database()->select("user_group_id")
						->from(Phpfox::getT("user"))
						->where('user_id = '.$user_id)
						->execute("getField");
		if($type!=0){
			if($ugi = $this->getGroupTransfer($user_group_id, $type))
			{
				$user_group_id = $ugi;
			}
		}
		return $user_group_id;
	}
	
	/**
	 * Get Setting through name input
	 * @param <string> $sName is the name of the setting
	 * @return <array> list of the gotten setting
	 */
	public function getSettings($type_id)
	{
		$aSetting = $this->database()->select("*")
						->from($this->_sTable)
						->where("type_id = ".$type_id)
						->execute('getRows');
		return $aSetting;
	}
	
	public function getPermission()
	{
		$aPermission = $this->database()->select("*")
						->from(Phpfox::getT('resume_setting_permission'))
						->execute('getRows');
		return $aPermission;
	}
    
    public function getAllPermissions()
    {
        $aPermissions = $this->getPermission();
        $aCheck = array();
        foreach ($aPermissions as $aPermission)
        {
            $aCheck[$aPermission['name']] = $aPermission['value'];
        }
        return array(
            'public_resume' => isset($aCheck['public_resume']) ? $aCheck['public_resume'] : 3,
            'get_basic_information' => isset($aCheck['get_basic_information']) ? $aCheck['get_basic_information'] : 0,
            'display_date_of_birth' => isset($aCheck['display_date_of_birth']) ? $aCheck['display_date_of_birth'] : 0,
            'display_gender' => isset($aCheck['display_gender']) ? $aCheck['display_gender'] : 0,
            'display_relation_status' => isset($aCheck['display_relation_status']) ? $aCheck['display_relation_status'] : 0,
            'display_resume_in_profile_info' => isset($aCheck['display_resume_in_profile_info']) ? $aCheck['display_resume_in_profile_info'] : 1,
            'position' => isset($aCheck['position']) ? $aCheck['position'] : 1
        );
    }
	
	public function getPermissionByName($name)
	{
		$aPermission = $this->getPermission();
		foreach($aPermission as $Permission)
		{
			if($Permission['name'] == $name)
			{
				return $Permission['value'];
			}
		}
		return 3;
	}
}

?>	