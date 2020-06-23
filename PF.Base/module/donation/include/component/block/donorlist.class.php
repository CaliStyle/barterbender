<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Friend
 * @version 		$Id: search.class.php 3639 2011-12-02 05:59:22Z Raymond_Benc $
 */
class Donation_Component_Block_Donorlist extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{            
		$iPage = $this->getParam('page', 0);
		$iPageId = (int)$this->getParam('iPageId');
		$iUserId = (int)$this->getParam('iUserId');
		$iLimit = Phpfox::getParam('donation.number_of_fetched_donors');
		if($iLimit <= 0)
		{
			$iLimit = 0;
		}
		if($this->getParam('iCurrentOffset'))
		{
			$iLimit = (int) $this->getParam('iCurrentOffset');
			$iLimit = $iLimit - 1;
		}
		$iPageSize = 20;
		$bIsOnline = false;		
		$oDb = Phpfox::getLib('database');
		$aParams = array();
		$aConditions = array();
		
		$iListId = 0;
		
		$iUserPageId = Phpfox::getService('donation')->getUserIdOfPage($iPageId);
		
		$aUsersId = Phpfox::getService('donation')->getDonatedUser($iPageId);
		if ($iUserId == $iUserPageId){
			$bModerator = true;
		}else{
			$bModerator = false;
		}
		
		if(Phpfox::isAdmin() && $iPageId == -1)
		{
			$bModerator = true;
		}
		if(!empty($aUsersId))
		{
			foreach($aUsersId as $user){
				$ret[] = $user['user_id'];
			}
			$aUsersId = implode(',', $ret);
			$aConditions[] = 'u.user_id IN (' . $aUsersId . ')';
		}		
		
		if (($sFind = $this->getParam('find')))
		{
			$aConditions[] = 'AND (u.full_name LIKE \'%' . $oDb->escape($sFind) . '%\' OR (u.email LIKE \'%' . $oDb->escape($sFind) . '@%\' OR u.email = \'' . $oDb->escape($sFind) . '\'))';	
		}		
		
		$aLetters = array(
			'All', '#', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
		);			
		
		if (($sLetter = $this->getParam('letter')) && in_array($sLetter, $aLetters) && strtolower($sLetter) != 'all')
		{
			if ($sLetter == '#')
			{
				$sSubCondition = '';
				for ($i = 0; $i <= 9; $i++)
				{
					$sSubCondition .= "OR u.full_name LIKE '" . Phpfox::getLib('database')->escape($i) . "%' ";
				}
				$sSubCondition = ltrim($sSubCondition, 'OR ');
				$aConditions[] = 'AND (' . $sSubCondition . ')';
			}
			else 
			{
				$aConditions[] = "AND u.full_name LIKE '" . Phpfox::getLib('database')->escape($sLetter) . "%'";	
			}
			
			$aParams['letter'] = $sLetter;
		}		
		
		if ($sView = $this->getParam('view'))
		{
			switch ($sView)
			{
				case 'top':
					$aConditions[] = 'AND is_top_friend = 1';
					break;
				case 'online':
					$bIsOnline = true;
					break;
				case 'all':
					
					break;
				default:					
					if ((int) $sView > 0 && ($aList = Phpfox::getService('friend.list')->getList($sView, Phpfox::getUserId())) && isset($aList['list_id']))
					{
						$iListId = (int) $aList['list_id'];
					}
					break;
			}
		}
		
		if ($this->getParam('type') == 'mail')
		{
			$aConditions[] = 'AND u.user_id != ' . Phpfox::getUserId();
			list($iCnt, $aFriends) = Phpfox::getService('user.browse')->conditions($aConditions)
				->sort('u.full_name ASC')
				->page($iPage)
				//->limit($iPageSize)			
				->get();                        
			if (Phpfox::getParam('mail.disallow_select_of_recipients'))
			{
				$oMail = Phpfox::getService('mail');
				foreach ($aFriends as $iKey => $aFriend)
				{
					if (!$oMail->canMessageUser($aFriend['user_id']))
					{
						$aFriends[$iKey]['canMessageUser'] = false;
					}
				}
			}
		}
		else 
		{		
			if (empty ($aConditions))
			{
				$aConditions[] = 'friend.user_id IN (0)';
			}
			$aResult = Phpfox::getService('donation.cache')->get('skey'.$iPageId.'.page.'. '0' .'.'. $iLimit);
			$iTotalDonors = Phpfox::getService('donation.cache')->get('skey'.$iPageId.'.page.totaldonors');
			if ($aResult===FALSE){     
				 $aFriends = Phpfox::getService('donation')->getDonorList(0, $iLimit, $iPageId);
				 $iTotalDonors = Phpfox::getService('donation')->getTotalDonor($iPageId);
									 //cache total here
				Phpfox::getService('donation.cache')->set('skey'.$iPageId.'.page.'. '0' .'.'. $iLimit, $aFriends, 3600);
				Phpfox::getService('donation.cache')->set('skey'.$iPageId.'.page.totaldonors', $iTotalDonors, 3600);
			}
			else
			{                    
				list($iCnt, $aFriends) = array(count($aResult), $aResult);
			}
		}
                
		if (!empty($aFriends))
		{
			foreach($aFriends as &$f){
			
			
				if($f['is_guest'])
				{
				}
				else
				{
					if (strlen($f['full_name']) > 20)
					{
							$f['full_name'] = substr($f['full_name'], 0, 20) . "...";
					}	
					$aUser = Phpfox::getService('donation')->getUser($f['user_id']);
					$aUser['suffix'] = '_50_square';
					$aUser['max_width'] = '50';
					$aUser['max_height'] = '50';
					$aUser['user'] = $aUser;                        
					$f['img'] = Phpfox::getLib('phpfox.image.helper')->display($aUser);                        
				}
			}
		}
                
		$aParams['input'] = $this->getParam('input');
		$aParams['friend_item_id'] = $this->getParam('friend_item_id');
		$aParams['friend_module_id'] = $this->getParam('friend_module_id');
		$aParams['type'] = $this->getParam('type');
	//	Phpfox::getLib('pager')->set(array('ajax' => 'donation.searchAjax', 'page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt, 'aParams' => $aParams));
		//currently owner isnot allowed to delete donors
		$sFriendModuleId = $this->getParam('friend_module_id', '');
		$this->template()->assign(array(
				'bLoadMore' => $iTotalDonors > $iLimit,
				'iTotalDonors' => $iTotalDonors,
				'iCurrentLimit' => ($iTotalDonors < $iLimit) ? $iTotalDonors : $iLimit,
				'aFriends' => $aFriends,
				'bModerator' => $bModerator,
				'iPageId' => $iPageId,
				'aLetters' => $aLetters,
				'sView' => $sView,
				'sActualLetter' => $sLetter,
				'sPrivacyInputName' => $this->getParam('input'),
				'aLists' => Phpfox::getService('friend.list')->get(),
				'bSearch' => $this->getParam('search'),
				'bIsForShare' => $this->getParam('friend_share', false),
				'sFriendItemId' => (int) $this->getParam('friend_item_id', '0'),
				'sFriendModuleId' => $sFriendModuleId,
				'sFriendType' => $this->getParam('type'),
				'sNoProfileImagePath' => Phpfox::getParam('core.path') . 'module/donation/static/image/no_avatar.jpg'
			)
		);
				
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('friend.component_block_search_clean')) ? eval($sPlugin) : false);
	}
}

?>