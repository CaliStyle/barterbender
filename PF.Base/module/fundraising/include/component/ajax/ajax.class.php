<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Ajax_Ajax extends Phpfox_Ajax
{

	/**
	 * if owner closes his campaign, he doesn't have to give the reason
	 * @TODO: check permission 
	 * @by minhta
	 */
	public function sendMailToAllDonors()
	{
		$iCampaignId = $this->get('campaign_id');

		Phpfox::isUser(true);

		if($this->get('submit'))
		{
			$sMessage = $this->get('message');
			$sSubject = $this->get('subject');
			$aCustomMessage = array(
				'message' => $sMessage,
				'subject' => $sSubject
			);
			
			if(Phpfox::getService('fundraising.mail.process')->sendEmailToAllDonors($iTemplateType = 0, $iCampaignId, $aCustomMessage)) {
				$this->alert(_p('message_is_successfully_sent'),null, 300, 150, true);
				return true;	
			}
		}
		else
		{
			Phpfox::getBlock('fundraising.campaign.form-email-to-all-donors', array('iCampaignId' => $iCampaignId));
			$this->setTitle(_p('send_mail_to_all_donors'));
		}

	}
	

	public function changePromoteBadge()
	{
		$iCampaignId = $this->get('campaign_id');
		$aVals = $this->get('val');
		$iStatus = Phpfox::getService('fundraising')->getBadgeStatusNumber('both');
		if(isset($aVals['donate_button']) && isset($aVals['donors']))
		{
			$iStatus = Phpfox::getService('fundraising')->getBadgeStatusNumber('both');
		}
		elseif(isset($aVals['donate_button']) && !isset($aVals['donors']))
		{
			$iStatus = Phpfox::getService('fundraising')->getBadgeStatusNumber('donate_button');	
		}
		elseif(!isset($aVals['donate_button']) && isset($aVals['donors']))
		{
			$iStatus = Phpfox::getService('fundraising')->getBadgeStatusNumber('donors');
		}
		elseif(!isset($aVals['donate_button']) && !isset($aVals['donors']))
		{
			$iStatus = Phpfox::getService('fundraising')->getBadgeStatusNumber('none');	
		}
		$sFrameUrl = Phpfox::getService('fundraising')->getFrameUrl($iCampaignId, $iStatus); 

		$this->html('#ynfr_promote_campaign_badge_code_textarea', Phpfox::getLib('parse.input')->prepare(Phpfox::getService('fundraising')->getBadgeCode($sFrameUrl)));

		$this->call("$('#ynfr_promote_iframe iframe').attr('src','" . $sFrameUrl . "');");
	}
	/**
	 * if owner closes his campaign, he doesn't have to give the reason
	 * @TODO: check permission 
	 * @by minhta
	 */
	public function closeCampaign()
	{
		$bIsOwner = $this->get('is_owner');
		$iCampaignId = $this->get('campaign_id');

		Phpfox::isUser(true);

		if(!$bIsOwner)
		{
			if($this->get('submit_reason'))
			{
				$sMessage = $this->get('message');
				
				if(Phpfox::getService('fundraising.campaign.process')->closeCampaign($iCampaignId, $sMessage))
				{
					$this->alert(_p('campaign_is_successfully_closed'),null, 300, 150, true);
					$this->call('setTimeout(function() { window.location.reload();}, 1000)');
					return true;
				}
			}
			else
			{
				Phpfox::getBlock('fundraising.campaign.form-reason-close-campaign', array('iCampaignId' => $iCampaignId));
				$this->setTitle(_p('close_this_campaign'));
			}
		}
		else
		{
			if(Phpfox::getService('fundraising.campaign.process')->closeCampaign($iCampaignId))
			{
				$this->alert(_p('campaign_is_successfully_closed'),null, 300, 150, true);
				$this->call('setTimeout(function() { window.location.reload();}, 1000)');
				return true;
			}
			
		}

	}


	public function getPromoteCampaignBox()
	{
		$iCampaignId = $this->get('id');
		if(!$iCampaignId)
		{
			return false;
		}

		Phpfox::getBlock('fundraising.campaign.promote-campaign', array('iCampaignId' => $iCampaignId));
		
	}

	/**
	 * follow or unfollow a campaign based on follow param
	 * if follow = 1 means user wants to follow, if  = 0 means want to un-follow
	 * @by minhta
	 */
	public function follow()
	{
		Phpfox::isUser(true);

		$iCampaignId = (int) $this->get('campaign_id');
		$sType = $this->get('type');

		if(Phpfox::getService('fundraising.campaign.process')->follow($iCampaignId, Phpfox::getUserId(), $sType))
		{
			// if we follow successfully, we need changing to un follow
			if ($sType == 1)
			{
				$sHtml = '<a href="#" title="' . _p('un_follow_this_campaign') . '" onclick="$.ajaxCall(\'fundraising.follow\', \'campaign_id=' . $this->get('campaign_id') . '&amp;type=0\'); return false;">' . _p('un_follow') . '</a>';
			} 
			elseif ($sType == 0)
			{
				$sHtml = '<a href="#" title="' . _p('follow_this_campaign') . '" onclick="$.ajaxCall(\'fundraising.follow\', \'campaign_id=' . $this->get('campaign_id') . '&amp;type=1\'); return false;">' . _p('follow') . '</a>';
			}


			//replace the old html by the new one, and show alert message for user
			$this->html('#ynfr_profile_follow_link', $sHtml)->alert(($sType == '1' ? _p('campaign_is_successfully_followed') : _p('campaign_is_successfully_un_followed')), null, 300, 150, true);
		}

	}
	public function highlight()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('fundraising.can_highlight_campaign', true);

		$isAdmin = $this->get('admin');

		// check is in admin cp and admin is featuring a campaign
		if($isAdmin)
		{
		   if (Phpfox::getService('fundraising.campaign.process')->highlight($this->get('campaign_id'), $this->get('active')))
		   {	
				return true;
		   }		   
		}
		else
		{

			if (Phpfox::getService('fundraising.campaign.process')->highlight($this->get('campaign_id'), $this->get('type')))
			{
				if ($this->get('type') == '1')
				{
					$sHtml = '<a href="#" title="' . _p('un_highlight_this_campaign') . '" onclick="$.ajaxCall(\'fundraising.highlight\', \'campaign_id=' . $this->get('campaign_id') . '&amp;type=0\'); return false;">' . _p('un_highlight') . '</a>';
				}
				else
				{
					$sHtml = '<a href="#" title="' . _p('highlight_this_campaign') . '" onclick="$.ajaxCall(\'fundraising.highlight\', \'campaign_id=' . $this->get('campaign_id') . '&amp;type=1\'); return false;">' . _p('highlight') . '</a>';
				}
		
				$this->html('#js_fundraising_highlight_' . $this->get('campaign_id'), $sHtml)->alert(($this->get('type') == '1' ? _p('campaign_is_successfully_highlighted') : _p('campaign_is_successfully_un_highlighted')));

				$this->call('setTimeout(function() { window.location.reload();}, 1000)');
				return true;
			}		
		}
		return false;	
	}
	public function setDefaultImage()
	{
	    if (Phpfox::getService('fundraising.campaign.process')->setDefaultImage($this->get('id')))
	    {
			Phpfox::getLib('cache')->remove('fundraising_featured_0');
	    }
	}
	
    public function sentToTarget()
    {
        Phpfox::isUser(true);
        if (Phpfox::getService('fundraising')->sendToTarget((int)$this->get('id'),0))
        {
          $this->alert(_p('fundraising_letter_was_successfully_sent_to_target'));
        }
        else
        {
           $this->alert('<div class="error_message">'._p('an_error_occurred_and_fundraising_letter_could_not_be_sent_to_target_please_try_again').'</div>');
        }
    }

	public function deleteNews()
	{
		if (Phpfox::getService('fundraising.process')->deleteNews($this->get('news_id')))
		{
			$this->call("$('#news_" . $this->get('news_id') . "').hide('slow');");
			$this->alert(_p('news_successfully_deleted'),_p('delete'),300,100,true);
		}		
	}
	
	public function postNews()
	{
		if ($aVals = $this->get('val'))
		{
			if(isset($aVals['post_news']) || isset($aVals['update_news']))
			{				
				if($iId = Phpfox::getService('fundraising.process')->postNews($aVals['campaign_id'],$aVals))
				{
					Phpfox::getBlock('fundraising.detail', array('sType' => 'news', 'id' => $aVals['campaign_id']));
					$this->call('$("#fundraising_comment_block").hide();');
					$this->call('$("#js_details_container").html("' . $this->getContent() . '");')->call('$Core.loadInit();');
					if(isset($aVals['news_id']) && (int)$aVals['news_id'] > 0)
					{
						$this->alert(_p('news_successfully_updated'),_p('update'),300,100,true);
						$this->alert('News updated');
					}
					else
					{
						$this->alert(_p('news_successfully_added'),_p('add'),300,100,true);
					}
				};
			}
		}		
	}
	
	public function inviteBlock()
	{
		Phpfox::getBlock('fundraising.campaign.form-invite-friend',
					array(
                        'id' => $this->get('id'),
                        'url' => $this->get('url'),
                    )
				);			
		$this->setTitle(_p('sign_this_fundraising'));
        $this->call('<script type="text/javascript">$Core.loadInit();$("#js_fundraising_block_invite_friends").show();</script>');
	}
	
	public function inviteFriends()
	{
		if ($aVals = $this->get('val'))
		{
			if(isset($aVals['invite']))
			{
				Phpfox::getService('fundraising.process')->sentInvite($aVals['campaign_id'],$aVals);
				$this->alert(_p('successfully_invited_users'),_p('invite_friends'),300, 100,true);
			}
		}
		else
		{
			$this->alert(_p('an_error_occurred_and_invited_message_could_not_be_sent'),_p('invite_friends'),300, 100,false);
		}
	}

    public function deleteImage()
    {
        Phpfox::isUser(true);
        $id = $this->get('id');

        if ($iNewImage = Phpfox::getService('fundraising.image.process')->delete($id)) {
            Phpfox::getLib('cache')->remove('fundraising_featured_0');
        }

        $this->call('$("#js_photo_holder_' . $id . '").remove(); onAfterDeletePhotoSuccess(' . intval($iNewImage) . ');');
    }
	
	public function updateCategory()
	{
		$sCategory = Phpfox::getService('fundraising.category.process')->update($this->get('category_id'), $this->get('quick_edit_input'), $this->get('user_id'));
		
		$this->call('window.location.href = \'' . Phpfox::getLib('url')->makeUrl('admincp.fundraising.category') . '\'');
	}
        
        
      public function updateFundraising()
	{                            
		$sFundraising = Phpfox::getService('fundraising.process')->updateTitle($this->get('campaign_id'),$this->get('user_id'), $this->get('quick_edit_input'));
		if($sFundraising)
                {
                    $this->call('window.location.href = \'' . Phpfox::getLib('url')->makeUrl('admincp.fundraising') . '\'');
                }		
	}

	public function moderation()
	{
		Phpfox::isUser(true);
		
		switch ($this->get('action'))
		{
			case 'approve':
				Phpfox::getUserParam('fundraising.can_approve_campaigns', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
					Phpfox::getService('fundraising.process')->approve($iId);
					$this->remove('#js_fundraising_entry' . $iId);					
				}
				$this->updateCount();
				$sMessage = _p('fundraising_s_successfully_approved');
				break;                        
			case 'delete':
                       
				Phpfox::getUserParam('fundraising.delete_user_campaign', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
					Phpfox::getService('fundraising.process')->delete($iId);
					$this->slideUp('#js_fundraising_entry' . $iId);
				}				
				$sMessage = _p('fundraising_s_successfully_deleted');
				break;
		}
		
		$this->alert($sMessage, 'Moderation', 300, 150, true);
		$this->hide('.moderation_process');
        $this->call('setTimeout(function() { window.location.reload();}, 1800)');
	}
	
	public function inlineDelete()
	{
		Phpfox::isUser(true);
		if (Phpfox::getService('fundraising.campaign.process')->delete($this->get('item_id')))
		{
			$this->call("$('#js_fundraising_entry" . $this->get('item_id') . "').hide('slow'); $('#core_js_messages').message('" . _p('fundraising_deleted', array('phpfox_squote' => true)) . "', 'valid').fadeOut(5000);");
		}
	}
	
	
	public function displayDetail()
	{
		$sType = $this->get('sType');
		Phpfox::getBlock('fundraising.detail', array('sType' => $sType, 'id' => $this->get('id'),'page' => $this->get('page')));
		if($sType == 'description')
		{
			$this->call('$("#fundraising_comment_block").show();');
		}
		else
		{
			$this->call('$("#fundraising_comment_block").hide();');
		}
		if(!Phpfox::getParam('core.site_wide_ajax_browsing')){
			$this->call('$("#js_details_container").html("' . $this->getContent() . '");')->call('$Core.loadInit();');	
		}else{
			$this->call('$("#js_details_container").html("' . $this->getContent() . '");');	
		}
	}
	
	public function approve()
	{
		Phpfox::isUser(true);
		if (Phpfox::getService('fundraising.campaign.process')->approve($this->get('id')))
		{
			if ($this->get('inline'))
			{
				$this->alert(_p('fundraising_has_been_approved'), _p('fundraising_approved'), 300, 100, true);
				$this->hide('#js_item_bar_approve_image');
				$this->hide('.js_moderation_off'); 
				$this->show('.js_moderation_on');
				$this->call('setTimeout(function() { window.location.reload();}, 1000);');
			}			
		}
	}
        
	//P_Check
	public function feature()
	{
		Phpfox::isUser(true);

		$isAdmin = $this->get('admin');

		// check is in admin cp and admin is featuring a campaign
		if($isAdmin)
		{
		   if (Phpfox::getService('fundraising.campaign.process')->feature($this->get('campaign_id'), $this->get('active')))
		   {	
				return true;
		   }		   
		}
		else
		{
			if (Phpfox::getService('fundraising.campaign.process')->feature($this->get('campaign_id'), $this->get('type')))
			{
				if ($this->get('type') == '1')
				{
					$sHtml = '<a href="#" title="' . _p('un_feature_this_fundraising') . '" onclick="$.ajaxCall(\'fundraising.feature\', \'campaign_id=' . $this->get('campaign_id') . '&amp;type=0\'); return false;">' . _p('un_feature') . '</a>';
				}
				else
				{
					$sHtml = '<a href="#" title="' . _p('feature_this_fundraising') . '" onclick="$.ajaxCall(\'fundraising.feature\', \'campaign_id=' . $this->get('campaign_id') . '&amp;type=1\'); return false;">' . _p('feature') . '</a>';
				}
		
				$this->html('#js_fundraising_feature_' . $this->get('campaign_id'), $sHtml)->alert(($this->get('type') == '1' ? _p('fundraising_successfully_featured') : _p('fundraising_successfully_un_featured')));
				if ($this->get('type') == '1')
				{
					$this->addClass('#js_fundraising_entry' . $this->get('campaign_id'), 'row_featured_image');
					$this->call('$(\'#js_fundraising_entry' . $this->get('campaign_id') . '\').css(\'height\',\'189px\').find(\'.js_featured_fundraising:first\').show();');
				}
				else
				{
					$this->removeClass('#js_fundraising_entry' . $this->get('campaign_id'), 'row_featured_image');
					$this->call('$(\'#js_fundraising_entry' . $this->get('campaign_id') . '\').css(\'height\',\'190px\').find(\'.js_featured_fundraising:first\').hide();');
				}
				return true;
			}		
		}
		return false;
	}

	
      public function getNew()
	{
		Phpfox::getBlock('fundraising.new');
		
		$this->html('#' . $this->get('id'), $this->getContent(false));
		$this->call('$(\'#' . $this->get('id') . '\').parents(\'.block:first\').find(\'.bottom li a\').attr(\'href\', \'' . Phpfox::getLib('url')->makeUrl('fundraising') . '\');');
	}
        
	public function helpOrdering()
	{
		Phpfox::isAdmin(true);
		$aVals = $this->get('val');
		
		Phpfox::getService('core.process')->updateOrdering(array(
				'table' => 'fundraising_help',
				'key' => 'help_id',
				'values' => $aVals['ordering']
			)
		);				
	}

    // this get email template for admincp

    public function fillEmailTemplate()
    {
        $iTypeId = $this->get('type_id');

        if(empty($iTypeId))
            $iTypeId = 0;

        $aEmail = Phpfox::getService('fundraising.mail')->getEmailTemplate($iTypeId);

		$aEmail['email_template'] = str_replace('"', '\"', $aEmail['email_template']);
		$this->call('$("#email_subject").val("' . $aEmail['email_subject'] . '"); Editor.setContent("' . $aEmail['email_template'] . '")');
    }

    /**
     * when 1 user share anything , their name will appear as a supporter
     */

    public function SupporterShare()
    {
        $iCampaignId = $this->get('campaign_id');

		if(!Phpfox::isUser())
		{
			return false;
		}

		if(Phpfox::getService('fundraising.user')->checkSupporterExist(Phpfox::getUserId(), $iCampaignId)) {
			Phpfox::getService('fundraising.user')->updateSupporter('share', Phpfox::getUserId());
			return true;
		}

        Phpfox::getService('fundraising.user')->addSupporter($iCampaignId);
    }

	public function setActive()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('fundraising.can_active_campaign', true);

		$isAdmin = $this->get('admin');

		// check is in admin cp and admin is featuring a campaign
		if($isAdmin)
		{
		   if (Phpfox::getService('fundraising.campaign.process')->setActive($this->get('campaign_id'), $this->get('active')))
		   {	
				return true;
		   }		   
		}
		else
		{
			if (Phpfox::getService('fundraising.campaign.process')->setActive($this->get('campaign_id'), $this->get('type')))
			{
				if ($this->get('type') == '1')
				{
					$sHtml = '<a href="#" title="' . _p('s_inactive') . '" onclick="$.ajaxCall(\'fundraising.setActive\', \'campaign_id=' . $this->get('campaign_id') . '&amp;type=0\'); return false;">' . _p('s_inactive') . '</a>';
				}
				else
				{
					$sHtml = '<a href="#" title="' . _p('s_active') . '" onclick="$.ajaxCall(\'fundraising.setActive\', \'campaign_id=' . $this->get('campaign_id') . '&amp;type=1\'); return false;">' . _p('s_active') . '</a>';
				}
		
				$this->html('#js_fundraising_setactive_' . $this->get('campaign_id'), $sHtml)->alert(($this->get('type') == '1' ? _p('campaign_is_successfully_active') : _p('campaign_is_successfully_inactive')));

				$this->call('setTimeout(function() { window.location.reload();}, 1000)');
				
				return true;
			}		
		}
		return false;	
	}

	// payment
	public function updateGatewayActivity()
	{
		if (Phpfox::getService('fundraising.gateway.process')->updateActivity($this->get('gateway_id'), $this->get('active')))
		{
			
		}		
	}
	
	public function updateGatewayTest()
	{
		if (Phpfox::getService('fundraising.gateway.process')->updateTest($this->get('gateway_id'), $this->get('active')))
		{
			
		}			
	}

    public function updateActivity()
    {
        if (Phpfox::getService('fundraising.category.process')->updateActivity($this->get('id'), $this->get('active')))
        {

        }
    }

    public function categoryOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Phpfox::getService('core.process')->updateOrdering(array(
                'table' => 'fundraising_category',
                'key' => 'category_id',
                'values' => $aVals['ordering']
            )
        );

        Phpfox::getLib('cache')->remove('fundraising_category', 'substr');
    }
}

?>