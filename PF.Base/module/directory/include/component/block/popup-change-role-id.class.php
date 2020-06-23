<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Popup_Change_Role_Id extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iUserId = (int)$this->getParam('user_id');
		$iBusinessId = (int)$this->getParam('business_id');
		
        $aMemberRoles = Phpfox::getService('directory')->getMemberRolesByBusinessId($iBusinessId);

        $aCurrentRoleOfUser =  Phpfox::getService('directory')->getUserMemberRole($iUserId,$iBusinessId);

		$this->template()->assign(array(
				'iUserId'				=> $iUserId,
				'iBusinessId'			=> $iBusinessId,
				'aMemberRoles'			=> $aMemberRoles,
				'aCurrentRoleOfUser'	=> $aCurrentRoleOfUser,
			)
		);	
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
