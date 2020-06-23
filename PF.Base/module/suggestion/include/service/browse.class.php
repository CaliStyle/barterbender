    <?php
    /**
     * [PHPFOX_HEADER]
     */

    defined('PHPFOX') or exit('NO DICE!');

    /**
     * @copyright		[YOUNETCO]
     * @author  		NghiDV
     * @package  		Module_Suggestion
     * @version 		$Id: browse.class.php 1 2011-11-25 15:29:17Z YOUNETCO $
     */
    class Suggestion_Service_Browse extends Phpfox_Service
    {
            /**
             * Class constructor
             */	
            public function __construct()
            {	

            }

            public function query()
            {    
//                //get current key word
                
                if (isset($_SESSION['suggestion']['current_view'])) $sCurView = $_SESSION['suggestion']['current_view']; else $sCurView = '';
                $sKey = Phpfox::getService('suggestion')->getSearchKey($sCurView);                   
                                
                if ($sKey != ''){
                    
                    $this->database()->select('s.module_id sub_module_id, s.item_id, user0.full_name as friend_full_name, user1.full_name as friend_friend_full_name, user1.user_id as friend_friend_user_id, s.processed as processed, s.url as url, s.title as title, ');
                    
                }else{
                    
                    $this->database()->select('s.module_id sub_module_id, s.item_id, user0.full_name as friend_full_name, user1.full_name as friend_friend_full_name, user1.user_id as friend_friend_user_id, s.processed as processed, s.url as url, s.title as title, ');
                    
                }
                
            }	

            public function processRows(&$aRows)
            {           
                
                $aKeys[] = Phpfox::getUserId();
                $aKeys[] = $_REQUEST['do'];
                
                $sKey = preg_replace('/[\/\\()]/','',implode('_', $aKeys));
                
                $aResult = Phpfox::getService('suggestion.cache')->get($sKey);
                $aResult = false;
                //hit cache
                if ($aResult !== FALSE) 
                    $aRows = $aResult;
                else{//cache is missing
                    $_sMessage = ucfirst(_p('suggestion.message')) . ": ";

                        foreach ($aRows as $iKey => &$aRow)
                        {                               
                            /*
                             * 
                             * fix for module not in friend. merge friend_firend_user_id = item_id
                             * merge ID of current item instead of friend_user_id of friend.
                             * 
                             */
                            if (!isset($aRow['friend_friend_user_id'])){
                                $aRow['friend_friend_user_id'] = $aRow['item_id'];
                            }

                            $sModule = Phpfox::getService('suggestion')->convertModule($aRow['module_id']);
                            
                            if ($sModule == 'friend'){
                                $sImg = Phpfox::getLib('image.helper')->display(array('theme' => 'misc/group.png', 'return_url' => true));
                            }else
                                $sImg = Phpfox::getLib('image.helper')->display(array('theme' => 'module/'.$sModule.'.png', 'return_url' => true));

                            $_sImg = explode("/theme/", $sImg);
                            $_sImg = PHPFOX_DIR . '/theme/' . $_sImg[1];
                            
                            if (is_file($_sImg)){
                                $aRow['icon'] = $sImg;
                            }else{
                                $aRow['icon'] = Phpfox::getParam('core.path')."module/suggestion/static/image/suggestion.png";
                            }
                            
                            $aRow['url'] = urlencode($aRow['url']);

                            switch($sModule){
                                case 'friend':
                                    if ($aRow['user_id'] != $aRow['friend_user_id']){ //suggestion
                                        if ($aRow['user_id'] == Phpfox::getUserId()){ //you suggestion friend to friend
//                                            $sMessage = 'You suggested {{friend_friend_name}} to {{friend_name}}';
                                            $sMessage = _p('suggestion.you_suggested_friend_to_friend');
                                            $sFriend_friend_name = Phpfox::getService('suggestion')->getUserLink($aRow['friend_friend_user_id']);
                                            $sFriend_name = Phpfox::getService('suggestion')->getUserLink($aRow['friend_user_id']);
                                            $sMessage = str_replace('{{friend_name}}', $sFriend_friend_name, $sMessage);
                                            $sMessage = str_replace('{{friend_friend_name}}', $sFriend_name, $sMessage);
                                        }else{ //you has been suggested to friend
//                                            $sMessage = 'You have been suggested to {{friend_friend_name}}';
                                            $sMessage = _p('suggestion.you_have_been_suggested_to_friend');
                                            $sFriend_friend_name = Phpfox::getService('suggestion')->getUserLink($aRow['friend_friend_user_id']);
                                            $sMessage = str_replace('{{friend_name}}', $sFriend_friend_name, $sMessage);
                                        }
                                    }else{ //recommendation
                                        $sMessage = _p('suggestion.you_have_been_suggested_to_friend');
                                        $sFriend_friend_name = Phpfox::getService('suggestion')->getUserLink($aRow['friend_friend_user_id']);
                                        $sMessage = str_replace('{{friend_name}}', $sFriend_friend_name, $sMessage);
                                        
                                    }                 
                                    if ($aRow['message'] != '')
                                        $aRow['message'] = $sMessage . '<br />' . $_sMessage .  $aRow['message'];
                                    else
                                        $aRow['message'] = $sMessage . '<br />' . '<span style="color:#999999;">' ._p('suggestion.no_message') . '</span>';
                                    
                                    $aRow['message'] = '<img src="'.$aRow["icon"].'" /> ' . $aRow['message'];
                                    $aRow['accept'] = _p('suggestion.accept');                                    
                                    $aRow['ignore'] = _p('suggestion.ignore');                                    

                                    break; 
                                case 'marketplace':                            
                                case 'video':                            
                                case 'photo':                            
                                case 'forum':
                                case 'blog':
                                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_view';
                                        $sPhraseAccept = 'suggestion.view';
                                    break;
                                case 'poll':                                    
                                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_rate';    
                                        $sPhraseAccept = 'suggestion.rate';
                                    break;

                                case 'quiz':
                                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_take';    
                                        $sPhraseAccept = 'suggestion.take';
                                    break;

                                case 'event':
                                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_join';    
                                        $sPhraseAccept = 'suggestion.join';
                                    break;

                                case 'music':
                                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_listen';    
                                        $sPhraseAccept = 'suggestion.listen';
                                    break;                            
                                default:
                                        $sPhrase = 'suggestion.suggestion_friend_has_suggested_you_to_view';
                                        $sPhraseAccept = 'suggestion.view';
                                    break;

                            }
                            if ($sModule != 'friend'){
                                //get messages for each module
                                if (Phpfox::getUserId() != $aRow['user_id']){
                                    $sMessage = preg_replace('/{{friend_name}}/', Phpfox::getService('suggestion')->getUserLink($aRow['user_id']), _p($sPhrase));
                                    $sMessage = preg_replace('/{{you}}/', _p('suggestion.you'), $sMessage);
                                }else{
                                	$sMessage = preg_replace('/has/', "have", _p($sPhrase));
                                    $sMessage = preg_replace('/{{friend_name}}/', _p('suggestion.you'), $sMessage);
                                    $sMessage = preg_replace('/{{you}}/', Phpfox::getService('suggestion')->getUserLink($aRow['friend_user_id']), $sMessage);
                                }
                                
                                $sMessage = preg_replace('/{{data}}/', Phpfox::getService('suggestion.url')->makeLink($aRow['url'], $aRow['title']), $sMessage);
                                
                                if ($aRow['message'] != '')
                                    $aRow['message'] = '<img src="'.$aRow["icon"].'" /> ' . $sMessage . '<br />' . $_sMessage .$aRow['message'];
                                else
                                    $aRow['message'] = '<img src="'.$aRow["icon"].'" /> ' . $sMessage . '<br />' . '<span style="color:#999999;">' ._p('suggestion.no_message') . '</span>';
                                
                                
                                $_sModuleName = Phpfox::getService('suggestion')->getModuleName($sModule);
                                $aRow['accept'] = _p($sPhraseAccept) . ' ' . $_sModuleName;
                                $aRow['ignore'] = _p('suggestion.ignore');   
                            }
                        }

                        Phpfox::getService('suggestion.cache')->set($sKey, $aRows);
                }
                    
            }

            public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
            {
                
                $this->database()->join(Phpfox::getT('user'), 'user', 's.user_id = user.user_id')
                                ->join(Phpfox::getT('user'), 'user0', 's.friend_user_id = user0.user_id')
                                ->leftjoin(Phpfox::getT('user'), 'user1', 's.item_id= user1.user_id')
                                ->order('s.time_stamp DESC');
                
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
                    if ($sPlugin = Phpfox_Plugin::get('suggestion.service_browse__call'))
                    {
                            eval($sPlugin);
                            return;
                    }

                    /**
                     * No method or plug-in found we must throw a error.
                     */

                    Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
            }	
    }

    ?>