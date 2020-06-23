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
 */class Suggestion_Component_Block_Search extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$iPage = $this->getParam('page', 0);
		
		$iPageSize = 100;		
		$bIsOnline = false;		
		$oDb = Phpfox::getLib('database');
		$aParams = array();
		$aConditions = array();
		
                //get Mutals friends ID
                $iFriendId = (int)$_SESSION['iFriendId'];
                $sSuggestionType = $_SESSION['sSuggestionType'];
                $sModule = $_SESSION['sModule'];
                
                //expect user id who create current item to suggestion
                $sExpectUserId = $_SESSION['sExpectUserId'];
				
                if ($sExpectUserId != '')
                    $aConditions[] = 'AND u.user_id NOT IN (' . $sExpectUserId . ')';
                
                $this->template()->assign(array('iFriendId'=>$iFriendId));

				$aConditions[] = '';
                /*suggestion type: suggestion, recommendation, friend_add*/
                
                switch($sSuggestionType){
                    case 'suggestion':
                        if ($sModule == 'suggestion_friend'){
                            $iUserIdToGetFriendList = Phpfox::getUserId();

                            //get mutal friends
                            $sMutalsId=false;
                            $aMutalsId = Phpfox::getService('suggestion')->getMutualFriends($iFriendId);
                           
                            $sRet = '';
                            if (count($aMutalsId)){
                                for($i=0; $i<count($aMutalsId); $i++){                        
                                    $sRet .= $aMutalsId[$i]['id'].",";
                                }
                            }
                            $sMutalsId = substr($sRet, 0, strlen($sRet)-1);    
                            if ($sMutalsId == false ? $sMutalsId="" : true);

                            //get friends is in waiting lists of current userid
                            $aFriendsPending = Phpfox::getService('suggestion')->getFriendsPendingList($iFriendId);

                            $sRet = '';
                            if (count($aFriendsPending)){
                                for($i=0; $i<count($aFriendsPending); $i++){                        
                                    $sRet .= $aFriendsPending[$i]['user_id'].",";
                                }
                            }                
                            $sFriendsPending = substr($sRet, 0, strlen($sRet)-1);                                

                            if ($sFriendsPending != '')
                                if ($sMutalsId == '') $sMutalsId = $sFriendsPending; else $sMutalsId .= ",".$sFriendsPending;


                            $aSuggestions = Phpfox::getService('suggestion')->getSuggestionList(Phpfox::getUserId(), $iFriendId, $sModule);
                            $aResult = array();

                            //get friends list has been sent
                            foreach($aSuggestions as $aSuggestion){                                
                                $aRows = Phpfox::getService('suggestion')->getSuggestionData($aSuggestion['suggestion_id']);
                                
                                if (count($aRows)>0){
                                    foreach($aRows as $aRow){
                                        /*current check processed has been process or pending*/
                                        if ($aRow['processed'] < 2) {
                                            $aResult[$aRow['item_id']] = $aRow['item_id'];
                                        }
                                    }
                                }
                                
                            }
                            if (count($aResult)>0){
                                $sResult = implode(',', $aResult);
                                $aConditions[] = 'AND u.user_id NOT IN ('.$sResult.')';
                            }

                            $aConditions[] = 'AND friend.is_page = 0';
                            if ($sMutalsId != ''){
                                //remove friends id of friends when suggsetion his/him friends
                                $sMutalsId .= ",$iFriendId";
                                $aConditions[] = 'AND u.user_id NOT IN ('.$sMutalsId.')';
                            }else{
                                $aConditions[] = 'AND u.user_id NOT IN ('.$iFriendId.')';
                            }
                            
                        }else{
                            /*
                             * not friend suggestion
                             */  
							
                            $iUserIdToGetFriendList = Phpfox::getUserId();
                            $aSuggestions = Phpfox::getService('suggestion')->getSuggestionListByUserId(Phpfox::getUserId(), $iFriendId, $sModule);
                           
                            $aResult = array();
                            
                            if (count($aSuggestions)>0){
                                
                                foreach($aSuggestions as $aSuggestion){
                                    $aResult[] = $aSuggestion['friend_user_id'];
                                }
                                
                                if (count($aResult)>0){
                                    $sResult = implode(',', $aResult);
                                    $aConditions[] = 'AND u.user_id NOT IN ('.$sResult.')';
                                }                          
                            }
                        }
                    break;
                    
                    case 'recommendation':
                        
                            $iUserIdToGetFriendList = $iFriendId;

                            //get mutal friends
                            $sMutalsId=false;
                            $aMutalsId = Phpfox::getService('suggestion')->getMutualFriends(Phpfox::getUserId());

                            $sRet = '';
                            if (count($aMutalsId)){
                                for($i=0; $i<count($aMutalsId); $i++){                        
                                    $sRet .= $aMutalsId[$i]['id'].",";
                                }
                            }
                            $sMutalsId = substr($sRet, 0, strlen($sRet)-1);    

                            if ($sMutalsId == false ? $sMutalsId="" : true);

                            //get friends is in waiting lists of current userid
                            $aFriendsPending = Phpfox::getService('suggestion')->getFriendsPendingList(Phpfox::getUserId());

                            $sRet = '';
                            if (count($aFriendsPending)){
                                for($i=0; $i<count($aFriendsPending); $i++){                        
                                    $sRet .= $aFriendsPending[$i]['user_id'].",";
                                }
                            }                
                            $sFriendsPending = substr($sRet, 0, strlen($sRet)-1);                                

                            if ($sFriendsPending != '')
                                if ($sMutalsId == '') $sMutalsId = $sFriendsPending; else $sMutalsId .= ",".$sFriendsPending;

                            $aSuggestions = Phpfox::getService('suggestion')->getSuggestionList($iFriendId, Phpfox::getUserId(), $sModule);
                            $aResult = array();

                            //get friends list has been sent
                            foreach($aSuggestions as $aSuggestion){
                                $aRows = Phpfox::getService('suggestion')->getSuggestionData($aSuggestion['suggestion_id']);
                                if (count($aRows)>0){
                                    foreach($aRows as $aRow){
                                        if ($aRow['processed']<2)
                                        $aResult[$aRow['item_id']] = $aRow['item_id'];
                                    }
                                }
                            }

                            if (count($aResult)>0){
                                $sResult = implode(',', $aResult);
                                $aConditions[] = 'AND u.user_id NOT IN ('.$sResult.')';
                            }

                            $aConditions[] = 'AND friend.is_page = 0';

                            if ($sMutalsId != ''){                            
                                //remove friends id of friends when suggsetion his/him friends
                                $sMutalsId .= "," . Phpfox::getUserId();
                                $aConditions[] = 'AND u.user_id NOT IN ('.$sMutalsId.')';
                            }else{
                                $aConditions[] = 'AND u.user_id NOT IN ('.Phpfox::getUserId().')';
                            }
                        break;
                    
                    case 'friend_add':
                        
                        $iUserIdToGetFriendList = $iFriendId;
                        
                        //get mutal friends
                        $sMutalsId=false;
                        $aMutalsId = Phpfox::getService('suggestion')->getMutualFriends(Phpfox::getUserId());

                        $sRet = '';
                        if (count($aMutalsId)){
                            for($i=0; $i<count($aMutalsId); $i++){                        
                                $sRet .= $aMutalsId[$i]['id'].",";
                            }
                        }
                        $sMutalsId = substr($sRet, 0, strlen($sRet)-1);    
                        
                        if ($sMutalsId == false ? $sMutalsId="" : true);

                        //get friends is in waiting lists of current userid
                        $aFriendsPending = Phpfox::getService('suggestion')->getFriendsPendingList(Phpfox::getUserId());
                        
                        $sRet = '';
                        if (count($aFriendsPending)){
                            for($i=0; $i<count($aFriendsPending); $i++){                        
                                $sRet .= $aFriendsPending[$i]['user_id'].",";
                            }
                        }                
                        $sFriendsPending = substr($sRet, 0, strlen($sRet)-1);                                

                        if ($sFriendsPending != '')
                            if ($sMutalsId == '') $sMutalsId = $sFriendsPending; else $sMutalsId .= ",".$sFriendsPending;
                            
                        $aSuggestions = Phpfox::getService('suggestion')->getSuggestionList($iFriendId, Phpfox::getUserId(), $sModule);
                        $aResult = array();
                        
                        //get friends list has been sent and not processed
                        foreach($aSuggestions as $aSuggestion){
                            $aRows = Phpfox::getService('suggestion')->getSuggestionData($aSuggestion['suggestion_id']);
                            if (count($aRows)>0){
                                foreach($aRows as $aRow){
                                    if ($aRow['processed']==0)
                                        $aResult[$aRow['item_id']] = $aRow['item_id'];
                                }
                            }
                        }
                        
                        if (count($aResult)>0){
                            $sResult = implode(',', $aResult);
                            $aConditions[] = 'AND u.user_id IN ('.$sResult.')';
                        }else{
                            /*if has no user in pending list*/
                            $aConditions[] = 'AND u.user_id IN (0)';
                        }
                        
                        $aConditions[] = 'AND friend.is_page = 0';
                        
                        if ($sMutalsId != ''){                            
                            //remove friends id of friends when suggsetion his/him friends
                            $sMutalsId .= "," . Phpfox::getUserId();
                            $aConditions[] = 'AND u.user_id NOT IN ('.$sMutalsId.')';
                        }else{
                            $aConditions[] = 'AND u.user_id NOT IN ('.Phpfox::getUserId().')';
                        }
                        
                        
                    break;

                } 
		if ($this->getParam('type') != 'mail')
		{
			$aConditions[] = 'AND friend.user_id = ' . $iUserIdToGetFriendList;
		}
		
		if (($sFind = $this->getParam('find')) && $sFind != _p('suggestion.search_by_email_full_name') )
		{
                    //support unicode
                    $sFind = Phpfox::getLib('parse.input')->convert($sFind);
                    
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
		if ($sView = $this->getParam('view',''))
		{        
                    
			switch ($sView)
			{
				case 'top':
					$aConditions[] = 'AND is_top_friend = 1';
					break;
				case 'online':
					$bIsOnline = true;
                    $_SESSION['friends']['online'] = $bIsOnline;
					break;
				case 'all':
					//unset online status
                                        if (isset($_SESSION['friends']['online']))
                                            unset($_SESSION['friends']['online']);
					break;
				default:
					if (is_numeric($sView) && ($aList = Phpfox::getService('friend.list')->getList($sView, $iUserIdToGetFriendList)) && isset($aList['list_id']))
					{
						$aConditions[] = 'AND list_id = ' . (int) $aList['list_id'];
					}
					break;
			}
		}
		
		if ($this->getParam('type') == 'mail')
		{
			$aConditions[] = 'AND u.user_id != ' . $iUserIdToGetFriendList;
                       
			list($iCnt, $aFriends) = Phpfox::getService('user.browse')->conditions($aConditions)
				->sort('u.full_name ASC')
				->page($iPage)
				->limit($iPageSize)			
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
                   
                    if (isset($_SESSION['friends']['online']))
                        $bIsOnline = true;

                    $aConditions[] = 'And u.view_id=0 ';
                    list($iCnt, $aFriends) = Phpfox::getService('friend')->get($aConditions, 'u.full_name ASC', $iPage, $iPageSize, true, true, $bIsOnline);
					
		}
		
	
		
		$aParams['input'] = $this->getParam('input');
		$aParams['friend_item_id'] = $this->getParam('friend_item_id');
		$aParams['friend_module_id'] = $this->getParam('friend_module_id');
		$aParams['type'] = $this->getParam('type');
                
                
                
                //hardcode fix hidden view more block when total user = 0
                if ($iCnt == 0) $iCnt = 1;
                
		Phpfox::getLib('pager')->set(array('ajax' => 'suggestion.searchAjax', 'page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt, 'aParams' => $aParams));
		
		$sFriendModuleId = $this->getParam('friend_module_id', '');
                
                //set disabled button if has no friends
                if (count($aFriends)==0){
                    $bDisabled = true;
                }else{
                    $bDisabled = false;
                }
        foreach ($aFriends as &$value) {
            $value['full_name'] = Phpfox::getLib('phpfox.parse.output')->shorten($value['full_name'],11, '...');
        }
		
		$this->template()->assign(array(
				'aFriends' => $aFriends,
				'bDisabled' => $bDisabled,
				'aLetters' => $aLetters,
				'sView' => $sView,
				'sActualLetter' => $sLetter,
				'sPrivacyInputName' => $this->getParam('input'),
				'aLists' => Phpfox::getService('suggestion')->getUserList(),
				'bSearch' => $this->getParam('search'),
				'bIsForShare' => $this->getParam('friend_share', false),
				'sFriendItemId' => (int) $this->getParam('friend_item_id', '0'),
				'sFriendModuleId' => $sFriendModuleId,
				'sFriendType' => $this->getParam('type')
			)
		);
		(($sPlugin = Phpfox_Plugin::get('suggestion.component_block_search_process')) ? eval($sPlugin) : false);
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