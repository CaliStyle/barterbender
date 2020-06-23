<?php

/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');

class Contactimporter_Service_Process extends Phpfox_Service
{

	public function deleteFailedInvitation($iUserId, $iInviteId)
	{
		$aResult = $this -> database() -> delete(Phpfox::getT('invite'), 'invite_id = ' . (int)$iInviteId);
		if ($aResult)
		{
            Phpfox::getService('contactimporter')->updateActivityPoint($iUserId, '-');
		}
	}

	/**
	 * delete inviation list
	 * @param array|int|string $aIds
	 * @return void
	 */
	public function deleteInvitation($aIds)
	{
		if (empty($aIds))
		{
			return array(
				0,
				0
			);
		}

		if (is_string($aIds))
		{
			$aIds = explode($glue = ',', trim($aIds));
		}

		$sDbCheck = '';

		foreach ($aIds as $sId)
		{
			$sId = trim($sId);
			$sDbCheck .= '\'' . $this -> database() -> escape($sId) . '\',';
		}

		$sDbCheck = rtrim($sDbCheck, ',');

		$aInvitations = $this -> database() -> select('*') -> from(Phpfox::getT('invite')) -> where('invite_id IN(' . $sDbCheck . ')') -> execute('getRows');

		$iSocials = 0;
		$iEmail = 0;
		if (!empty($aInvitations))
		{
			$sPatt = '/\(.*\)/';
			foreach ($aInvitations as $aInvitation)
			{
				$aResult = $this -> database() -> delete(Phpfox::getT('invite'), 'invite_id = ' . (int)$aInvitation['invite_id']);

				if ($aResult)
				{
					//$iPoint = (int) $this->getPointSetting($aInvitation['user_id']);
					$iUserId = (int)$aInvitation['user_id'];
					preg_match($sPatt, $aInvitation['email'], $arr);
					if (count($arr))
					{
						$iSocials++;
						phpfox::getService('contactimporter') -> updateStatistic($iUserId, 0, -1);
					}
					else
					{
						$iEmail++;
						phpfox::getService('contactimporter') -> updateStatistic($iUserId, -1, 0);
					}
				}
			}
		}
		return array(
			$iSocials,
			$iEmail
		);
	}

	public function addQueue($provider = '', $total = 0, $message = '', $friends_ids = '', $jsons = '')
	{
		$iUserId = Phpfox::getUserId() or Phpfox::getService('contactimporter') -> getInviteUserId();

		$session = '';

		if ($provider == 'facebook' or $provider == 'linkedin' or $provider == 'twitter')
		{
			$oProvider = Phpfox::getService('contactimporter') -> getProvider($provider);
			$aSession = $oProvider -> getApi();
			$session = serialize($aSession);
		}

		$bIsXmpp = 1;
		if($provider == 'facebook')
		{
			if(Phpfox::getService('contactimporter')->checkFacebookXmppPermission($provider))
			{
				$bIsXmpp = 1;
			}	
			else
			{
				$bIsXmpp = 0;
			}
		}
		$iQueueId = Phpfox::getLib('phpfox.database') -> insert(Phpfox::getT('contactimporter_queue'), array(
			'user_id' => $iUserId,
			'session' => $session,
			'provider' => $provider,
			'total' => $total,
			'message' => $message,
			'is_xmpp' => $bIsXmpp,
			'friend_ids' => $friends_ids,
			'jsons' => $jsons,
			'time_stamp' => PHPFOX_TIME
		));

		$aIds = unserialize($friends_ids);
		
		
		$this->insertFriendListIntoQueue($aIds, $provider, $iQueueId);
		
		return $iQueueId;
	}

	public function getPointSetting($iUserId, $sSetting = "points_contactimporter_sentinvitations")
	{
		if (!$iUserId)
		{
			return 0;
		}
		$aUser = Phpfox::getService('user') -> getUser($iUserId);

		$aSetting = Phpfox::getLib('database') -> select('*') -> from(Phpfox::getT('user_group_setting')) -> where('module_id="contactimporter" AND name="' . $sSetting . '"') -> execute('getRow');

		$aUserSetting = Phpfox::getLib('database') -> select("value_actual") -> from(Phpfox::getT('user_setting')) -> where('setting_id = ' . $aSetting['setting_id'] . ' AND user_group_id = ' . $aUser['user_group_id']) -> execute('getField');
		$iPoint = 1;
		if (!empty($aUserSetting))
		{
			$iPoint = (int)$aUserSetting;
		}
		else
		{
			switch ($aUser['user_group_id'])
			{
				case 1 :
					$iPoint = $aSetting['default_admin'];
					break;
				case 2 :
					$iPoint = $aSetting['default_user'];
					break;
				case 3 :
					$iPoint = $aSetting['default_guest'];
					break;
				case 4 :
					$iPoint = $aSetting['default_staff'];
					break;
				default :
					$aRow1 = Phpfox::getLib('database') -> select('*') -> from(Phpfox::getT('user_group_custom')) -> where('module_id="contactimporter" AND name="' . $sSetting . '"') -> execute('getRow');
					if (!empty($aRow1))
					{
						$iPoint = (int)$aRow1['default_value'];
					}
					break;
			}
		}
		return $iPoint;
	}
	public function sendPhpfoxMail($aVals)
	{
		$oFilter = Phpfox::getLib('parse.input');
		$aInsert = array(
			'parent_id' => 0,
			'subject' => $aVals['subject'],
			'preview' => $oFilter -> clean(strip_tags(Phpfox::getLib('parse.bbcode') -> cleanCode(str_replace(array(
				'&lt;',
				'&gt;'
			), array(
				'<',
				'>'
			), $aVals['message']))), 255),
			'owner_user_id' => 1, // Phpfox::getUserId(),
			'viewer_user_id' => $aVals['to'],
			'viewer_is_new' => 1,
			'time_stamp' => PHPFOX_TIME,
			'time_updated' => PHPFOX_TIME,
			'total_attachment' => 0,
		);
		$iId = $this -> database() -> insert(Phpfox::getT('mail'), $aInsert);

		$this -> database() -> insert(Phpfox::getT('mail_text'), array(
			'mail_id' => $iId,
			'text' => $oFilter -> clean($aVals['message']),
			'text_parsed' => $oFilter -> prepare($aVals['message'])
		));
	}
	
	/**
	 * 
	 * Fix issue:
	 * _ Load Balancing
	 * _ Improve Performance
	 */
	public function cronSendMail()
	{   
		// get the oldest entry in queue table
		$aRow = Phpfox::getService('contactimporter')->getQueueToSend();
		if(!$aRow)
		{
			return false;
		}

		$iQueueId = $aRow['queue_id'];
		$iUserId = $aRow['user_id'];
		$iTotal = $aRow['total'];
		$iSuccess = $aRow['success'];
		$iFails = $aRow['fail'];
		$sProvider = $aRow['provider'];
		$sMessage = $aRow['message'];

		//Update timestamp
		$this -> database() -> update(Phpfox::getT('contactimporter_queue'), array('time_stamp' => PHPFOX_TIME), "queue_id = " . $iQueueId);


		if ($iSuccess + $iFails >= $iTotal)
		{
			if ($iFails == 0)
			{
				$aUpdate['fail_ids'] = '';
			}
			$aUpdate['status'] = 'success';
			$this -> database() -> update(Phpfox::getT('contactimporter_queue'), $aUpdate, "queue_id = " . $iQueueId);
			return true;
		}

		//--------------------------------------------------------------------------------------------------------------------------------
		$aResult = array();
		$aResult[] = 'user_id: ' . $iUserId;
		
		//get list contacts needed to send 
		$aSendIds = Phpfox::getService('contactimporter')->getSendIdsFromQueueForProvider($aRow);
		
		// send invite by corresponding provider
		$aSendResult = $this->sendInvite($sProvider, $iUserId, $aSendIds, '', $sMessage, '', $iQueueId );
		
		$bIsShowResult = true;
		if($bIsShowResult)
		{
			echo "success: ".count($aSendResult['aSuccesseds']);
			echo "\nfailed: ".count($aSendResult['aFaileds']);

			if(count($aSendResult['aSuccesseds']) == 0 && count($aSendResult['aFaileds']) == 0)
			{
			}
			else
			{
				//Update timestamp
				$this -> database() -> update(Phpfox::getT('contactimporter_queue'), array('time_stamp_realsent' => PHPFOX_TIME), "queue_id = " . $iQueueId);
			}
		}

		Phpfox::getService('contactimporter.process')->updateQueueByResult($aSendResult, $iQueueId, $sProvider);

		$iSuccess = Phpfox::getService('contactimporter')->getTotalSuccessedInvitationOfQueue($iQueueId);
		$iFails =  Phpfox::getService('contactimporter')->getTotalFailedInvitationOfQueue($iQueueId);

		$iSents = count($aSendResult['aSuccesseds']);

		$iTotalSent = count($aSendResult['aSuccesseds']) + count($aSendResult['aFaileds']);
		
		// ----------------------

		$aUpdate = array(
			'success' => $iSuccess,
			'fail' => $iFails
		);

		if ($iSuccess + $iFails >= $iTotal)
		{
			$aUpdate['status'] = 'success';
		}
		else
		{
			$aUpdate['server_id'] = null;
		}

		$this -> database() -> update(Phpfox::getT('contactimporter_queue'), $aUpdate, "queue_id = " . $iQueueId);
		
		return true;	
		
	}

	public function sendXmppReminderEmail($aRow)
	{
		$oMail = Phpfox::getLib('mail');

		$aUser = Phpfox::getService('user') -> getUser($aRow['user_id']);
		$sSubject = _p('remind_xmpp_subject');
		$core_path = phpfox::getParam('core.path');
		$sCallback = urlencode(Phpfox::getLib('url') -> makeUrl('contactimporter.facebook'));
		$sLink = "{$core_path}/module/socialbridge/static/php/facebook.php?callbackUrl={$sCallback}&bredirect=1";
		$sBody = _p('xmpp_reminder_body', array(
				'link' => $sLink,
				'site_name' => Phpfox::getParam('core.site_title')
			));
		$oMail -> to($aUser['email']) -> subject($sSubject) -> message($sBody) -> send();
	}

	/**
	 * this is preserver method to send general messeage.
	 */
	public function sendMessage($sProvider, $iUserId, $aRecipients, $sSubject, $sMessage, $sLink = NULL)
	{
		$oContactImporter = Phpfox::getService('contactimporter');
		$oProvider = $oContactImporter -> getProvider($sProvider);
		$pluginType = $oContactImporter -> getPluginType($sProvider);

		switch ($pluginType)
		{
			case 'social' :
			case 'email' :
			case 'openiviter' :
			default :
				// does not support
				break;
		}
	}

	public function setCurrentApi($sProvider, $aQueue)
	{
		if($sProvider == 'facebook' || $sProvider == 'twitter')
		{
			$oProvider = Phpfox::getService('contactimporter')->getProvider($sProvider);
			$oSession = unserialize($aQueue['session']);
			//backward compatible with 3.03p2
			if(is_array($oSession))
			{
				if($sProvider == 'facebook')
				{
					$oApi = $oProvider->getApi();
					$aSetting = Phpfox::getService('socialbridge')->getSetting($sProvider);

					if (is_array($oSession) && isset($oSession['access_token']))
					{
						$oApi->setAccessToken($oSession['access_token']);
					}
					else
					{
						$oApi->setAccessToken($oSession);
					}


				}
				else if($sProvider == 'twitter')
				{
					$oApi = $oProvider->getApi();
					$oApi->setOAuthToken($oSession['oauth_token']);
		            $oApi->setOAuthTokenSecret($oSession['oauth_token_secret']);
				}
			}
			else
			{
				$oProvider->setApi($oSession);
			}

			
		}	
	}

	public function insertFailedInvitationIntoQueue($aFaileds, $sProvider)
	{
		foreach($aFaileds as $aFailed)
		{
			$aInsert = array(
					'is_sent' => 1,
					'time_stamp' => PHPFOX_TIME,
					'is_failed' => 1,
					'queue_id' =>0,
					'provider' => $sProvider,
					'friend_id' => $aFailed['friend_id'],
					'error_message' => $aFailed['message'],
				);

			$this->addAccessTokenIntoArray($aInsert, $sProvider);
			$this->addUIDIntoArray($aInsert, $sProvider);

			$this->database()->insert(Phpfox::getT('contactimporter_invitation_queue_list'), $aInsert);
		}
	}

	public function addAccessTokenIntoArray(&$aInsert, $sProvider)
	{
		if($sProvider == 'facebook')
		{
			$oProvider = Phpfox::getService('contactimporter')->getProvider($sProvider);
			$oApi = $oProvider->getApi();
			$sAccessToken = $oApi->getAccessToken();
		}
		else if($sProvider == 'twitter')
		{

			$oProvider = Phpfox::getService('contactimporter')->getProvider($sProvider);
			$oApi = $oProvider->getApi();
			$sAccessToken = $oApi->getAccessToken();
		}
		else if($sProvider == 'linkedin')
		{
			$oProvider = Phpfox::getService('contactimporter')->getProvider($sProvider);
			$oApi = $oProvider->getApi();
			$aAccessToken = $oApi->getTokenAccess();
			/**
			 * array(4) { ["oauth_token"]=> string(36) "411629ce-6e16-4380-93e4-df34d7b955ec" ["oauth_token_secret"]=> string(36) "ecbb2d0a-1612-4ab0-a7b9-7f9cf0e519f6" ["oauth_expires_in"]=> string(7) "5183998" ["oauth_authorization_expires_in"]=> string(7) "5183998" } 
			 * 
			 */
			$sAccessToken = $aAccessToken['oauth_token'];
		}
		else
		{
			$sAccessToken = '0';
		}

		$aInsert['access_token'] = $sAccessToken;
	}
	public function addUIDIntoArray(&$aInsert, $sProvider)
	{
		$oProvider = Phpfox::getService('contactimporter')->getProvider($sProvider);
		$sUid = 0;
		if($sProvider == 'facebook')
		{
			$aProfile = $oProvider->getProfile('me',true);
			$sUid = $aProfile['identity'];
		}
		else if($sProvider == 'linkedin' || $sProvider == 'twitter')
		{
			$aProfile = $oProvider->getProfile();
			$sUid = $aProfile['identity'];
		}
		$aInsert['uid'] = $sUid;
	}
	
	public function insertSuccessedInvitationIntoQueue($aSuccesseds, $sProvider)
	{

		foreach($aSuccesseds as $aSuccessed)
		{
			$aInsert = array(
					'is_sent' => 1,
					'time_stamp' => PHPFOX_TIME,
					'is_failed' => 0,
					'queue_id' =>0,
					'provider' => $sProvider,
					'friend_id' => $aSuccessed['friend_id'],
				);
			$this->addAccessTokenIntoArray($aInsert, $sProvider);

			$this->addUIDIntoArray($aInsert, $sProvider);
			
			$this->database()->insert(Phpfox::getT('contactimporter_invitation_queue_list'), $aInsert);
		}
	}


	public function updateQueueByResult($aSendResult, $iQueueId = 0, $sProvider = '')
	{
		$aFaileds = $aSendResult['aFaileds'];
		$aSuccesseds = $aSendResult['aSuccesseds'];

		foreach($aFaileds as $aFailed)
		{
			$aUpdate = array(
				'is_sent' => 1,
				'time_stamp' => PHPFOX_TIME,
				'is_failed' => 1,
				'error_message' => $aFailed['message']
			);

			$this->addAccessTokenIntoArray($aUpdate, $sProvider);
			$this->addUIDIntoArray($aUpdate, $sProvider);

			$this->database()->update(Phpfox::getT('contactimporter_invitation_queue_list'), $aUpdate, 'queue_id = ' . $iQueueId . ' AND friend_id like \'' . $aFailed['friend_id'] . '\'');
		}


		foreach($aSuccesseds as $aSuccessed)
		{
			$aUpdate = array(
				'is_sent' => 1,
				'time_stamp' => PHPFOX_TIME
			);

			$this->addAccessTokenIntoArray($aUpdate, $sProvider);
			$this->addUIDIntoArray($aUpdate, $sProvider);

			$this->database()->update(Phpfox::getT('contactimporter_invitation_queue_list'), $aUpdate, 'queue_id = ' . $iQueueId . ' AND friend_id like \'' . $aSuccessed['friend_id'] . '\'');
		}	
	}
	
	public function sendInviteInQueue()
	{
		// get the oldest entry in queue table
		$aRow = Phpfox::getService('contactimporter')->getQueueToSend();
			
		if(!$aRow)
		{
			return false;
		}

		$iQueueId = $aRow['queue_id'];
		$iUserId = $aRow['user_id'];
		$iTotal = $aRow['total'];
		$iSuccess = $aRow['success'];
		$iFails = $aRow['fail'];
		$sProvider = $aRow['provider'];
		$sMessage = $aRow['message'];

		//Update timestamp
		$this -> database() -> update(Phpfox::getT('contactimporter_queue'), array('time_stamp' => PHPFOX_TIME), "queue_id = " . $iQueueId);


		if ($iSuccess + $iFails >= $iTotal)
		{
			if ($iFails == 0)
			{
				$aUpdate['fail_ids'] = '';
			}
			$aUpdate['status'] = 'success';
			$this -> database() -> update(Phpfox::getT('contactimporter_queue'), $aUpdate, "queue_id = " . $iQueueId);
			return true;
		}

		// we use singleton to change the state of provider, it means this provider will use object stored in database 
		//which has a valid access token
		// note it is tricky and dangerous
		
		Phpfox::getService('contactimporter.process')->setCurrentApi($sProvider, $aRow);

		// check to migrate in to new facebook api

		if($sProvider == 'facebook' && !$aRow['is_xmpp'])
		{
			// if new permission has not been granted
			if(!Phpfox::getService('contactimporter')->checkFacebookXmppPermission($sProvider))
			{
				echo 'need xmpp permission for this queue';
				return false;
			}
			else
			{
				//update xmpp field
				$this -> database() -> update(Phpfox::getT('contactimporter_queue'), array('is_xmpp' => 1), "queue_id = " . $iQueueId);
			}
		}

		
		//--------------------------------------------------------------------------------------------------------------------------------
		$aResult = array();
		$aResult[] = 'user_id: ' . $iUserId;
		
		//get list contacts needed to send 
		$aSendIds = Phpfox::getService('contactimporter')->getSendIdsFromQueueForProvider($aRow);
		
		// send invite by corresponding provider
		$aSendResult = $this->sendInvite($sProvider, $iUserId, $aSendIds, '', $sMessage, '', $iQueueId );

		$bIsShowResult = true;
		if($bIsShowResult)
		{
			echo "success: ".count($aSendResult['aSuccesseds']);
			echo "\nfailed: ".count($aSendResult['aFaileds']);

			if(count($aSendResult['aSuccesseds']) == 0 && count($aSendResult['aFaileds']) == 0)
			{
			}
			else
			{
				//Update timestamp
				$this -> database() -> update(Phpfox::getT('contactimporter_queue'), array('time_stamp_realsent' => PHPFOX_TIME), "queue_id = " . $iQueueId);
			}
		}

		Phpfox::getService('contactimporter.process')->updateQueueByResult($aSendResult, $iQueueId, $sProvider);

		$iSuccess = Phpfox::getService('contactimporter')->getTotalSuccessedInvitationOfQueue($iQueueId);
		$iFails =  Phpfox::getService('contactimporter')->getTotalFailedInvitationOfQueue($iQueueId);

		$iSents = count($aSendResult['aSuccesseds']);

		$iTotalSent = count($aSendResult['aSuccesseds']) + count($aSendResult['aFaileds']);
		
		// ----------------------

		$aUpdate = array(
			'success' => $iSuccess,
			'fail' => $iFails
		);

		if ($iSuccess + $iFails >= $iTotal)
		{
			$aUpdate['status'] = 'success';
		}
		else
		{
			$aUpdate['server_id'] = null;
		}

		$this -> database() -> update(Phpfox::getT('contactimporter_queue'), $aUpdate, "queue_id = " . $iQueueId);
		
		if(isset($aSendResult['aSuccesseds']) && count($aSendResult['aSuccesseds'])>0)
		{
			$numbers = (int)$aRow['success'] + (int)count($aSendResult['aSuccesseds']);
			// send notify to inviter
			$insertCron = array(
				'user_id' => $aRow['user_id'],
				'provider' => $aRow['provider'],
				'time_stamp' => PHPFOX_TIME,
				'numbers' => $numbers.'/'.$aRow['total']
			);
			$notify_id = $this->database()->insert(Phpfox::getT('contactimporter_cron_notify'), $insertCron);
			
			$aInsertNotify = array(
				'type_id' => 'contactimporter_notice_cron',
				'item_id' => $notify_id,
				'user_id' => $aRow['user_id'],	
				'owner_user_id' => $aRow['user_id'],
				'time_stamp' => PHPFOX_TIME		
			);	
			
			$this->database()->insert(Phpfox::getT('notification'), $aInsertNotify);
		}
		
		return true;

	}

    /**
     * @param $sProvider
     * @param $iUserId
     * @param $aRecipients
     * @param $sSubject
     * @param $sMessage
     * @param null $sLink
     * @param int $iQueueId
     * @return array
     */
	public function sendInvite($sProvider, $iUserId, $aRecipients, $sSubject, $sMessage, $sLink = NULL, $iQueueId = 0)
	{
		$iSendCounter = 0;
		$aFaileds = array();
		$aSuccesseds = array();

		$iUserId = intval($iUserId);

		$oContactImporter = Phpfox::getService('contactimporter');

		if ($iUserId == 0)
		{
			$iUserId = $oContactImporter -> getInviteUserId();
		}

		$oProvider = $oContactImporter -> getProvider($sProvider);
		$pluginType = $oContactImporter -> getPluginType($sProvider);
		$sInfoPattern = $sCriteriaPattern = '';

		if ($iUserId)
		{
			$iPoint = (int)$this -> getPointSetting($iUserId);
		}
		switch($pluginType)
		{
			case 'social' :
			case 'openinviter' :
				$sInfoPattern = "{$sProvider} (:sRecipient)";
				$sCriteriaPattern = "email like '%{$sProvider} (:sRecipient)%' and user_id= '{$iUserId}'";
				break;
			case 'email' :
			default :
				$sInfoPattern = ':sRecipient';
				$sCriteriaPattern = "email = '%s' and user_id= '{$iUserId}'";
		}

		foreach ($aRecipients as $sRecipient)
		{
			// send from member
			if ($iUserId)
			{
				$sCriteria = strtr($sCriteriaPattern, array(
					':sRecipient' => $sRecipient,
					':sProvider' => $sProvider
				));

				$aInvite = $this -> database() -> select('invite_id') -> from(Phpfox::getT('invite')) -> where($sCriteria) -> execute('getSlaveRow');
				if ($aInvite)
				{
					$iInviteId = $aInvite['invite_id'];
				}
				else
				{
					$sInfo = strtr($sInfoPattern, array(
						':sRecipient' => $sRecipient,
						':sProvider' => $sProvider
					));
					$iInviteId = Phpfox::getService('invite.process') -> addInvite($sInfo, $iUserId);
					if($sProvider == 'facebook')	
					{
						$aProfile = Phpfox::getService('socialbridge.provider.facebook')->getProfile($sRecipient, $bIsGetNew = true); 
						if($aProfile)
						{
							$this->database()->update(Phpfox::getT('invite'), array('invited_name' => $aProfile['full_name']), 'invite_id = ' . $iInviteId);
						}
					}
					
				}
				$sLink = Phpfox::getLib('url') -> makeUrl('invite', array('id' => $iInviteId));
			}
			else
			{
				// send invite from guests
				$sLink = Phpfox::getLib('url') -> makeUrl('invite', array('id' => 0));
			}
			
			$result = $oProvider -> sendInvitation($iUserId, $sRecipient, $sSubject = NULL, $sMessage, $sLink);
			
			if(isset($result['result']))
			{
				$aTemp = array(
					'friend_id' => $sRecipient,
					'message' => serialize($result['message'])
				);
				if($result['result'])
				{
					// successfully send an invitation
					$aSuccesseds[] = $aTemp;
				}
				else
				{
					$aFaileds[] = $aTemp;
				}
			}
		
			$iSendCounter += 1;
		}

		if ($iUserId)
		{
			Phpfox::getService('contactimporter') -> updateActivityPoint($iUserId, '+', $iSendCounter);

			$iSocial = $iSendCounter * ($pluginType != 'email' ? 1 : 0);
			$iEmail = $iSendCounter * ($pluginType != 'email' ? 0 : 1);
			$this -> updateStatistic($iUserId, $iEmail, $iSocial, $sProvider);
		}

		if ($iUserId && $iSendCounter)
		{
			$oContactImporter -> setUserHasInvited($iUserId, 1);
		}

		(($sPlugin = Phpfox_Plugin::get('contactimporter.service_process_send_invite_end')) ? eval($sPlugin) : false);

		return array(
			'aFaileds' => $aFaileds,
			'aSuccesseds' => $aSuccesseds
		);
	}

	public function updateStatistic($iUserId, $iEmails, $iSocials, $sProvider = '')
	{
		$aRow = Phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('contactimporter_statistics')) -> where('user_id = ' . $iUserId) -> execute('getRow');
		if ($aRow != null)
		{
			$email = $aRow['emails'] + $iEmails;
			if ($email < 0)
				$email = 0;

			$social = $aRow['socials'] + $iSocials;

			if ($social < 0)
			{
				$social = 0;
			}
			Phpfox::getLib('phpfox.database') -> update(Phpfox::getT('contactimporter_statistics'), array(
				'emails' => $email,
				'socials' => $social
			), 'statictis_id = ' . $aRow['statictis_id']);
		}
		else
		{
			Phpfox::getLib('phpfox.database') -> insert(Phpfox::getT('contactimporter_statistics'), array(
				'user_id' => $iUserId,
				'emails' => $iEmails,
				'socials' => $iSocials
			));
		}

		if ($sProvider)
		{
			$iTotal = $iEmails;
			if ($iSocials > 0)
			{
				$iTotal = $iSocials;
			}
			if ($iTotal < 0)
				$iTotal = 0;
			$aRow = Phpfox::getLib('phpfox.database') -> select('*') -> from(Phpfox::getT('contactimporter_contact')) -> where('provider = "' . $sProvider . '" AND user_id = ' . $iUserId) -> execute('getRow');
			if ($aRow != null)
			{
				Phpfox::getLib('phpfox.database') -> update(Phpfox::getT('contactimporter_contact'), array('total' => $aRow['total'] + $iTotal), 'contact_id = ' . $aRow['contact_id']);
			}
			else
			{
				Phpfox::getLib('phpfox.database') -> insert(Phpfox::getT('contactimporter_contact'), array(
					'user_id' => $iUserId,
					'provider' => $sProvider,
					'total' => $iTotal
				));
			}
			
			//Insert admin statistics
			Phpfox::getLib('phpfox.database') -> insert(Phpfox::getT('contactimporter_admin_statistics'), array(
				'user_id' => $iUserId,
				'provider' => $sProvider,
				'date' => date('Y-m-d'),
				'total' => $iTotal
			));
		}
	}

	
	public function insertFriendListIntoQueue($aFriends, $sProvider, $iQueueId)
	{
		
		$aField = array(
			'queue_id',
			'provider',
			'is_sent',
			'friend_id',
            'time_stamp'
		);

		$aInserts = array();
		foreach ($aFriends as $sFriendId) {
			$aInsert = array(
				$iQueueId,
				$sProvider,
				0,
				$sFriendId,
                PHPFOX_TIME
			);
			$aInserts[] = $aInsert;
		}

		Phpfox::getLib('database')->multiInsert(Phpfox::getT('contactimporter_invitation_queue_list'), $aField, $aInserts);
	}

	public function updateQueueData($iQueueId)
	{
		$aQueue = Phpfox::getService('contactimporter')->getQueueById($iQueueId);
		if(!$aQueue)
		{
			return false;
		}
		
		$sProvider = $aQueue['provider'];

		// should be refactored to use polimorphism here
		switch ($sProvider) {
			case 'facebook':
					Phpfox::getService('contactimporter.process')->updateFacebookFriends($iQueueId);	
				break;
			case 'twitter':
					Phpfox::getService('contactimporter.process')->updateTwitterFriends($iQueueId);	
				break;
			case 'linkedin':
					Phpfox::getService('contactimporter.process')->updateLinkedinFriends($iQueueId);	
				break;
			case 'yahoo':
			case 'gmail':
			case 'hotmail':
				break;

		}
	}


	public function updateTwitterFriends($iQueueId)
	{
		$aFriends = Phpfox::getService('contactimporter.process')->getTwitterFriends($iQueueId);	
		Phpfox::getService('contactimporter.process')->insertFriendListIntoQueue($aFriends, 'twitter', $iQueueId);
		
	}

	public function updateFacebookFriends($iQueueId)
	{
		$aFriends = Phpfox::getService('contactimporter.process')->getFacebookFriends($iQueueId);	

		Phpfox::getService('contactimporter.process')->insertFriendListIntoQueue($aFriends, 'facebook', $iQueueId);
	}

	public function updateLinkedinFriends($iQueueId)
	{
		$aFriends = Phpfox::getService('contactimporter.process')->getLinkedinFriends($iQueueId);	

		Phpfox::getService('contactimporter.process')->insertFriendListIntoQueue($aFriends, 'linkedin', $iQueueId);
	}

	public function getLinkedinFriends($iQueueId)
	{
		$aQueue = Phpfox::getService('contactimporter')->getQueueById($iQueueId);
		
		$oProvider = Phpfox::getService('contactimporter')->getProvider($aQueue['provider']);
		$oProvider->setApi(unserialize($aQueue['session']));
		$oLinkedIn = $oProvider->getApi();
		$response = $oLinkedIn -> connections();

		$aFriendIds = array();

		if ($response['success'] == FALSE)
		{
			return $aFriendIds;
		}

		$data = json_decode(json_encode(simplexml_load_string($response['linkedin'])), 1);

		if ($data['@attributes']['total'] <= 0)
		{
			return $aFriendIds;
		}
		else
		{
			foreach ($data['person'] as $item)
			{
				if($item['id'])
				{
					$aFriendIds[] = $item['id'];
				}
			}
		}

		return $aFriendIds;
	}

	public function getFacebookFriends($iQueueId)
	{
		$aQueue = Phpfox::getService('contactimporter')->getQueueById($iQueueId);

//		$oFacebook = unserialize($aQueue['session']);
		Phpfox::getService('contactimporter.process')->setCurrentApi($aQueue['provider'], $aQueue);	
		$oProvider = Phpfox::getService('contactimporter')->getProvider($aQueue['provider']);
		$oFacebook = $oProvider->getApi();

		$sToken = $oFacebook->getAccessToken();

		try{
			$aFriends = $oFacebook->api(array(
			'method' => 'fql.query',
			'query' => "SELECT uid2 FROM friend WHERE uid1 = me()",
			'access_token' => $sToken
				));
			}
		catch (exception $e)
		{
			echo($e->getMessage());
		}
		

		$aFriendIds = array();
		foreach ($aFriends as $aFriend) {
			$aFriendIds[] = $aFriend['uid2'];
		}

		return $aFriendIds;
	}

	public function getTwitterFriends($iQueueId)
	{
		$aQueue = Phpfox::getService('contactimporter')->getQueueById($iQueueId);
		require_once (PHPFOX_DIR . 'module' . PHPFOX_DS . 'socialbridge' . PHPFOX_DS . 'include' . PHPFOX_DS . 'service' . PHPFOX_DS . 'libs' . PHPFOX_DS . 'twitter.php');

		Phpfox::getService('contactimporter.process')->setCurrentApi($aQueue['provider'], $aQueue);	

		//later we retrieve access token from social bridge
		$oProvider = Phpfox::getService('contactimporter')->getProvider($aQueue['provider']);
		$oTwitter = $oProvider->getApi();

		if (!$oTwitter) {
			return false;
		}
		$me = $oTwitter->accountVerifyCredentials();
		list($sNext, $aFriendIds) = $oTwitter->followersIds(null, $me['id']);
		$this->database()->update(Phpfox::getT('contactimporter_queue'), array('next' => $sNext), "queue_id = " . $iQueueId);

		return $aFriendIds;
	}
	
	public function removeRemainingInvitations($iUserId = 0){
		if(!$iUserId)
			return;
		$aRows = Phpfox::getLib('phpfox.database') -> select('queue_id, user_id,total,success,fail') -> from(Phpfox::getT('contactimporter_queue')) -> where('total > 0 AND user_id =' . Phpfox::getUserId()) -> execute('getRows');
		
		foreach($aRows as $key=>$aRow){
			$current_sent = $aRow['success'] + $aRow['fail'];
			Phpfox::getLib('phpfox.database')->update(PHpfox::getT('contactimporter_queue'),array(
				'total' => $current_sent
			),'queue_id = '.$aRow['queue_id']);	
			Phpfox::getLib('phpfox.database')->delete(Phpfox::GetT('contactimporter_invitation_queue_list'),'queue_id = '.$aRow['queue_id'].' and is_sent = 0 and is_failed = 0');
		}
	}
	
	/*
	 * Delete queue 
	 * aQueueId is array iQueueId
	 */
	
	public function deleteQueue($aQueueId)
	{
		foreach ($aQueueId as $iQueueId)
		{
			$IqlItem = $this -> database() -> select('*') -> from(Phpfox::getT('contactimporter_invitation_queue_list'), 'iql') 
			-> where("iql.id = ".$iQueueId) 
			-> execute('getSlaveRow');
			
			//delete value in contactimporter_invitation_queue_list
			$aResult = $this -> database() -> delete(Phpfox::getT('contactimporter_invitation_queue_list'), 'id = ' . (int)$iQueueId);
			if($aResult)
			{
				//uppdate value in contactimporter_queue
				phpfox::getService('contactimporter') -> updateQueue(array('iQueueId' => $IqlItem['queue_id'], 'friendId' => $IqlItem['friend_id'], ));
			}
		}
	}	

/*
	 * $params is array
	 * $aResendInviteId
	 */
	
	public function reSendAllInvitation($params = array())
	{
		$status = 0;
		$message = _p('default_invite_message_text');
		foreach($params['aResendInviteId'] as $iInvite)
		{
			
			$sMail = Phpfox::getLib('phpfox.database') -> select('email') -> from(Phpfox::getT('invite'), 'invite') -> where('invite.invite_id = ' . $iInvite . ' AND is_resend = 0') -> execute('getSlaveRow');
	
			if (empty($sMail))
			{
				continue;
			}
			else
			{
				if (Phpfox::getLib('mail') -> checkEmail($sMail))
				{
					$sLink = Phpfox::getLib('url') -> makeUrl('invite', array('id' => $iInvite));
					$subcribe_message = "";
					$is_unsubcribe = phpfox::getLib('database') -> select('param_values') -> from(phpfox::getT('contactimporter_settings')) -> where('settings_type="is_unsubcribed"') -> execute('getRow');
					if (isset($is_unsubcribe['param_values']) && $is_unsubcribe['param_values'])
					{
						$encoded = PREG_REPLACE("'(.)'e", "dechex(ord('\\1'))", $sMail['email']);
						$whyyouwereinvited = phpfox::getLib('url') -> makeURL('contactimporter/whyyouwereinvited');
						$whyyouwereinvited .= '?id=' . $encoded;
						$blockall = Phpfox::getLib('url') -> makeURL('contactimporter/blockallfurtheremailmessages');
						$blockall .= '?id=' . $encoded;
						$subcribe_message = '<a target="_blank" href="' . $whyyouwereinvited . '">' . _p('find_out_why_you_were_invited_by_clicking_here') . '</a><br /><a target="_blank" href="' . $blockall . '">' . _p('block_all_further_email_mesages') . '</a>';
					}
					$unsubscribeEmail = phpfox::getService('contactimporter') -> getEmailUnsubscribe();
					if (!in_array($sMail['email'], $unsubscribeEmail))
					{
						$status = 1;
						
						$bSent = Phpfox::getLib('mail') -> to($sMail['email'])
					
						-> subject(array(
							'invite.full_name_invites_you_to_site_title',
							array(
								'full_name' => Phpfox::getUserBy('full_name'),
								'site_title' => Phpfox::getParam('core.site_title')
							)
						)) -> message(array(
							'invite.full_name_invites_you_to_site_title_link',
							array(
								'full_name' => Phpfox::getUserBy('full_name'),
								'site_title' => Phpfox::getParam('core.site_title') . '<br/>' . $message . '<br/>' . $subcribe_message,
								'link' => $sLink
							)
						)) -> send();
	
						if ($bSent)
						{
							//Phpfox::getLib('database') -> update(Phpfox::getT('invite'), array('is_resend' => 1), 'invite_id=' . $iInvite);
						}
					}
				}
			}
		}
		return $status; 
	}
}
?>