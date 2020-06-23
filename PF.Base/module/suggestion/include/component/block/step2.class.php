<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Suggestion
 * @version 		$Id: sample.class.php 1 2011-11-25 15:29:17Z YOUNETCO $
 */class Suggestion_Component_Block_Step2 extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
            
            $type = $this->getParam('type','');
          
            $aUser = Phpfox::getService('user')->getUser($_SESSION['iFriendId'], 'u.full_name, u.user_name, u.user_id');
            
            $iCurrentUserId = Phpfox::getUserId();
            
            $sLink = Phpfox::permalink($aUser['full_name'], null);
            $sYouAreNow = _p('suggestion.you_are_now_friend_with_select_friend');
            $sLinkUser = '<a target="_blank" href="'.$sLink.'">'.$aUser['full_name'].'</a>';
            $sYouAreNow = preg_replace('/{{friend_name}}/', $sLinkUser, $sYouAreNow);
            $sYouAreNowSuggestion = _p('suggestion.you_are_now_friend_with_select_friend_suggestion');
            $sYouAreNowSuggestion = preg_replace('/{{friend_name}}/', $sLinkUser, $sYouAreNowSuggestion);
            
            (isset($_SESSION['show']) ? $bShow = $_SESSION['show'] : $bShow = 0) ;
            
            $this->template()->assign(array(
                'aFriend' => $aUser,
                'iCurrentUserId' => $iCurrentUserId,
                'type' => $type,
                'sYouAreNowSuggestion' => $sYouAreNowSuggestion,
                'bShow' => $bShow,
                'sYouAreNow' => $sYouAreNow
            ));
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('suggestion.component_block_search_clean')) ? eval($sPlugin) : false);
	}
}

?>