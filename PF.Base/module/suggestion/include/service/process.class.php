<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Suggestion
 * @version 		$Id: process.class.php 1 2011-11-25 15:29:17Z YOUNETCO $
 */
class Suggestion_Service_Process extends Phpfox_Service {

    /**
     * Class constructor
     */
    public function __construct() {
        $this->_sTable = Phpfox::getT('suggestion');
    }

    /*
     * remove all session handle suggestion detail.
     * Do not display suggestion popup when admin approve any item
     */
    
    public function updateSetting($value1,$value2,$value3)
	{
		phpfox::getLib("database")->delete(phpfox::getT('suggestion_setting'),'user_id='.phpfox::getUserId());
		$aInsert['user_id']=phpfox::getUserId();
		
		if($value1==1)
		{
			$aInsert['user_notification']="suggestion.enable_content_suggestion_popup";	
			phpfox::getLib("database")->insert(phpfox::getT('suggestion_setting'),$aInsert);
		}
		if($value2==1)
		{
			$aInsert['user_notification']="suggestion.enable_system_recommendation";
			phpfox::getLib("database")->insert(phpfox::getT('suggestion_setting'),$aInsert);
		}
		if($value3==1)
		{
			$aInsert['user_notification']="suggestion.enable_system_suggestion";
			phpfox::getLib("database")->insert(phpfox::getT('suggestion_setting'),$aInsert);
		}
	}
	
    public function dontAskMeAgain($iDontAskMeAgain)
    {
        $this->database()->delete(Phpfox::getT('suggestion_setting'), 'user_id = ' . Phpfox::getUserId() . ' AND user_notification = "suggestion.enable_content_suggestion_popup"');
        
        if ($iDontAskMeAgain == 1)
        {
            $aInsert = array();
            $aInsert['user_id'] = Phpfox::getUserId();
            
            $aInsert['user_notification'] = "suggestion.enable_content_suggestion_popup";	
            $this->database()->insert(Phpfox::getT('suggestion_setting'), $aInsert);
            
            $aInsert['user_notification'] = "suggestion.enable_system_recommendation";
            $this->database()->insert(Phpfox::getT('suggestion_setting'), $aInsert);
            
            $aInsert['user_notification'] = "suggestion.enable_system_suggestion";
            $this->database()->insert(Phpfox::getT('suggestion_setting'), $aInsert);
        }
    }    
    
    public function ignoreSuggestion(){        
        Phpfox::getService('suggestion.cache')->set('ignoreSuggestion',1);
    }
    
    public function isIgnoreSuggestion(){
        $result = Phpfox::getService('suggestion.cache')->get('ignoreSuggestion');
        if ((int)$result == 1){            
            Phpfox::getService('suggestion.cache')->remove('ignoreSuggestion');
            return true;
        }
        return false;
    }
    
    public function delete($iUserId, $sModuleId, $iItemId) {
        $this->database()->delete($this->_sTable, 'user_id = ' . (int) $iUserId . ' AND item_id = ' . (int) $iItemId . ' AND module_id = "' . $sModuleId . '"');

        //flush all cache 
        Phpfox::getService('suggestion.cache')->removeAll(Phpfox::getUserId());
    }
	
	
	public function deleteSuggestion($iSuggestionId) {
        $this->database()->delete($this->_sTable, 'suggestion_id = ' . (int) $iSuggestionId );

        //flush all cache 
        Phpfox::getService('suggestion.cache')->removeAll(Phpfox::getUserId());
    }
    
    public function deleteNotification($sModuleId, $iItemId, $iSuggestionId) {
        $this->database()->delete(Phpfox::getT('suggestion_notification'), 'notification_id = ' . (int) $iItemId . ' AND module_id = "' . $sModuleId . '"' . ' AND suggestion_id =' . (int)$iSuggestionId);

        //flush all cache 
        Phpfox::getService('suggestion.cache')->removeAll(Phpfox::getUserId());
    }

    /*
     * delete temp data when suggest item in module Music
     */

    public function deletePrivateData($iUserId) {
        $this->database()->delete($this->_sTable, 'user_id = ' . Phpfox::getUserId() . ' AND friend_user_id = 0 AND item_id = 0');
    }

    /*
     * process user has approve suggesetion or ignore it.
     * @int iApprove: 1 or -1
     * 1: approve
     * -1: ignore
     * 
     */

    public function approve($iFriendId, $iItemid, $iApprove=1, $sModule, $iProcessId = '') {
        //flush all cache 
        
        $condition = ' WHERE  item_id = ' . (int) $iItemid . ' AND friend_user_id = "' . (int) $iFriendId . '" AND module_id = "' . $sModule . '"' . ' AND processed != 2';
		if(!empty($iProcessId))
		{
			$condition .= " AND suggestion_id = ".(int)$iProcessId;
		}
        Phpfox::getService('suggestion.cache')->removeAll(Phpfox::getUserId());
        
        return $this->database()->query('update ' . Phpfox::getT('suggestion') . ' set processed = ' . (int) $iApprove . $condition );
    }

    public function changeStatusSuggestFriend($iUserId,$iFriendId, $iItemid, $iStatus=1, $sModule) {
        //flush all cache 
        Phpfox::getService('suggestion.cache')->removeAll(Phpfox::getUserId());
        
        return $this->database()->query('update ' . Phpfox::getT('suggestion') . ' set processed = ' . (int) $iStatus . ' WHERE  item_id = ' . (int) $iItemid . ' AND friend_user_id = "' . (int) $iFriendId . '" AND user_id = "' . (int) $iUserId . '" AND module_id = "suggestion_friend"' . ' AND processed != 2');
    }

    public function denyFriend($iFriendId, $iUserId){
        $this->database()->delete(Phpfox::getT('friend_request'),'friend_user_id = ' . (int)$iFriendId . ' AND user_id = ' . (int)$iUserId);
    }
    
    /*
     * update suggestion detail by field
     */

    public function updateBy($iItemId, $sValue, $sModule, $sField) {
        //flush all cache of current user
        Phpfox::getService('suggestion.cache')->removeAll(Phpfox::getUserId());
        
        return $this->database()->update(Phpfox::getT('suggestion'), array($sField => $sValue), 'module_id="' . $sModule . '" AND item_id="' . $iItemId . '"');
        
        
    }

    /*
     * update quiz link
     */

    public function updateQuizLink($iId) {
        //flush all cache of current user
        Phpfox::getService('suggestion.cache')->removeAll(Phpfox::getUserId());
        
        $aQuiz = $this->database()->select('q.quiz_id, q.title, u.user_id, u.user_name')
                ->from(Phpfox::getT('quiz'), 'q')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = q.user_id')
                ->where('q.quiz_id = ' . (int) $iId)
                ->execute('getSlaveRow');

        if (!isset($aQuiz['quiz_id'])) {
            return false;
        }
        if (Phpfox::getParam('core.is_personal_site')) {
            $sLink = Phpfox::getLib('url')->makeUrl('quiz', $aQuiz['title']);
            Phpfox::getService('suggestion.process')->updateBy($iId, $sLink, $sModule = 'suggestion_quiz', 'url');
            return;
        }

        $sLink = Phpfox::getLib('url')->makeUrl('quiz', array($aQuiz['quiz_id'], $aQuiz['title']));

        Phpfox::getService('suggestion.process')->updateBy($iId, $sLink, $sModule = 'suggestion_quiz', 'url');
                
    }

    /*
     * add notification to notification table
     */

    public function addNotification($sType, $iItemId, $iOwnerUserId, $iSuggestionId=null, $iSenderUserId=null) {
        

        if (defined('SKIP_NOTIFICATION')) {
            return true;
        }
        
        $aInsert = array(
            'module_id' => $sType,
            'item_id' => $iItemId,           
            'suggestion_id' => (int)$iSuggestionId           
        );
        
        $iLastNotificationId = $this->database()->insert(Phpfox::getT('suggestion_notification'), $aInsert);
        
        $aInsert = array(
            'type_id' => 'suggestion',
            'item_id' => $iLastNotificationId,
            'user_id' => $iOwnerUserId,
            'owner_user_id' => ($iSenderUserId === null ? Phpfox::getUserId() : $iSenderUserId),
            'time_stamp' => PHPFOX_TIME            
        );

		$IsRows=phpfox::getLib("database")->select('*')
		->from(phpfox::getT('user_notification'))
		->where('user_id='.$iOwnerUserId.' and user_notification="'."suggestion.enable_system_suggestion".'"')
		->execute('getSlaveRows');
		
		if(count($IsRows)==0)
        	$this->database()->insert(Phpfox::getT('notification'), $aInsert);

        return true;
    }

    /**
     * 
     * add new suggestion 
     * 
     * @param int $iUserId: id of user who suggestion some items to friends
     * @param int $iFriendId: id of user who has received suggestion from iUserId
     * @param int $sModule: name of module they suggestion together
     * @param int $sMessage: message iUserID attach when send suggestion
     * @param int $sLinkCallback: the link to link to item 
     * @param int $sTitle: the title of suggestion item
     * @param int $sPrefix: to known section they suggested, module or in pages
     * 
     */

    public function add($iUserId, $iFriendId, $sFriendList, $sModule, $sMessage, $sLinkCallback='', $sTitle='', $sPrefix='', $bNoLimit = false) {

        $aFriendList = explode(',', $sFriendList);        
        $iLimitMessage = (int)Phpfox::getUserParam('suggestion.max_message_chars');
        
        foreach ($aFriendList as $_iUserId) {

            if ($sModule == 'suggestion_recommendation')
                $sModule = 'suggestion_friend';

                $iLastId = $this->database()->insert(Phpfox::getT('suggestion'), array(
                            'user_id' => $iUserId,
                            'friend_user_id' => $iFriendId,
                            'module_id' => $sModule,
                            'message' => $bNoLimit ? $sMessage : Phpfox::getLib('parse.input')->clean($sMessage, $iLimitMessage),
                            'friend_user_id' => $iFriendId,
                            'module_id' => $sModule,
                            'url' => $sLinkCallback,
                            'title' => $sTitle,
                            'item_id' => $_iUserId,
                            'time_stamp' => PHPFOX_TIME,
                            'prefix' => $sPrefix
                        )
                );
        }

        //flush all cache of current user
        Phpfox::getService('suggestion.cache')->removeAll(Phpfox::getUserId());
        return (int)$iLastId;
    }

	

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing 
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments) {
        if ($sPlugin = Phpfox_Plugin::get('suggestion.service_suggestion__call')) {
            return eval($sPlugin);
        }

        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

}

?>