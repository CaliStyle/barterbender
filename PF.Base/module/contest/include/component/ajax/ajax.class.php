<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Ajax_Ajax extends Phpfox_Ajax
{
	/**
	 * to keep track of adding entries in contest
	 */
	public function setContestSession ()
	{
		$iType = $this->get('contest_type');
		$iContestId = $this->get('contest_id');
		Phpfox::getService('contest.helper')->setSessionBeforeAddItemFromSubmitForm($iContestId, $iType);
		//$this->call("window.location.href = $('#yncontest_create_new_item a').attr('href');");
	}

	public function submitEntry ()
	{
		$sSummary = $this->get('summary');
		$sTitle = $this->get('title');
		$iItemId = $this->get('item_id');
		$iItemType = $this->get('item_type');
		$iContestId = $this->get('contest_id');
		$bIsSubmit = $this->get('is_submit');
        $iSourceId = $this->get('source_id');
		$aEntryParam = array(
			'sSummary' => $sSummary,
			'sTitle' => $sTitle,
			'iItemId' => $iItemId,
			'iItemType' => $iItemType,
			'iContestId' => $iContestId,
            'iSourceId' => $iSourceId
			);

		if($bIsSubmit)
		{
			$iEntryId = Phpfox::getService('contest.entry.process')->add($sTitle, $sSummary, $iItemId, $iItemType, $iContestId,$iSourceId);

			if($iEntryId)
			{
				$this->alert(_p('contest.thanks_for_submitting'), null, 300, 150, true);
                $sContestUrl = Phpfox::getService('contest.contest')->getContestUrl($iContestId);
				$sEntryUrl = $sContestUrl.'entry_'.$iEntryId.'/';
				$this->call("setTimeout(function() { window.location.href ='".$sEntryUrl."';}, 1000)");
			}
		}
		else
		{
			Phpfox::getBlock('contest.entry.preview', array('aEntryParam' => $aEntryParam));
		}
	}

	public function payForPublishContest()
	{
		
	}

	public function addVote()
    {
		$entry_id = $this->get('entry_id');
		$is_voted = $this->get('is_voted');
		$user_id = Phpfox::getUserId();
        
        $is_success = false;
        if ($is_voted)
        {
            if (Phpfox::getService("contest.entry.process")->deleteVote($user_id, $entry_id))
            {
                $is_success = true;
                $this->alert(_p('contest.you_un_vote_successfully'));
            }
        }
        else
        {
            if (Phpfox::getService("contest.entry.process")->addVote($user_id, $entry_id))
            {
                $is_success = true;
                $this->alert(_p('contest.you_vote_successfully'));
            }
        }
        
        if ($is_success)
        {
            $this->call("setTimeout(function() { window.location.href = window.location.href;}, 1000)");
        }
        else
        {
            $this->alert('An error occurred. Please try again.');
        }
	}

    public function fillEmailTemplate ()
    {
    	$iTypeId = $this->get('type_id');

    	if(empty($iTypeId))
    		$iTypeId = 0;

    	$aEmail = Phpfox::getService('contest.mail')->getEmailTemplateByTypeId($iTypeId);
        
        $aEmail['email_subject'] = Phpfox::getLib('parse.output')->parse($aEmail['subject']);
        $aEmail['content'] = str_replace('"', '\"', $aEmail['content']);
        $aEmail['content'] = preg_replace('/[\r]+/', '', $aEmail['content']);
        
        //$aEmail['content'] = Phpfox::getLib('phpfox.parse.output')->parse($aEmail['content']);
    	//$aEmail['content'] = str_replace('"', '\"', $aEmail['content']);
    	//$aEmail['content'] = str_replace("\r", "", $aEmail['content']);
    	// echo htmlentities($aEmail['content']);
        // exit;
    	$this->call('$("#subject").val("' . $aEmail['subject'] . '"); $("#content").val("' . $aEmail['content'] . '")');
    }

    public function showPayPopup()
    {
    	$iContestId = $this->get('contest_id');
    	Phpfox::getBlock('contest.pay', array(
    		'contest_id' => $iContestId
    		) );
    }

    public function addNewContest()
    {

    	if($aVals = $this->get('val'))
    	{

    		if($iId = Phpfox::getService('contest.contest.process')->addOrUpdate($aVals)) { //Add new contest
			//resend to this controller and set tab to photos
    			$this->call("yncontest.addContest.showPayPopup($iId);");
    		}
    		else
    		{
    			$aError = Phpfox_error::get();
    			$sHtml = Phpfox::getService('contest.helper')->generateErrorHtmlFromArrayOfMessage($aError);

    			$this->alert($sHtml,null, 300, 150, $bClose = false);
    		}
    	}
    	
    }

    public function processPayForPublishContest()
    {
    	$aVals = $this->get('val');
    	$iContestId = $this->get('contest_id');

    	$sUrl = '';

    	$aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);
    	$aFees = Phpfox::getService('contest.contest')->getAllFees();

		//if contest is not publish and fee for publish is <=0 then we publish it
    	if(!$aContest['is_published'] && 
    		($aFees['publish'] <= 0 ||
				//denied contest is published so we don't need to charge it again
    			$aContest['contest_status'] == Phpfox::getService('contest.constant')->getContestStatusIdByStatusName('denied')
    			))
    	{
    		$bResult = Phpfox::getService('contest.contest.process')->publishContest($iContestId);

    		if($bResult)
    		{
    			$this->alert(_p('contest.contest_successfully_published'),_p('Notice'),300,100,true);

    			$sUrl = Phpfox::getService('contest.contest')->getContestUrl($iContestId);;

    		}

    	}

		// if there is any paypal check out, we navigate to paypal
    	$aResult = Phpfox::getService('contest.contest.process')->payForPublishContest($aVals, $iContestId);  

    	if($aResult['result'])
    	{
    		$sUrl = $aResult['checkout_url'];
    	}
    	else
    	{
    		if(isset($aResult['message']))
    		{
    			$this->alert('<div class="error_message">' . $aResult['message'] .'</div>');
    		}
    		exit;
    	}	



    	$this->call('setTimeout(function() { location.href = \'' . $sUrl . '\';}, 1500);');


    }

    public function feature()
    {
    	$iContestId = $this->get('contest_id');
    	if(!Phpfox::getService('contest.permission')->canFeatureContest($iContestId))
    	{
    		$this->alert(_p('contest.you_can_not_perform_this_action'),_p('Notice'),300,100,true);
    		exit;
    	}

    	$iType = $this->get('type');

    	$bIsAdmin = $this->get('admin');

    	if (Phpfox::getService('contest.contest.process')->featureContest($iContestId, $iType))
    	{
    		if($iType == 1)
    		{
    			$this->alert(_p('contest.contest_successfully_featured'),_p('Notice'),300,100,true);
    		}
    		else
    		{
    			$this->alert(_p('contest.contest_successfully_un_featured'),_p('Notice'),300,100,true);
    		}

    		$this->call('setTimeout(function() { location.reload();}, 1500);');
    	}
    }

    public function premium()
    {
    	$iContestId = $this->get('contest_id');


    	$iType = $this->get('type');

    	if(!Phpfox::getService('contest.permission')->canPremiumContest($iContestId))
    	{
    		$this->alert(_p('contest.you_can_not_perform_this_action'),_p('Notice'),300,100,true);
    		exit;
    	}


    	if (Phpfox::getService('contest.contest.process')->premiumContest($iContestId, $iType))
    	{

    		if($iType == 1)
    		{
    			$this->alert(_p('contest.contest_successfully_premium'),_p('Notice'),300,100,true);
    		}
    		else
    		{
    			$this->alert(_p('contest.contest_successfully_un_premium'),_p('Notice'),300,100,true);
    		}

    		$this->call('setTimeout(function() { location.reload();}, 1500);');
    	}
    }

    public function endingSoon()
    {
    	$iContestId = $this->get('contest_id');

    	$iType = $this->get('type');

    	if(!Phpfox::getService('contest.permission')->canEndingSoonContest($iContestId))
    	{
    		$this->alert(_p('contest.you_can_not_perform_this_action'),_p('Notice'),300,100,true);
    		exit;
    	}

    	if (Phpfox::getService('contest.contest.process')->endingSoonContest($iContestId, $iType))
    	{

    		if($iType == 1)
    		{
    			$this->alert(_p('contest.contest_successfully_ending_soon'),_p('Notice'),300,100,true);
    		}
    		else
    		{
    			$this->alert(_p('contest.contest_successfully_un_ending_soon'),_p('Notice'),300,100,true);
    		}

    		$this->call('setTimeout(function() { location.reload();}, 1500);');
    	}
    }
    
    public function active()
    {
    	$iContestId = $this->get('contest_id');

    	$iType = $this->get('type');

    	if(!Phpfox::getService('contest.permission')->canActiveContest($iContestId))
    	{
    		$this->alert(_p('contest.you_can_not_perform_this_action'),_p('Notice'),300,100,true);
    		exit;
    	}

    	if (Phpfox::getService('contest.contest.process')->activeContest($iContestId, $iType))
    	{

    		if($iType == 1)
    		{
    			$this->alert(_p('contest.contest_successfully_active'),_p('Notice'),300,100,true);
    		}
    		else
    		{
    			$this->alert(_p('contest.contest_successfully_in_active'),_p('Notice'),300,100,true);
    		}

    		$this->call('setTimeout(function() { location.reload();}, 1500);');
    	}
    }

    public function closeContest()
    {
    	$iContestId = $this->get('contest_id');

    	if(!Phpfox::getService('contest.permission')->canCloseContest($iContestId, Phpfox::getUserId()))
    	{
    		$this->alert(_p('contest.you_can_not_perform_this_action'),_p('Notice'),300,100,true);
    		exit;
    	}

    	if (Phpfox::getService('contest.contest.process')->closeContest($iContestId))
    	{
            Phpfox::getService('contest.contest.process')->addUserClose($iContestId, Phpfox::getUserId());	
            $this->alert(_p('contest.closed_contest_successfully'),_p('Notice'),300,100,true);
    		$this->call('setTimeout(function() { location.reload();}, 1500);');
    	}
    	else
    	{
    		$this->alert(_p('contest.you_can_not_perform_this_action'),_p('Notice'),300,100,true);
    	}

    }

    public function deleteContest()
    {
    	$iContestId = $this->get('contest_id');

    	if(!Phpfox::getService('contest.permission')->canDeleteContest($iContestId, Phpfox::getUserId()))
    	{
    		$this->alert(_p('contest.you_can_not_perform_this_action'),_p('Notice'),300,100,true);
    		exit;
    	}
        $bIsAdminCP = $this->get('is_admincp');
    	if (Phpfox::getService('contest.contest.process')->deleteContest($iContestId))
    	{
    		$this->alert(_p('contest.contest_successfully_deleted'),_p('Notice'),300,100,true);

    		$sUrl = Phpfox::getLib('url')->makeUrl('contest'); 
    		Phpfox::addMessage(_p('contest.contest_successfully_deleted'));
            if($bIsAdminCP)
            {
                $this->call('setTimeout(function() { location.reload();}, 1500);');
            }
            else
            {
                $this->call('setTimeout(function() { location.href = \'' . $sUrl . '\';}, 1500);');
            }
    		
    	}
    	else
    	{
    		$this->alert(_p('contest.you_can_not_perform_this_action'),_p('Notice'),300,100,true);
    	}

    }


    public function publishContest()
    {
    	$iContestId = $this->get('contest_id');

    	if(!Phpfox::getService('contest.permission')->canPublishContest($iContestId, Phpfox::getUserId()))
    	{
    		$this->alert(_p('contest.you_can_not_perform_this_action'),_p('Notice'),300,100,true);
    		exit;
    	}
    	
    	if (Phpfox::getService('contest.contest.process')->publishContest($iContestId));
    	{
    		$this->alert(_p('contest.contest_successfully_published'),_p('Notice'),300,100,true);

    		$this->call('setTimeout(function() { location.reload();}, 1500);');
    	}
    }

    public function joinContest()
    {
    	$iContestId = $this->get('contest_id');

    	if (Phpfox::getService('contest.participant.process')->joinContest($iContestId, Phpfox::getUserId()))
    	{
    		$sUrl = Phpfox::getService('contest.contest')->getContestUrl($iContestId);
    		$this->call('location.href = \'' . $sUrl . '\';');
    	}
    	else
    	{
    		$this->alert(_p('contest.you_can_not_perform_this_action'), null, 300, 100, true);
    	}
    }

    public function leaveContest()
    {
    	$iContestId = $this->get('contest_id');

    	if (Phpfox::getService('contest.participant.process')->leaveContest($iContestId, Phpfox::getUserId()))
    	{
    		$sUrl = Phpfox::getService('contest.contest')->getContestUrl($iContestId);
    		$this->call('location.href = \'' . $sUrl . '\';');
    	}
    	else
    	{
    		$this->alert(_p('contest.you_can_not_perform_this_action'), null, 300, 100, true);
    	}
    }

    public function showJoinPopup()
    {
    	$iContestId = $this->get('contest_id');
    	Phpfox::getBlock('contest.join', array(
    		'contest_id' => $iContestId
    		) );
    }

    public function showInvitePopup()
    {
    	$iContestId = $this->get('contest_id');
    	$iEntryId = $this->get('entry_id');
    	Phpfox::getBlock('contest.contest.add-edit.form-invite-friend', array(
    		'contest_id' => $iContestId,
    		'is_popup' => true,
    		'entry_id' => $iEntryId ? $iEntryId : 0
    		) );

    	$this->call('<script type="text/javascript">$Core.loadInit();$("#js_contest_block_invite_friends").show();</script>');
    }

    public function submitInviteForm()
    {
    	$aVals = $this->get('val');
    	$aVals['invite'] = $this->get('friend');
    	$iContestId = $this->get('contest_id');

        if(empty($aVals['invite']) && empty($aVals['emails'])) {
            $this->call("$('#error_message_invite_friend').css('display','block').html('"._p('contest.please_select_a_friend')."').fadeOut(6000);");
                $this->call("$('#yncontest_invite_friend_button').attr('disabled', false);");
        }
        else{
            if (Phpfox::getService('contest.contest.process')->sendInvite($aVals, $iContestId))
            {

                $this->call("$('#yncontest_invite_friend_button').attr('disabled', false);");
        		
                $this->alert(_p('contest.invitation_successfully_sent'),_p('Notice'),300,100,true);

        		$sUrl = Phpfox::getService('contest.contest')->getContestUrl($iContestId);

                $this->call('setTimeout(function() { location.href = \'' . $sUrl . '\';}, 1500);');
        	
            }
        	else
        	{
        		$this->alert(_p('contest.you_can_not_perform_this_action'),_p('Notice'),300,100,true);
        	}
        }
    }

    public function actionEntry(){
    	$type = $this->get('type');
    	$entry_id = $this->get('entry_id');
    	switch($type){
    		case 'approve':
    		Phpfox::getService('contest.entry.process')->approveEntry($entry_id);
    		$this->alert(_p('contest.approve_entries_successfully'));
    		break;
    		case 'deny':
    		Phpfox::getService('contest.entry.process')->denyEntry($entry_id);
    		$this->alert(_p('contest.deny_entries_successfully'));
    		break;
    		case 'winning':
    		Phpfox::getService('contest.entry.process')->winningEntry($entry_id);
    		$this->alert(_p('contest.set_winning_entries_successfully'));
    		break;
    		case 'delete':
    		Phpfox::getService('contest.entry.process')->deletewinningEntry($entry_id);
    		$this->alert(_p('contest.delete_winning_entries_successfully'));
    		break;
    	}


    	$this->call('setTimeout(function() {window.location.href = window.location.href},1500);');

    }

    public function followContest()
    {
    	Phpfox::isUser(true);

    	$iContestId = (int) $this->get('contest_id');
    	$sType = $this->get('type');

    	if(Phpfox::getService('contest.participant.process')->followContest($iContestId, Phpfox::getUserId(), $sType))
    	{
    		if ($sType == 1)
    		{
    			$sHtml = '<a href="#" title="' . _p('contest.un_follow_this_contest') . '" onclick="$.ajaxCall(\'contest.followContest\', \'contest_id=' . $iContestId . '&amp;type=0\'); return false;">' . _p('contest.un_follow') . '</a>';
    		} 
    		elseif ($sType == 0)
    		{
    			$sHtml = '<a href="#" title="' . _p('contest.follow_this_contest') . '" onclick="$.ajaxCall(\'contest.followContest\', \'contest_id=' . $iContestId . '&amp;type=1\'); return false;">' . _p('contest.follow') . '</a>';
    		}

    		$this->html('#yncontest_photo_follow_link', $sHtml);
    	}
    }

    public function favoriteContest()
    {
    	Phpfox::isUser(true);

    	$iContestId = (int) $this->get('contest_id');
    	$sType = $this->get('type');

    	if(Phpfox::getService('contest.participant.process')->favoriteContest($iContestId, Phpfox::getUserId(), $sType))
    	{
    		if ($sType == 1)
    		{
    			$sHtml = '<a href="#" title="' . _p('contest.un_favorite_this_contest') . '" onclick="$.ajaxCall(\'contest.favoriteContest\', \'contest_id=' . $iContestId . '&amp;type=0\'); return false;">' . _p('contest.un_favorite') . '</a>';
    		} 
    		elseif ($sType == 0)
    		{
    			$sHtml = '<a href="#" title="' . _p('contest.favorite_this_contest') . '" onclick="$.ajaxCall(\'contest.favoriteContest\', \'contest_id=' . $iContestId . '&amp;type=1\'); return false;">' . _p('contest.favorite') . '</a>';
    		}

    		$this->html('#yncontest_photo_favorite_link', $sHtml);
    	}
    }

    public function moderateEntry()
    {
    	Phpfox::isUser(true);	

    	switch ($this->get('action'))
    	{
    		case 'approve':
    		foreach ((array) $this->get('item_moderate') as $iId)
    		{
    			Phpfox::getService('contest.entry.process')->approveEntry($iId);
    		}	

    		$this->alert(_p('contest.approve_entries_successfully'));
    		$this->call('setTimeout(function() {window.location.href = window.location.href},1500);');
    		return;
    		break;			
    		case 'deny':
    		foreach ((array) $this->get('item_moderate') as $iId)
    		{
    			Phpfox::getService('contest.entry.process')->denyEntry($iId);
    		}		
    		$this->alert(_p('contest.deny_entries_successfully'));
    		$this->call('setTimeout(function() {window.location.href = window.location.href},1500);');
    		return;
    		break;
    		case 'delete':
    		foreach ((array) $this->get('item_moderate') as $iId)
    		{
    			Phpfox::getService('contest.entry.process')->deletewinningEntry($iId);
    		}	
    		$this->alert(_p('contest.delete_winning_entries_successfully'));
    		$this->call('setTimeout(function() {window.location.href = window.location.href},1500);');
    		return;
    		break;
    		case 'set_as_winning_entries':
    		$entry = array();
    		foreach ((array) $this->get('item_moderate') as $iId)
    		{
    			$entry[] = $iId;	
    		}
    		$entry = implode(",", $entry);
    		if(strlen($entry)>0)			
    		{
    			$this->hide('.moderation_process');
    			$this->call('tb_show("",$.ajaxBox("contest.setWinning","width=400&entry_id='.$entry.'"))');
    			return;
    		}
    		$sMessage = _p('contest.entries_successfully_set_as_winning');
    		break;
    	}

    	$this->alert($sMessage, 'Moderation', 300, 150, true);
    	$this->hide('.moderation_process');			
    }

    public function setWinning(){
    	$this->setTitle(_p('contest.set_as_winning_entries'));
    	$entry_id = $this->get('entry_id');
    	Phpfox::getBlock('contest.entry.set-winning-entries',array(
    		'aIdEntry' => $entry_id
    		));
    }

    public function submit_form_set_winning(){
    	$aVals = $this->get('val');

    	$url = Phpfox::getService('contest.entry.process')->winningEntry($aVals);

    	$this->alert(_p('contest.set_winning_entries_successfully'));
    	$this->call('setTimeout(function() { location.href = \'' . $url . '\';}, 1500);');
    }

    public function getPromoteContestBox()
    {
    	$iContestId = (int) $this->get('contest_id');
        
        $this->setTitle(_p('contest.promote_contest'));
    	Phpfox::getBlock('contest.contest.promote-contest', array('contest_id' => $iContestId));
    }

    public function changePromoteBadge()
    {
    	$iContestId = $this->get('contest_id');
    	$aVals = $this->get('val');
    	$iStatus = Phpfox::getService('contest.constant')->getBadgeStatusIdByName('both');
    	if(isset($aVals['photo']) && isset($aVals['description']))
    	{
    		$iStatus = Phpfox::getService('contest.constant')->getBadgeStatusIdByName('both');
    	}
    	elseif(isset($aVals['photo']) && !isset($aVals['description']))
    	{
    		$iStatus = Phpfox::getService('contest.constant')->getBadgeStatusIdByName('photo');	
    	}
    	elseif(!isset($aVals['photo']) && isset($aVals['description']))
    	{
    		$iStatus = Phpfox::getService('contest.constant')->getBadgeStatusIdByName('description');	
    	}
    	elseif(!isset($aVals['photo']) && !isset($aVals['description']))
    	{
    		$iStatus =  Phpfox::getService('contest.constant')->getBadgeStatusIdByName('none');	
    	}
    	$sFrameUrl = Phpfox::getService('contest.contest')->getFrameUrl($iContestId, $iStatus);
        $sBadgeCode = Phpfox::getService('contest.contest')->getBadgeCode($sFrameUrl); 

    	$this->html('#yncontest_promote_contest_badge_code_textarea', htmlentities($sBadgeCode));

    	$this->call("$('#yncontest_promote_iframe iframe').attr('src','" . $sFrameUrl . "');");
    }

    public function approveContest()
    {
    	$iContestId = $this->get('contest_id');

    	if (Phpfox::getService('contest.contest.process')->approveContest($iContestId))
    	{
    		$this->alert(_p('contest.contest_successfully_approved'),_p('Notice'),300,100,true);

    		$sUrl = Phpfox::getService('contest.contest')->getContestUrl($iContestId);
    		$this->call('setTimeout(function() { location.reload();}, 1500);');
		// $this->call('setTimeout(function() { location.href = \'' . $sUrl . '\';}, 1500);');
    	}
    	else
    	{
    		$this->alert(_p('contest.you_can_not_perform_this_action'),_p('Notice'),300,100,true);
    	}

    }

    public function denyContest()
    {
    	$iContestId = $this->get('contest_id');

    	if (Phpfox::getService('contest.contest.process')->denyContest($iContestId))
    	{
    		$this->alert(_p('contest.contest_successfully_denied'),_p('Notice'),300,100,true);

    		$sUrl = Phpfox::getService('contest.contest')->getContestUrl($iContestId);
		// $this->call('setTimeout(function() { location.href = \'' . $sUrl . '\';}, 1500);');
    		$this->call('setTimeout(function() { location.reload();}, 1500);');
    	}
    	else
    	{
    		$this->alert(_p('contest.you_can_not_perform_this_action'),_p('Notice'),300,100,true);
    	}
    }

    public function deleteAnnouncement()
    {
    	$iAnnouncementId = $this->get('announcement_id');
    	if(Phpfox::getService('contest.announcement.process')->delete($iAnnouncementId))
    	{
    		$this->call("$('#yc_announcemet_" . $iAnnouncementId . "').hide('slow');");
    		$this->alert(_p('contest.announcement_successfully_deleted'),_p('contest.delete'),300,100,true);
    	}
    }

    public function removeWinningEntry()
    {
        $iEntryId = $this->get('entry_id');
        if (Phpfox::getService('contest.entry.process')->removeWinningEntry($iEntryId))
        {
            $this->alert(_p('contest.entry_successfully_removed_from_winning_list'),_p('Notice'),300,100,true);

            $this->call('setTimeout(function() { location.reload();}, 1500);');
        }
        else
        {
            $this->alert(_p('contest.you_can_not_perform_this_action'),_p('Notice'),300,100,true);
        }
    }
	
    public function moderation()
	{
		Phpfox::isUser(true);
		
		switch ($this->get('action'))
		{
			case 'delete':
				Phpfox::isAdmin(true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
					Phpfox::getService('contest.contest.process')->deleteContest($iId);
					$this->slideUp('#js_contest_item_' . $iId);
				}				
				$sMessage = _p('contest.contest_s_successfully_deleted');
				break;
		}
		
		$this->alert($sMessage, 'Moderation', 300, 150, true);
		$this->hide('.moderation_process');			
	}
	public function categoryOrdering()
	{
		Phpfox::isAdmin(true);
		$aVals = $this->get('val');
		Phpfox::getService('core.process')->updateOrdering(array(
															 'table' => 'contest_category',
															 'key' => 'category_id',
															 'values' => $aVals['ordering']
														 )
		);

		Phpfox::getLib('cache')->remove('contest_category', 'substr');
	}

	public function updateActivity()
	{
		if (Phpfox::getService('contest.category.process')->updateActivity($this->get('id'), $this->get('active'), $this->get('sub')))
		{

		}
	}
}

?>