<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<?php
class Contactimporter_Component_Block_Statistics extends Phpfox_Component
{    
	public function process()
	{
		/**
		 * SKIP statistic if user have not logged in yet.
		 */
		$iUserId = Phpfox::getUserId();
		
		if(!$iUserId)
		{
			return FALSE;	
		}	
		
		$max_invitation = $this->getParam('max_invitation');
		if (!$max_invitation)
		{
			$max_invitation = Phpfox::getService('contactimporter')->getMaxInvitation();
		}		
		$aStatistics = Phpfox::getService('contactimporter')->getStatistics();                
		
		//get total send
		$iTotal = phpfox::getLib('phpfox.database') -> select('SUM(total) as total') -> from(phpfox::getT('contactimporter_contact')) -> where("user_id = " . phpfox::getUserId(). " AND provider != 'manual'") -> execute('getRow');
		
		//get total friends
		$total_friend_invitation = phpfox::getLib('phpfox.database') -> select('COUNT(*)') -> from(phpfox::getT('contactimporter_social_joined')) -> where('inviter = ' . phpfox::getUserId()) -> execute('getSlaveField');
		
		$this->template()->assign(array(
			'sHeader' => _p('statistics'),
			'sDeleteBlock' => 'dashboard',
			'contactimporter.js' => 'module_contactimporter',
			'statistics' => $aStatistics ,
			'total_invitation' => (isset($iTotal['total']) && $iTotal['total']) ? $iTotal['total'] : 0,
			'total_friend_invitation' => (isset($total_friend_invitation) && $total_friend_invitation) ? $total_friend_invitation : 0,
			'max_invitation' => $max_invitation
		));
		return 'block';
	}
}
?>