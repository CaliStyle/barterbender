<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Suggestion
 * @version 		$Id: callback.class.php 1 2011-11-25 15:29:17Z YOUNETCO $
 */
class Suggestion_Service_Callback extends Phpfox_Service
{
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('suggestion');
	}
    
    public function getNotificationSettings()
    {
        return array('suggestion.enable_system_suggestion' =>
            array(
                'phrase' => _p('suggestion.notification_for_suggestion_system'),
                'default' => 1
            ),
        );
    }

    public function getNotification($aNotification)
    {

        $iNotificationId = $aNotification['item_id'];

        /* get suggest notification detail */
        $aNotificationDetail = $this->database()
                ->select('*')
                ->from(Phpfox::getT('suggestion_notification'))
                ->where('notification_id = ' . (int) $iNotificationId)
                ->execute('getRow');

        $sModule = $aNotificationDetail['module_id'];

        switch ($sModule) {
            case 'suggestion_friend':
                $sUserName = $aNotification['full_name'];
                ($aNotification['gender'] == 1 ? $gender = _p('suggestion.his') : $gender = _p('suggestion.her'));

                $sMsg = $sUserName . " " . _p('suggestion.has_sent_you_a_friend_suggestion');

                $aSuggest = Phpfox::getService('suggestion')->getSuggestionDetailByNotification($aNotification['user_id'], $aNotification['item_user_id'], 'suggestion_friend', $aNotification['item_id']);

                if (isset($aSuggest['message']) && $aSuggest['message'] != '')
                {
                    $sMsg .= "<br />" . _p('suggestion.message') . ": " . $aSuggest['message'];
                }

                 $aSuggestionDetail = Phpfox::getService('suggestion')->getSuggestionDetailBySuggestionId($aNotificationDetail['suggestion_id']);

                if(count($aSuggestionDetail)){

                    $aUserItem = Phpfox::getService('user')->getUser($aSuggestionDetail['item_id'], 'u.full_name, u.user_name');
                    if(isset($aUserItem['user_name'])){
                        $sLink = Phpfox::permalink($aUserItem['user_name'], null);
                    }
                    else{
                        $sLink = '';   
                    }
                }
                else{
                    $sLink = '';   
                }

                $sLink =  Phpfox::getLib('url')->makeUrl('suggestion',array('view' => 'friends'));

                return array(
                    'message' => $sMsg,
                    'link'    => $sLink
                    //'link' => '#suggest_' . $aNotification['user_id'] . '_' . $sUserName
                );
                break;
            case 'suggestion_marketplace':
            case 'suggestion_video':
            case 'suggestion_photo':
            case 'suggestion_forum':
            case 'suggestion_blog':
            case 'suggestion_video':
                $sMsg = _p('suggestion.suggestion_friend_has_suggested_you_to_view');
                break;

            case 'suggestion_poll':
                $sMsg = _p('suggestion.suggestion_friend_has_suggested_you_to_rate');
                break;

            case 'suggestion_quiz':
                $sMsg = _p('suggestion.suggestion_friend_has_suggested_you_to_take');
                break;

            case 'suggestion_event':
                $sMsg = _p('suggestion.suggestion_friend_has_suggested_you_to_join');
                break;

            case 'suggestion_music':
                $sMsg = _p('suggestion.suggestion_friend_has_suggested_you_to_listen');
                break;

            /*
             * fix for newer module not include core module 
             */
            default:
                $sMsg = _p('suggestion.suggestion_friend_has_suggested_you_to_view');
                break;
        }

        $aUser = Phpfox::getService('user')->getUser($aNotification['owner_user_id'], 'u.full_name');

        $aSuggestionDetail = Phpfox::getService('suggestion')->getSuggestionDetailByUserId($aNotification['owner_user_id'], Phpfox::getUserId(), $sModule, $aNotificationDetail['item_id']);

        if (count($aSuggestionDetail) > 0)
        {
            $sName = $aSuggestionDetail['title'];

            $sName = $sName;

            $sMsg = preg_replace('/{{friend_name}}/', $aUser['full_name'], $sMsg);

            $sMsg = preg_replace('/{{you}}/', _p('suggestion.you'), $sMsg);

            $sMsg = preg_replace('/{{data}}/', $sName, $sMsg);

            if ($aSuggestionDetail['module_id'] == 'suggestion_document')
            {
                $aSuggestionDetail['url'] = Phpfox::getLib("url")->makeUrl('document') . $aSuggestionDetail['item_id'] . "/";
                if (Phpfox::isModule('document'))
                {
                    $title_url = PHpfox::getService('document.process')->getDocumentTitleUrl($aSuggestionDetail['item_id']);
                    if ($title_url)
                        $aSuggestionDetail['url'].=$title_url . "/";
                }
            }
            if ($aSuggestionDetail['module_id'] == 'suggestion_page')
            {
                $aSuggestionDetail['url'] = Phpfox::getLib("url")->makeUrl('document') . $aSuggestionDetail['item_id'] . "/";
                if (Phpfox::isModule('document'))
                {
                    $title_url = PHpfox::getService('document.process')->getDocumentTitleUrl($aSuggestionDetail['item_id']);
                    if ($title_url)
                        $aSuggestionDetail['url'].=$title_url . "/";
                }
            }
            if ($aSuggestionDetail['module_id'] == 'suggestion_pages')
            {
                $aSuggestionDetail['url'] = Phpfox::getLib("url")->makeUrl('pages') . $aSuggestionDetail['item_id'] . "/";
            }
			/*
			if ($aSuggestionDetail['module_id'] == 'suggestion_forum')
			
				   { 
					   	 
							//$iForum_Post_Id =$list[1];
							$oforum_post = Phpfox::getService('suggestion')->getForumPostByForumPostID($aSuggestionDetail['item_id']);							
							$iThreadId = isset($oforum_post['thread_id'])?$oforum_post['thread_id']:0;	  
							$aSuggestionDetail['url'] =	 Phpfox::getLib("url")->makeUrl('forum.thread').$iThreadId;	
						 
						//var_dump($sUrl);
				   }
			 */
			 
				   
			

            $aParams = array(
                'iFriendId' => $aSuggestionDetail['friend_user_id'],
                'iItemid' => $aSuggestionDetail['item_id'],
                'sModule' => $aSuggestionDetail['module_id'],
                'sRedirect' => str_replace('/', '__', base64_encode($aSuggestionDetail['url']))
            );

            $ret[] = 'suggestion';
            $ret[] = 'view_redirect';
            foreach ($aParams as $k => $v)
            {
                $ret[] = $k . '_' . $v;
            }

            //append message of user
            $ret = implode("/", $ret);


            if (Phpfox::getParam('core.url_rewrite') < 2)
                $sLinkName = Phpfox::getLib('url')->getDomain() . $ret;
            else{
            	//$sLinkName = Phpfox::getLib('url')->getDomain() . 'index.php?do=/' . $ret;
				$sLinkName = Phpfox::getLib('url')->getDomain() . '?do=/' . $ret;
            }
			if ($aSuggestionDetail['module_id'] == 'suggestion_forum')
			{
				$sLinkName = $aSuggestionDetail['url'];	
			}   

            if (isset($aSuggestionDetail['message']) && $aSuggestionDetail['message'] != '')
            {
                $sMsg .= '<br />' . ucfirst(_p('suggestion.message')) . ': ' . $aSuggestionDetail['message'];
            }

            return array(
                'message' => $sMsg,
                'link' => $sLinkName
            );
        }
        else
        { //if not has suggestion detail delete notification
            $sType = $aNotification['type_id'];
            $iItemId = $aNotification['item_id'];
            $iUserId = (int) Phpfox::getUserId();
            Phpfox::getService('notification.process')->delete($sType, $iItemId, $iUserId);
            $this->database()->delete(Phpfox::getT('suggestion_notification'), 'module_id = "' . $sModule . '" AND notification_id = ' . (int) $iItemId);
            return array(
                'message' => 'Deleting...',
                'link' => '#'
            );
        }
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
		if ($sPlugin = Phpfox_Plugin::get('suggestion.service_callback__call'))
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
