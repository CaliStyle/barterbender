<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Suggestion
 * @version 		$Id: ajax.class.php 1 2011-11-25 15:29:17Z YOUNETCO $
 */
class Suggestion_Component_Ajax_Ajax extends Phpfox_Ajax
{

	/*
	 * remove all session relate with suggestion, when admin click approve any items.
	 * Not display suggestion popup
	 */
	function savechangeclick()
	{
		$value1 = $this -> get('value1');
		$value2 = $this -> get('value2');
		$value3 = $this -> get('value3');
		phpfox::getService("suggestion.process") -> updateSetting($value1, $value2, $value3);
		$this -> call("$('form:last').submit();");
        $this->call('window.location.href = \'' . Phpfox_Url::instance()->makeUrl('') . 'user/privacy/?tab=suggestion_tab' . '\';');
	}

	public function dontAskMeAgain()
	{
		Phpfox::getService("suggestion.process") -> dontAskMeAgain((int)$this -> get('bDontAskMeAgain', 0));
	}

	function config()
	{

		Phpfox::getBlock('suggestion.config', array());
	}

	public function ignoreSuggestion()
	{
		Phpfox::getService('suggestion.process') -> ignoreSuggestion();
	}

	public function ignoreSuggestFriend()
	{
		Phpfox::getService('suggestion.process') -> changeStatusSuggestFriend((int)$this -> get('iUserId'), (int)Phpfox::getUserId(), (int)$this -> get('iItemId'), 2, "suggestion_friend");
		return true;
	}

	public function showSocialPublishers()
	{

		if (isset($_SESSION['feed_added']))
		{
			echo '<div id="btnHideBox" onclick="js_box_remove(this);">&nbsp;</div>';
			$this -> call('<script>$("#btnHideBox").click();</script>');
			unset($_SESSION['feed_added']);

			if (Phpfox::isModule('socialpublishers'))
			{
				(($sPlugin = Phpfox_Plugin::get('suggestion.component_add_request')) ? eval($sPlugin) : false);
			}

		}

	}

	/*
	 * compose a new message to join event or pages
	 */
	public function compose()
	{
		Phpfox::isUser(true);

		$this -> setTitle(_p('suggestion.privacy_notification_message'));

		Phpfox::getComponent('suggestion.compose', null, 'controller');

		(($sPlugin = Phpfox_Plugin::get('suggestion.component_ajax_compose')) ? eval($sPlugin) : false);

		echo '<script type="text/javascript">$Core.loadInit();</script>';
	}

	public function composeProcess()
	{
		Phpfox::isUser(true);

		$this -> errorSet('#js_ajax_compose_error_message');

		$oObject = Phpfox::getComponent('suggestion.compose', null, 'controller');

		if ($oObject -> getReturn())
		{
			$this -> call('$(\'#\' + tb_get_active()).find(\'.js_box_content:first\').html(\'<div class="message">' . _p('mail.your_message_was_successfully_sent') . '</div>\'); setTimeout(\'tb_remove();\', 2000);');
		}
	}

	public function changeShow()
	{
		$iChecked = (int)$this -> get('checked');
		$_SESSION['show'] = $iChecked;
	}

	public function approve()
	{
		$iFriendId = (int)$this -> get('iFriendId');
		$iItemId = (int)$this -> get('iItemId');
		$iApprove = (int)$this -> get('iApprove');
		$iProcessId = $this -> get('iProcessId', '');
		$sModule = htmlentities($this -> get('sModule', ''));
		$sUrl = $this -> get('sUrl', '');
		$bAddFriend = $this -> get('bAddFriend', 1);

		$suggestionId = substr($iProcessId, 8, strlen($iProcessId));
		switch($sModule)
		{
			case 'suggestion_friend' :
				Phpfox::getService('suggestion.process') -> approve($iFriendId, $iItemId, $iApprove, $sModule, $suggestionId);

				/*
				 * hide ajax process
				 * add friends request [accept or deny]
				 * reload current page
				 * effect for friend page if iProcessId != ''
				 */

				if ($iProcessId != '')
				{
					$this -> call('$("#' . $iProcessId . '").find(".ajaxLoader").hide();');
					$this -> call('$("#left").find("li[class*=\'active\']").eq(0).find("a").eq(0).click();');
					/*accept friends*/
					if ($bAddFriend == 1 && $iApprove == 1)
					{
						Phpfox::getService('friend.request.process') -> add($iFriendId, $iItemId, 0, $sMessage = '');
					} elseif ($iApprove == 2)
					{
						/*Deny friends, remove friends for friends pending list*/
						Phpfox::getService('suggestion.process') -> denyFriend($iFriendId, $iItemId);
					}
					$this -> call('window.location.reload();');
				}

				break;
			case 'people_you_may_know' :
				Phpfox::getService('friend.request.process') -> add($iFriendId, $iItemId, 0, $sMessage = '');
				$this -> call('window.location.reload();');

				break;
			case 'pages_you_may_like' :
				Phpfox::getLib('database') -> insert(Phpfox::getT('like'), array('type_id' => 'pages', 'item_id' => (int)$iItemId, 'user_id' => (int)$iFriendId, 'time_stamp' => PHPFOX_TIME));
				Phpfox::getLib('database') -> updateCount('like', 'type_id = \'pages\' AND item_id = ' . (int)$iItemId . '', 'total_like', 'pages', 'page_id = ' . (int)$iItemId);

				$this -> call('window.location.reload();');
				break;
			case 'jobs_you_may_like' :
				$url = Phpfox::getLib('url') -> makeUrl('pages', $aPage['page_id']);
				$this -> call('window.location.href = $url;');
				break;
			case 'suggestion_pages' :
				Phpfox::getLib('database') -> insert(Phpfox::getT('like'), array('type_id' => 'pages', 'item_id' => (int)$iItemId, 'user_id' => (int)$iFriendId, 'time_stamp' => PHPFOX_TIME));
				Phpfox::getLib('database') -> updateCount('like', 'type_id = \'pages\' AND item_id = ' . (int)$iItemId . '', 'total_like', 'pages', 'page_id = ' . (int)$iItemId);

				Phpfox::getService('suggestion.process') -> approve($iFriendId, $iItemId, $iApprove, $sModule, $suggestionId);

				$this -> call('window.location.reload();');

				break;
			/*
			 * all module expect suggestion_friend
			 * process if accept redirect to this destination
			 */
			default :
				Phpfox::getService('suggestion.process') -> approve($iFriendId, $iItemId, $iApprove, $sModule, $suggestionId);
				$this -> call('$("#' . $iProcessId . '").find(".ajaxLoader").hide();');

				/*
				 * accept this suggestion, redirect to this page
				 */
				if ($iApprove == 1 && $sUrl != '')
				{

					$this -> call('window.location = "' . $sUrl . '";');

				} else
				{
					/*reload current page*/
					$this -> call('window.location.reload()');
				}

				break;
		}
	}

	public function delete()
	{
		$iSuggestId = $this -> get('iSuggestId', '');
		Phpfox::getService('suggestion.process') -> deleteSuggestion($iSuggestId);
		$this -> call('window.location.reload()');
	}

	public function deleteReminder()
	{
		$iReminderId = $this -> get('iReminderId', '');
		Phpfox::getService('suggestion.reminder') -> deleteReminder($iReminderId);
		$this -> call('window.location.reload()');
	}

	public function friends()
	{
		if ((Phpfox::getUserParam('suggestion.enable_friend_recommend') || Phpfox::getUserParam('suggestion.enable_friend_suggestion')))
		{
			$ynCookie = Phpfox::getCookie('suggestion');
			if (isset($_SESSION['suggestion']['aFeed']) || (isset($ynCookie) && strlen($ynCookie) > 0))
			{
				$ynCookie = json_decode($ynCookie, true);
				unset($_SESSION['suggestion']['aFeed']);

				$sLink = $ynCookie['aFeed']['sLinkCallback'];
				$sTitle = $ynCookie['aFeed']['title'];
				$__sModule = $ynCookie['aFeed']['type_id'];
				Phpfox::setCookie('suggestion', '', -1);
			} else
			{
				//not set sesstion get private data set into database
				//hardcode for module default: 'Music'
				$aPrivateData = Phpfox::getService('suggestion') -> getPrivateData(Phpfox::getUserId());

				if (isset($aPrivateData) && count($aPrivateData) > 0)
				{
					$aRet = unserialize(base64_decode($aPrivateData['message']));

					$sLink = urlencode($aRet['sLinkCallback']);
					$sTitle = base64_encode(urlencode($aRet['title']));
					$__sModule = $aRet['sModule'];
					$sPrefix = $aRet['prefix'];
					$bShow = 1;

					Phpfox::getService('suggestion.process') -> deletePrivateData(Phpfox::getUserId());
				}
			}

			if (isset($_SESSION['iFriendId']))
				unset($_SESSION['iFriendId']);
			if (isset($_SESSION['sSuggestionType']))
				unset($_SESSION['sSuggestionType']);
			if (isset($_SESSION['sModule']))
				unset($_SESSION['sModule']);

			$iFriendId = (int)$this -> get('iFriendId');
			$_SESSION['iFriendId'] = $iFriendId;

			$sSuggestionType = htmlentities($this -> get('sSuggestionType', 'suggestion'));
			$_SESSION['sSuggestionType'] = $sSuggestionType;

			$sModule = htmlentities($this -> get('sModule', 'suggestion_friend'));
			$_SESSION['sModule'] = $sModule;

			if (!isset($sLink))
			{
				$sLink = htmlentities($this -> get('sLinkCallback', ''));
			}

			$_SESSION['sLinkCallback'] = $sLink;

			// $sTitle = ($this->get('sTitle',''));
			$sTitle = base64_decode($this -> get('sTitle', ''));
			$sTitle = urldecode($sTitle);

			/* fix for module photo does not have info when add new */
			$iIsAdded = (int)Phpfox::getService('suggestion.cache') -> get('photo.added');
			if (isset($_SESSION['suggestion']['photo']) && $iIsAdded == 1)
			{
				$sTitle = $_SESSION['suggestion']['photo']['title'];
				unset($_SESSION['suggestion']['photo']);

				Phpfox::getService('suggestion.cache') -> remove('photo.added');
			}

			$_SESSION['sTitle'] = $sTitle;

			$sExpectUserId = $this -> get('sExpectUserId', '');
			$_SESSION['sExpectUserId'] = $sExpectUserId;

			$_sPrefix = htmlentities($this -> get('sPrefix', ''));
			$_SESSION['sPrefix'] = $_sPrefix;

			switch ($sSuggestionType)
			{
				//suggestion friends of current userid to friends
				case 'suggestion' :
					//if current online filter session is set, clear session
					if (isset($_SESSION['friends']['online']))
						unset($_SESSION['friends']['online']);

					$iSuggestion = 0;

					$aModule = explode('_', $sModule);
					if (count($aModule) > 0)
						$_sModuleName = ucfirst($aModule[1]);
					else
						$_sModuleName = '';

					if ($_sModuleName == 'Pages')
					{
						if ($sExpectUserId == 0)
						{
							$suggest_page = phpfox::getService("pages") -> getPage($iFriendId);
							if (count($suggest_page) > 0)
							{
								$sExpectUserId = $suggest_page['user_id'];
								$_SESSION['sExpectUserId'] = $sExpectUserId;
							}
						}

						$_sModuleName = 'Page';
					}

					switch ($sModule)
					{
						case 'suggestion_friend' :
							$iSuggestion = Phpfox::getService('suggestion') -> isAllowRecommendationPopup() && Phpfox::getUserParam('suggestion.enable_friend_recommend');
							$aFriend = Phpfox::getService('user') -> getUser($iFriendId, 'u.full_name, u.user_name');
							$sUserName = $aFriend['user_name'];
							$sFullName = $aFriend['full_name'];

							$sLink = Phpfox::getService('suggestion') -> getUserLink($iFriendId);

							$sHeader = str_replace("{{friend_name}}", $sLink, _p('suggestion.you_are_now_friend_with_select_friend_suggestion'));

							break;
						case 'suggestion_marketplace' :
						case 'suggestion_pages' :
						case 'suggestion_video' :
						case 'suggestion_photo' :
						case 'suggestion_forum' :
						case 'suggestion_blog' :
							$sHeader = _p('suggestion.suggestion_popup_message_viewing');
							break;

						case 'suggestion_poll' :
							$sHeader = _p('suggestion.suggestion_popup_message_voting');
							break;

						case 'suggestion_quiz' :
							$sHeader = _p('suggestion.suggestion_popup_message_taking');
							break;

						case 'suggestion_event' :
							$sHeader = _p('suggestion.suggestion_popup_message_joining');
							break;

						case 'suggestion_music' :
							$sHeader = _p('suggestion.suggestion_popup_message_listening');

							break;

						default :
							if ($sTitle == '')
								$sTitle = $__sModule;
							$sHeader = _p('suggestion.suggestion_popup_message_default');

							break;
					}

					if ((empty($sTitle)) || ($sTitle == 'forum_post'))
					{
						if ($sModule == 'suggestion_forum')
						{
							$oforum_post = Phpfox::getService('suggestion') -> getForumPostByForumPostID($iFriendId);
							$iThreadId = isset($oforum_post['thread_id']) ? $oforum_post['thread_id'] : 0;
							$sLink = Phpfox::getLib("url") -> makeUrl('forum.thread') . $iThreadId . "/view_" . $iFriendId;
							$_SESSION['sLinkCallback'] = $sLink;
							$sTitle = isset($oforum_post['thread_title']) ? $oforum_post['thread_title'] : "";
							$_SESSION['sTitle'] = $sTitle;
						}
					}

					$__sLink = Phpfox::getService('suggestion.url') -> makeLink($sLink, $sTitle);
					$sHeader = preg_replace('/{{name}}/', $__sLink, $sHeader);
					$sHeader = preg_replace('/{{suggestion_name}}/', $_sModuleName, $sHeader);

					echo '<p style="line-height:1.5em; background-color:#BAFC8B; margin:0 0 10px 0; padding:10px;">' . $sHeader . '</p>';
					$sTitle = _p('suggestion.suggestion');

					$this -> setTitle($sTitle);

					$html = Phpfox::getBlock('suggestion.friends', array('iFriendId' => $iFriendId, 'iSuggestion' => $iSuggestion, 'sModule' => $sModule, 'sTitle' => $_SESSION['sTitle'], 'sLink' => $_SESSION['sLinkCallback'], 'sSuggestionType' => $sSuggestionType));

					break;

				case 'recommendation' :
					//recommend friends
					//if current online filter session is set, clear session
					if (isset($_SESSION['friends']['online']))
						unset($_SESSION['friends']['online']);

					$this -> template() -> assign(array('iSuggestion' => 0));
					$sLink = Phpfox::getService('suggestion') -> getUserLink($iFriendId);

					$sHeader = '';
					$sUserLink = Phpfox::getService('suggestion') -> getUserLink($iFriendId);

					if (!$this -> get('skip'))
						$sHeaderTop = preg_replace('/{{friend_name}}/', $sUserLink, _p('suggestion.you_has_just_succeeded_sending_your_friend_suggestion_to_friend'));
					else
						$sHeaderTop = preg_replace('/{{friend_name}}/', $sUserLink, _p('suggestion.you_has_just_skipped_sending_your_friend_suggestion_to_friend'));

					echo '<p style="line-height:1.5em; background-color:#BAFC8B; margin:0 0 10px 0; padding:10px;">' . $sHeaderTop . '<BR>' . $sHeader . '</p>';

					$sTitle = _p('suggestion.recommendation');

					Phpfox::getBlock('suggestion.friends', array('iFriendId' => $iFriendId, 'sSuggestionType' => $sSuggestionType));

					$this -> setTitle($sTitle);
					break;

				//choose friends of suggestion list to add
				default :
					//if current online filter session is set, clear session
					if (isset($_SESSION['friends']['online']))
						unset($_SESSION['friends']['online']);

					$this -> template() -> assign(array('iSuggestion' => 0));
					//recommend friends

					$sLink = Phpfox::getService('suggestion') -> getUserLink($iFriendId);
					$sHeader = preg_replace('/{{friend_name}}/', $sLink, _p('suggestion.here_are_friend_select_member_to_get_from_you'));
					$sTitle = _p('suggestion.add_friend');
					echo '<p style="line-height:1.5em; background-color:#BAFC8B; margin:0 0 10px 0; padding:10px;">' . $sHeader . '</p>';

					Phpfox::getBlock('suggestion.friends', array('iFriendId' => $iFriendId, 'sSuggestionType' => $sSuggestionType));

					$this -> setTitle($sTitle);
					break;
			}
		}
	}

	public function update_user_notification()
	{
		$bEnableSuggestion = $this -> get('bEnableSuggestion', 1);
		$bEnableRecommendation = $this -> get('bEnableRecommendation', 1);
	}

	public function append_user_image()
	{

		if (Phpfox::getUserParam('suggestion.enable_friend_recommend') || Phpfox::getUserParam('suggestion.enable_friend_suggestion'))
		{
			$sUserId = $this -> get('sUserId');
			$aUserId = explode(",", $sUserId);
			if (count($aUserId) > 0)
			{
				foreach ($aUserId as $iUserId)
				{
					if ($iUserId == 0)
						return '';
					$aUser = Phpfox::getService('suggestion') -> getUser($iUserId);
					$aUser['suffix'] = '_120';
					$aUser['max_width'] = '50';
					$aUser['max_height'] = '50';
					$aUser['user'] = $aUser;
					$this -> call("$('.suggestion_user_thumb_$iUserId').remove();");
					$img = '<span class="suggestion_user_thumb_' . $iUserId . '">' . Phpfox::getLib('phpfox.image.helper') -> display($aUser) . '</span>';

					$sAppendId = 'js_friend_' . $iUserId;
					$this -> prepend('#' . $sAppendId, $img);
				}
			}
		}
	}

	public function search()
	{
		if (Phpfox::getUserParam('suggestion.enable_friend_recommend') || Phpfox::getUserParam('suggestion.enable_friend_suggestion'))
		{

			Phpfox::getBlock('suggestion.search', array('input' => $this -> get('input'), 'friend_module_id' => $this -> get('friend_module_id'), 'friend_item_id' => $this -> get('friend_item_id'), 'type' => $this -> get('type')));
			if ($this -> get('type') == 'mail')
			{
				$this -> call('<script type="text/javascript">$(\'#TB_ajaxWindowTitle\').html(\'' . _p('friend.search_for_members', array('phpfox_squote' => true)) . '\');</script>');
			} else
			{
				$this -> call('<script type="text/javascript">$(\'#TB_ajaxWindowTitle\').html(\'' . _p('friend.search_for_your_friends', array('phpfox_squote' => true)) . '\');</script>');
			}
		}
	}

	public function searchAjax()
	{
		if (Phpfox::getUserParam('suggestion.enable_friend_recommend') || Phpfox::getUserParam('suggestion.enable_friend_suggestion'))
		{
			Phpfox::getBlock('suggestion.search', array('search' => true, 'friend_module_id' => $this -> get('friend_module_id'), 'friend_item_id' => $this -> get('friend_item_id'), 'page' => $this -> get('page'), 'find' => $this -> get('find'), 'letter' => $this -> get('letter'), 'input' => $this -> get('input'), 'view' => $this -> get('view'), 'type' => $this -> get('type')));

			$this -> call('$(\'#js_friend_search_content\').html(\'' . $this -> getContent() . '\'); updateFriendsList();');
			/*disable select all button if has no friends*/
			$this -> call('if($("#searchBlock").find(".extra_info").html()){$("#selectAll").attr("disabled","disabled")};');
		}
	}

	/*
	 * add suggestion request to member
	 */
	public function addRequest()
	{

		if (Phpfox::getUserParam('suggestion.enable_friend_recommend') || Phpfox::getUserParam('suggestion.enable_friend_suggestion'))
		{
			Phpfox::getUserParam('friend.can_add_friends', true);

			//flush all cache of all user
			Phpfox::getService('suggestion.cache') -> removeAll();

			$aVals = $this -> get('val');
			$sMessage = $aVals['message'];
			if ($this -> get('sSuggestionType') != null)
				$sSuggestionType = $this -> get('sSuggestionType');
			else
				$sSuggestionType = $_SESSION['sSuggestionType'];
			if ($this -> get('sModule') != null)
				$sModule = $this -> get('sModule');
			else
				$sModule = $_SESSION['sModule'];
			if (isset($_SESSION['sPrefix']))
				$sPrefix = $_SESSION['sPrefix'];
			else
				$sPrefix = '';

			if ($this -> get('iFriendId') != null)
				$iFriendId = $this -> get('iFriendId');
			else
				$iFriendId = $_SESSION['iFriendId'];

			switch ($sSuggestionType)
			{
				/*
				 * Suggestion for all module
				 */
				case 'suggestion' :
					switch($sModule)
					{
						case 'suggestion_friend' :
							if (count($aVals['to']) == 0)
								return;

							$aUserId = array_unique($aVals['to']);

							$sFriendList = implode(",", $aUserId);
							$iCurrentUserId = Phpfox::getUserId();

							if ($iLastId = Phpfox::getService('suggestion.process') -> add(Phpfox::getUserId(), $iFriendId, $sFriendList, $sModule, $sMessage, $sPrefix))
							{

								//add notification
								if (Phpfox::isModule('notification'))
								{
									/*
									 * overwrite insert to notification
									 */
									Phpfox::getService('suggestion.process') -> addNotification($sModule, Phpfox::getUserId(), $iFriendId, $iLastId);

								}
							}

							if (Phpfox::getUserParam('suggestion.enable_friend_recommend') && Phpfox::getService('suggestion') -> isAllowRecommendationPopup())
							{
								$sParam = 'sSuggestionType=recommendation&iFriendId=' . $iFriendId;
								$this -> call("suggestion_and_recommendation_tb_show('',$.ajaxBox('suggestion.friends','" . $sParam . "'))");
							} else
							{
								$this->alert( _p('suggestion.completed_request'));
							}
							//clear current friends of current user
							break;
						/*
						 * suggesiton for module not include friend
						 */
						default :
							if (count($aVals['to']) == 0)
								return;

							$iDataId = (int)$this -> get('iFriendId');
							if ($iDataId == 0)
								$iDataId = $_SESSION['iFriendId'];

							$sLinkCallback = $this -> get('sLink', '');
							if ($sLinkCallback == '')
								$sLinkCallback = $_SESSION['sLinkCallback'];

							$sTitle = $this -> get('sTitle', '');
							if ($sTitle == '')
							{
								$sTitle = $_SESSION['sTitle'];
							}

							$aUserId = array_unique($aVals['to']);

							foreach ($aUserId as $iUserId)
							{
								$aSuggestionDetail = Phpfox::getService('suggestion') -> getSuggestionDetailByUserId(Phpfox::getUserId(), $iUserId, $sModule, $iDataId);

								/*
								 * if has not been suggested to this friends.
								 * add to suggestion
								 */

								if (count($aSuggestionDetail) == 0 || (count($aSuggestionDetail) > 0 && $aSuggestionDetail['processed'] == 2))
								{

									$iLastId = Phpfox::getService('suggestion.process') -> add(Phpfox::getUserId(), $iUserId, $iDataId, $sModule, $sMessage, $sLinkCallback, $sTitle, $sPrefix);

									$this -> updateMissingField($sModule);

									/*
									 * manual insert to notification
									 */
									Phpfox::getService('suggestion.process') -> addNotification($sModule, $iDataId, $iUserId, $iLastId);
								}
							}
							$this->alert(_p('suggestion.completed_request'));

							if (isset($_SESSION['feed_added']))
							{
								if (Phpfox::isModule('socialpublishers'))
								{
									$this -> call('$("#result").click();');
									(($sPlugin = Phpfox_Plugin::get('suggestion.component_add_request')) ? eval($sPlugin) : false);
								}
								unset($_SESSION['feed_added']);
							}
							break;
					}
					break;

				case 'recommendation' :
					$iFriendId = $_SESSION['iFriendId'];
					//process send request to each user
					$aUserId = array_unique($aVals['to']);
					$aProcessedId = array();

					foreach ($aUserId as $iUserId)
					{
						if (Phpfox::getService('friend') -> isFriend(Phpfox::getUserId(), $iUserId))
						{
							continue;
						} else
						{
							//not friends each others
							Phpfox::getService('friend.request.process') -> add(Phpfox::getUserId(), $iUserId, 0, $sMessage);
							$aProcessedId[] = $iUserId;
						}
					}
					if (count($aProcessedId))
					{
						$aProcessedId = implode(",", $aProcessedId);
					}
					$this->alert(_p('suggestion.completed_request'));
					unset($_SESSION['sSuggestionType']);
					break;

				default :
					//add friends
					$aProcessed = array();

					if (count($aVals['to']) == 0)
						return;
					$aUserId = array_unique($aVals['to']);
					//process send request to each user
					foreach ($aUserId as $iUserId)
					{
						Phpfox::getService('suggestion.process') -> approve(phpfox::getUserId(), $iUserId, 1, "suggestion_friend");
						if (Phpfox::getService('friend') -> isFriend(Phpfox::getUserId(), $iUserId))
						{
							continue;
						} else
						{
							//not friends each others
							switch($sModule)
							{
								case 'suggestion_friend' :
									Phpfox::getService('friend.request.process') -> add(Phpfox::getUserId(), $iUserId, 0, $sMessage);
									break;
							}
						}
					}
					//flush all cache
					Phpfox::getService('suggestion.cache') -> removeAll(Phpfox::getUserId());

					$this->alert(_p('suggestion.completed_request'));

					break;
			}
		}

	}

	/*
	 * append link suggest to friends on each member profile
	 */
	public function appendSuggest()
	{
		if (Phpfox::getUserParam('suggestion.enable_friend_recommend') || Phpfox::getUserParam('suggestion.enable_friend_suggestion'))
		{
			if (Phpfox::getUserParam('suggestion.enable_friend_suggestion'))
				$sSuggestion = 'suggestion';
			else
				$sSuggestion = 'recommendation';

			$sUserId = $this -> get('sUserId') . '';
			if ($sUserId != '')
			{
				$aUserId = explode(",", $sUserId);

				$sLink = Phpfox::permalink(Phpfox::getUserBy('full_name'), null);
				$sTitle = _p('suggestion.will_get_friends_suggestion_from');
				$sLinkUser = '<a target="_blank" href="' . $sLink . '">' . Phpfox::getUserBy('full_name') . '</a>';
				$sTitle = preg_replace('/{{friend_name}}/', $sLinkUser, $sTitle);

				foreach ($aUserId as $iUserId)
				{
					$aUser = Phpfox::getService('user') -> getUser($iUserId, 'u.full_name, u.user_name');

					$sLink = '<a class="button btn btn-success btn-sm" style="margin-top:10px;font-size:11px;padding:5px;letter-spacing:0px;" href="' . Phpfox::permalink($aUser['user_name'], null) . '" >' . _p('suggestion.suggest_to_friends_2') . '</a>';
					$this -> call("$('#user_member_page_$iUserId').html('');");
					$this -> call("$('#user_member_page_$iUserId').html('$sLink');");
					$sMsg = '';
					$this -> append("#user_member_page_$iUserId", "<script language='javascript'>$('#user_member_page_$iUserId').click(function(e){e.preventDefault();suggestion_and_recommendation_tb_show('" . $sTitle . "',$.ajaxBox('suggestion.friends','iFriendId=" . $iUserId . "&sSuggestionType=" . $sSuggestion . "'))});</script>");
				}

			}
		}
	}

	/*
	 * update missing field when add new does not have a title;
	 */
	private function updateMissingField($sModule)
	{
		switch($sModule)
		{

			case 'suggestion_quiz' :
				if (isset($_SESSION['suggestion']['quiz']))
				{
					Phpfox::getService('suggestion.process') -> updateQuizLink($_SESSION['suggestion']['quiz']['quiz_id']);
					unset($_SESSION['suggestion']['quiz']);
				}
				break;
			case 'suggestion_photo' :
				if (isset($_SESSION['suggestion']['photo']))
				{
					$sTitle = $_SESSION['suggestion']['photo']['title'];
					$iItemId = $_SESSION['suggestion']['photo']['photo_id'];
					Phpfox::getService('suggestion.process') -> updateBy($iItemId, $sTitle, $sModule, 'title');
					unset($_SESSION['suggestion']['photo']);
				}
				break;
		}
	}

	public function loadFriendAjax()
	{
		$offset = (int)$this -> get('offset');
		$aRows = Phpfox::getService('suggestion') -> getSuggestionFriendList(Phpfox::getUserId(), $offset);

	}

	public function loadPeopleYouMayKnowAjax()
	{

		$type = 'people_you_may_knows';

		$offset = (int)$this -> get('offset');
		$offset += (int)Phpfox::getParam('suggestion.number_item_on_other_block');

		Phpfox::getBlock('suggestion.view-more', array('offset' => $offset, 'type' => $type));

		$this -> append('#people_you_may_knows', $this -> getContent(false));

		$total = Phpfox::getService('suggestion') -> getCountPeopleYouMayKnow();

		$this -> call("$('#offset_people_you_may_knows').val(" . $offset . "); $('#view_more_loader').hide();");

		if (($offset + Phpfox::getParam('suggestion.number_item_on_other_block')) < $total)
		{
			$this -> call("$('#people_you_may_know_view_more').show();");
		}

	}

	public function loadPagesYouMayLikeAjax()
	{

		$type = 'pages_you_may_like';

		$offset = (int)$this -> get('offset');
		$offset += (int)Phpfox::getParam('suggestion.number_item_on_other_block');

		Phpfox::getBlock('suggestion.view-more', array('offset' => $offset, 'type' => $type));

		$this -> append('#pages_you_may_like', $this -> getContent(false));

		$total = Phpfox::getService('suggestion') -> getCountPagesYouMayLike();

		$this -> call("$('#offset_pages_you_may_like').val(" . $offset . ");$('#view_more_loader').hide();");

		if (($offset + Phpfox::getParam('suggestion.number_item_on_other_block')) < $total)
		{
			$this -> call("$('#pages_you_may_like_view_more').show();");
		}

	}

	public function loadJobsYouMayLikeAjax()
	{

		$type = 'jobs_you_may_like';

		$offset = (int)$this -> get('offset');
		$offset += (int)Phpfox::getParam('suggestion.number_item_on_other_block');

		Phpfox::getBlock('suggestion.view-more', array('offset' => $offset, 'type' => $type));

		$this -> append('#jobs_you_may_like', $this -> getContent(false));

		$total = Phpfox::getService('suggestion') -> getCountJobsYouMayLike();

		$this -> call("$('#offset_jobs_you_may_like').val(" . $offset . ");$('#view_more_loader').hide();");

		if (($offset + Phpfox::getParam('suggestion.number_item_on_other_block')) < $total)
		{
			$this -> call("$('#jobs_you_may_like_view_more').show();");
		}

	}

	public function loadObjectViewMore()
	{
		$type = $this -> get('type');

		$id = 'iPage_' . $type;

		$iPage = (int)$this -> get($id);
		$iPage++;

		Phpfox::getBlock('suggestion.view-more', array('iPage' => $iPage, 'type' => $type));

		$total = Phpfox::getService('suggestion') -> getCountObject();

		$this -> append('#' . $type, $this -> getContent(false));

		$key = $this -> get('key');
		$this -> call("$('#iPage_" . $type . "').val('" . $iPage . "'); $('#view_more_loader').hide();");

		$temp = (int)($iPage * Phpfox::getParam('suggestion.number_item_on_other_block'));

		if ($temp < $total)
		{
			$this -> call("$('#suggestion_view_more_$type').show();");
		} else
		{
			if ($iPage < 2)
				$this -> append('#' . $type, "<div class='message'>" . _p('suggestion.no_new_suggestion_at_this_time') . " </div>");
		}

	}

	public function loadReminderViewMore()
	{
		$type = $this -> get('type');

		$id = 'iPage_' . $type;
		$iPage = (int)$this -> get($id);
		$iPage++;

		Phpfox::getBlock('suggestion.view-more', array('iPage' => $iPage, 'type' => $type));

		$temp = (int)($iPage * Phpfox::getParam('suggestion.number_item_on_other_block'));

		$total = Phpfox::getService('suggestion.reminder') -> countReminder(Phpfox::getUserId(), $type);

		$this -> append('#' . $type, $this -> getContent(false));

		$key = $this -> get('key');
		$this -> call("$('#iPage_" . $type . "').val('" . $iPage . "'); $('#view_more_loader').hide();");

		if ($temp < ($total - 1))
		{
			$this -> call("$('#suggestion_view_more_$type').show();");
		} else
		{
			$this -> append('#' . $type, "<div class='message'>" . _p('suggestion.no_new_suggestion_at_this_time') . " </div>");
		}

	}

}
?>