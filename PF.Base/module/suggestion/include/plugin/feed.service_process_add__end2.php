<?php 
if ((int)$iParentUserId == 0)
{
	$bDisplay = true;
	$iTempA = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 0;
	$a = (int)strlen($iTempA);
	$b = (int)strlen(Phpfox::getLib("url")->makeUrl('advancedphoto.add'));
	// Fix bug migration from Phpfox Music to Music Sharing.
	$aCore = Phpfox::getLib('request')->getArray('core');
	$bMigration = false;
	if (isset($aCore['call']) && $aCore['call'] == 'musicsharing.migrateData')
	{
		$bMigration = true;
	}
	if (Phpfox::isModule('suggestion') && (Phpfox::isUser() || $iOwnerUserId > 0) && !$bMigration)
	{
		$sTypeId = $aInsert['type_id'];

		if(strpos($sTypeId, 'friend') === FALSE)
		{
			$_SESSION['feed_added'] = 1;
		}

		$aTypeId = explode("_", $sTypeId);
		$sModuleName = $aTypeId[0];
		$sPrefix = $this->_bIsCallback ? $this->_aCallback['table_prefix'] : '';

		/*
		 * not suggestion to friends if module is [friend, pages]
		 * not process for module friend
		 */
		if (!Phpfox::getService('suggestion')->isNotificationMessage($sTypeId) && Phpfox::getService('suggestion')->isSupportModule($sModuleName) && !in_array($sModuleName,array('friend','pages','link')))
		{
			$_aFeed = $aInsert;

			$_aFeed['feed_id'] = 0;
			$_aFeed['full_name'] = Phpfox::getUserBy('full_name');
			$_aFeed['gender'] = (int)Phpfox::getUserBy('gender');
			$_aFeed['prefix'] = $sPrefix;

			/*
			 * check if type id has sub action 
			 * ex: music_album will call back getFeedRedirectAlbum
			 * if not has sub action will callback: getFeedRedirect
			 * 
			 */        


			if (isset($aTypeId[1]))
			{
				$sFunction = 'getFeedRedirect' . ucfirst($aTypeId[1]);
				$sFunctionFeed = 'getActivityFeed' . ucfirst($aTypeId[1]);
			}
			else
			{
				$sFunction = 'getFeedRedirect';
				$sFunctionFeed = 'getActivityFeed';
			}

			/*
			 * hardcode for module not music and sType not company of jobposting
			 */

			
			if ($sModuleName != 'music')
			{
				/*
				 * get callback link of item has been insert
				 * if is module quiz do nothing
				 */
				
				//MinhNTK
				if ($sModuleName != 'quiz' && $sModuleName != 'link' && $sModuleName != 'document' && $sModuleName != 'foxfavorite' && Phpfox::hasCallback($sModuleName, $sFunction))
				{
					$sLinkCallBack = Phpfox::getService($sModuleName.'.callback')->$sFunction($_aFeed['item_id']);
				}
				else
				{
					$sLinkCallBack = '';
				}
				
	
				if (Phpfox::hasCallback($sModuleName, $sFunctionFeed))
				{
					$_aFeedDetail= Phpfox::getService($sModuleName.'.callback')->$sFunctionFeed($_aFeed);

				}


				if (isset($_aFeedDetail['feed_title']))
				{
					$sTitle = $_aFeedDetail['feed_title'];
				}
				else
				{
					$sTitle = $_aFeed['sModule'];
				}


				$_aFeed['title'] = $sTitle;
				$_aFeed['sLinkCallback'] = $sLinkCallBack;
				$_aFeed['sModule'] = $sModuleName;


				if ( ( $sType == 'contest' || $sType == 'coupon' || $sType =='jobposting_job' ) )
				{
					$aRet['title'] = $sTitle;
					$aRet['sLinkCallback'] = $sLinkCallBack;
					$aRet['sModule'] = $sModuleName;
					$aRet['type_id'] = $_aFeed['type_id'];
					$aRet['item_id'] = $_aFeed['item_id'];
					$aRet['prefix'] = $sPrefix;
					if($sType =='jobposting_job')
						$aRet['sLinkCallback'] = $_aFeedDetail['feed_link'];
					
					$sRet = base64_encode(serialize($aRet));					

					Phpfox::getService('suggestion.process')->add($aInsert['user_id'], $iFriendId = 0, $sFriendList = 0, $sModule = 0, $sMessage = $sRet, $sLinkCallback = '', $sTitle = '', '', true);

				}

				if ($sModuleName == 'jobposting') {
						$_aFeed['sLinkCallback'] = $_aFeedDetail['feed_link'];
				}

				
				if ($sModuleName == 'forum')
				{
					/*update forum link*/
					if (Phpfox::getParam('core.url_rewrite') < 2)
					{
						$sLinkCallBack = Phpfox::getLib('url')->getDomain().'forum/thread/'.$_aFeed['item_id'].'/'.$_aFeed['title'].'/';
					}
					else
					{
						$sLinkCallBack = Phpfox::getLib('url')->getDomain().'index.php?do=/forum/thread/'.$_aFeed['item_id'].'/'.$_aFeed['title'].'/';
					}
					$_aFeed['sLinkCallback'] = $sLinkCallBack;                
				}

				//if is moduel channel waiting for show suggestion popup page after added video or video of channel            
				if ($sModuleName == 'videochannel' && $_aFeed['type_id']!="videochannel_favourite" && $_aFeed['type_id']!="videochannel_unfavourite")
				{
					if (Phpfox::getUserParam('suggestion.enable_friend_suggestion') && Phpfox::getUserParam('suggestion.enable_content_suggestion_popup') && Phpfox::getService('suggestion')->isAllowContentSuggestionPopup())
					{
						$_sItemId = urlencode($_aFeed['item_id']);
						$_sTitle = base64_encode(urlencode($_aFeed['title']));
						$sShowSuggestion = "suggestion_and_recommendation_tb_show('...',$.ajaxBox('suggestion.friends','iFriendId=".$_sItemId."&sSuggestionType=suggestion&sModule=suggestion_videochannel&sLinkCallback=".$sLinkCallBack."&sTitle=".$_sTitle."&sPrefix=&sExpectUserId='));";
						echo '<script type="text/javascript">setTimeout(function(){'.$sShowSuggestion.'},1300);</script>';
					}
					else
					{
						unset($_SESSION['suggestion']);
					}
				}

				if ($sModuleName == 'video')
				{
					if (Phpfox::getUserParam('suggestion.enable_friend_suggestion') && Phpfox::getUserParam('suggestion.enable_content_suggestion_popup') && Phpfox::getService('suggestion')->isAllowContentSuggestionPopup())
					{
						$_sItemId = urlencode($_aFeed['item_id']);
						$_sTitle = base64_encode(urlencode($_aFeed['title']));
						$sShowSuggestion = "suggestion_and_recommendation_tb_show('...',$.ajaxBox('suggestion.friends','iFriendId=".$_sItemId."&sSuggestionType=suggestion&sModule=suggestion_video&sLinkCallback=".$sLinkCallBack."&sTitle=".$_sTitle."&sPrefix=&sExpectUserId='));";
						echo 'setTimeout(function(){'.$sShowSuggestion.'},1300);';
					}
					else
					{
						unset($_SESSION['suggestion']);
					}
				}
				if($_aFeed['type_id']!='videochannel_favourite' && $_aFeed['type_id']!='videochannel_unfavourite' && $_aFeed['type_id']!='jobposting_company')
				{
					$_SESSION['suggestion']['aFeed'] = $_aFeed;
					Phpfox::setCookie("suggestion", json_encode(array('aFeed' => $_aFeed)));
				}
			 
			if(isset($_SESSION['suggestion']['aFeed']['type_id']) && $_SESSION['suggestion']['aFeed']['type_id']=='blog')
			{
				if(isset($_SESSION['suggestion']['aFeed']['blog_id']) && $_SESSION['suggestion']['aFeed']['blog_id']>0)
					$_SESSION['suggestion']['aFeed']['item_id'] = $_SESSION['suggestion']['aFeed']['blog_id'];
			}
			
			}
			else
			{
				/*
				 * process for module music
				 * store template data to DB & replace when suggestion box is called
				 */ 

				$iSongId = $_aFeed['item_id'];

				/*
				 * hardcode always suggestion a song.
				 */

				$sFunction = 'getFeedRedirectSong';
				$sFunctionFeed = 'getActivityFeedSong';

				$aSong = Phpfox::getService('music')->getSong($iSongId);

				if (count($aSong) > 0)
				{
					$iAlbumId = $_aFeed['item_id'];

					$sLinkCallBack = Phpfox::getService($sModuleName.'.callback')->$sFunction($iAlbumId);
					$_aFeedDetail= Phpfox::getService($sModuleName.'.callback')->$sFunctionFeed($_aFeed);
					if (isset($_aFeedDetail['feed_title'])){
						$sTitle = $_aFeedDetail['feed_title'];
					}else{
						$sTitle = $_aFeed['sModule'];
					}
					$aRet['title'] = $sTitle;
					$aRet['sLinkCallback'] = $sLinkCallBack;
					$aRet['sModule'] = $sModuleName;
					$aRet['type_id'] = $_aFeed['type_id'];
					$aRet['item_id'] = $_aFeed['item_id'];
					$aRet['prefix'] = $sPrefix;
					$sRet = base64_encode(serialize($aRet));
					
					$iUserId = Phpfox::getUserId();

					if (!isset($_SESSION['suggestion']['ajax']))
					{
						Phpfox::getService('suggestion.process')->add($iUserId, $iFriendId = 0, $sFriendList = 0, $sModule = 0, $sMessage = $sRet, $sLinkCallback = '', $sTitle = '', '', true);
					}
				}
			}
		}
	?>

	<?php }/*end check module*/?>

	<?php 
	// Turn on popup when creating a feed on activity feed or wall
	$support_advphoto = 0;
	if (isset($_SESSION['suggestion']['aFeed']) && ($a != $b || ($a==$b && $_SESSION['suggestion']['aFeed']['sModule']!='advancedphoto')))
	{
		if ($_SESSION['suggestion']['aFeed']['sModule']!="advancedphoto" || ($_SESSION['suggestion']['aFeed']['sModule']=="advancedphoto" && isset($_POST['action'])))
		{
			$support_advphoto = 1;
		}

		if ($support_advphoto && Phpfox::isModule('suggestion') && Phpfox::isUser() && isset($_SESSION['suggestion']['aFeed']['sModule']) && $_SESSION['suggestion']['aFeed']['sModule']!="videochannel" && $_SESSION['suggestion']['aFeed']['sModule']!="video" && ($_SESSION['suggestion']['aFeed']['sModule']!="photo" || ($_SESSION['suggestion']['aFeed']['sModule']=="photo" && isset($_POST['action']))))
		{
			$bShow = 0;
			/*check if any item approved by admin; not show suggestion*/
			if (Phpfox::getService('suggestion.process')->isIgnoreSuggestion())
			{
				unset($_SESSION['suggestion']['aFeed']);
			}

			if (isset($_SESSION['suggestion']['aFeed']))
			{
				$_aFeed = $_SESSION['suggestion']['aFeed'];     
			 
				$sLinkCallback = $_aFeed['sLinkCallback'];
				$sTitle = base64_encode(urlencode($_aFeed['title'].''));

				if (($_aFeed['sModule']=='photo' && $_aFeed['type_id']=='photo') || ($_aFeed['sModule']=='advancedphoto' && $_aFeed['type_id']=='advancedphoto'))
				{
					if(Phpfox::isModule("photo"))
					{
						$aPhoto = Phpfox::getService("photo")->getCoverPhoto($_aFeed['item_id']);
						$_SESSION['suggestion']['photo']['title'] = $aPhoto['title'];
						$_SESSION['suggestion']['photo']['photo_id'] = $aPhoto['photo_id'];
					}
					else if(Phpfox::isModule("advancedphoto"))
					{
						 $aPhoto = Phpfox::getService("advancedphoto")->getCoverPhoto($_aFeed['item_id']);
					}

					if ($_aFeed['title'] == "")
					{
						 $sTitle = base64_encode(urlencode($aPhoto['title'].''));
					}
				}

				$sPrefix = $_SESSION['suggestion']['aFeed']['prefix'];
				$bShow = 1;    

				if (isset($_SESSION['suggestion']['photo']['method']) && $_SESSION['suggestion']['photo']['method'] == 'simple')
				{
					$bDisplay = false;
				}
				

				if ($_aFeed['sModule'] == 'blog')
				{
					$bDisplay = true;
				}
				
				if ($_aFeed['sModule'] == 'music')
				{
					$bDisplay = false;
				}

				if ($_aFeed['sModule'] == 'contest')
				{
					$bDisplay = false;
				}

				if ($_aFeed['sModule'] == 'coupon')
				{

					$bDisplay = false;
				}


				if ($_aFeed['sModule'] == 'jobposting')
				{

					$bDisplay = false;
				}

			}
			else
			{
			}

			// Ajax call.
			$ynsuggestion_ajax = Phpfox::getCookie('suggestion_ajax');
			if (isset($_SESSION['suggestion']['ajax'])
				|| (isset($ynsuggestion_ajax) && $ynsuggestion_ajax == '1')
				)
			{
				Phpfox::setCookie('suggestion_ajax', '', -1);
				$oAjax = Phpfox::getLib('ajax');
				if(($_aFeed['sModule'] == 'photo' && Phpfox::getParam('photo.html5_upload_photo'))
					|| ($_aFeed['sModule'] == 'advancedphoto' && Phpfox::getParam('advancedphoto.html5_upload_photo'))
					){
					// do not process for uploading by html5 (photo, advancedphoto)

				} else {

					if ( !isset($_SESSION['suggestion']['photo']['method']) || $_SESSION['suggestion']['photo']['method'] != 'simple' ) {
						$oAjax->call('window.parent.suggestion_and_recommendation_tb_show("...", window.parent.$.ajaxBox(\'suggestion.friends\', \'iFriendId=\' + ' . $_aFeed['item_id'] . ' + \'&sSuggestionType=suggestion&sModule=suggestion_' . $_aFeed['sModule'] . '&sLinkCallback=' . $sLinkCallback . '&sTitle=' . $sTitle . '&sPrefix=' . $sPrefix . '&sExpectUserId=\'));'); 
					}
				}

				unset($_SESSION['suggestion']['ajax']);
				if (isset($_SESSION['suggestion']['aFeed']))
				{
					unset($_SESSION['suggestion']['aFeed']);
				}
			}

		}
	}
}
 
?>