<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_Process extends Phpfox_Service 
{
	private $_bHasImage = false;
	private $_sDirFundraising = "";
	
	/**
	 * Class constructor
	 */	
	public function __construct()
	{
		$this->_sTable = Phpfox::getT('fundraising_campaign');
		$this->_sDirFundraising = Phpfox::getParam('core.dir_pic'). 'fundraising/';
	}
      
	private function _verify(&$aVals, $bIsUpdate = false)
	{
		if (isset($_FILES['image']))
		{
		    foreach ($_FILES['image']['error'] as $iKey => $sError)
		    {
			   if ($sError == UPLOAD_ERR_OK) 
			   {            
				  $aImage = Phpfox::getLib('file')->load('image[' . $iKey . ']', array(
					  'jpg',
					  'gif',
					  'png'
				   )
				  );
				  				  
				  if ($aImage === false)
				  {				    
				    return false;
				  }
				  
				  $this->_bHasImage = true;
			   }
		    }
		}
		return true;	
	}
     
    public function close($iId)
    {
        $iId = rtrim($iId, ',');
        return $this->database()->update(Phpfox::getT('fundraising'), array('status'=> '1'), 'is_approved = 1 AND campaign_id IN(' . $iId . ')');
    }

  
	
	public function updateTitle($iId, $iUserId, $sTitle)
	{
		$oFilter = Phpfox::getLib('parse.input');
		$sTitle = $oFilter->clean($sTitle, 255);
		if(!empty($sTitle))
		{                
		    $this->database()->update(Phpfox::getT('fundraising_campaign'), array('title' => $sTitle), 'campaign_id = ' . (int) $iId);
		    return $iId;
		}
		return 0;
	}
      
      public function updateInvite($iId, $iSignedId)
      {
        if((int)$iSignedId)
        {
          $this->database()->update(Phpfox::getT('fundraising_invited'), array('signed_id' => (int)$iSignedId), 'invite_id = ' . (int) $iId);
          return $iId;
        }
        return 0;
      }
		
	public function deleteNews($iId)
        {
		$aNews = $this->database()->select('pn.news_id')
			->from(Phpfox::getT('fundraising_news'),'pn')
			->join(Phpfox::getT('fundraising_campaign'),'p','p.campaign_id = pn.campaign_id')
			->where('pn.news_id = ' . (int) $iId. ' AND p.user_id = ' . Phpfox::getUserId())
			->execute('getSlaveRow');
		if($aNews)
		{
			$this->database()->delete(Phpfox::getT('fundraising_news'), "news_id = " . (int) $iId);
			return true;
		}
		return false;
	}
		
	public function postNews($iId, $aVals)
        {
            $oFilter = Phpfox::getLib('parse.input');
            $sHeadline = $oFilter->clean($aVals['news_headline'], 255);
            $sContent = $oFilter->clean($aVals['news_content']);

			$iCampaignId = $iId;
            if(!empty($sHeadline) && !empty($sContent))
            {
		$aInsert = array(
			'campaign_id'		=> $iId,			
			'headline' 		=> $sHeadline,
			'link'	 		=> $aVals['news_link'],
			'content'		=> $oFilter->prepare($sContent),
			'time_stamp'		=> PHPFOX_TIME			
		);
		
		if(isset($aVals['news_id']) && (int)$aVals['news_id'] > 0)
		{
			$iId = $aVals['news_id'];
			$iId = $this->database()->update(Phpfox::getT('fundraising_news'), $aInsert, 'news_id = ' . (int) $iId);			
		}
		else
		{
			$iId = $this->database()->insert(Phpfox::getT('fundraising_news'), $aInsert);		
		}
		Phpfox::getService('fundraising.campaign')->notifyToAllFollowers($iCampaignId, $sItemType = 'news');
                return $iId;
            }
            return 0;
        }	
	      
    public function getInvitedId($iItemId, $iUserid)
    {
      return (int) $this->database()->select('invite_id')
                   ->from(Phpfox::getT('fundraising_invited'))
                   ->where('campaign_id = ' . (int) $iItemId . ' AND invited_user_id = ' . $iUserid)
                   ->execute('getSlaveField');
    }
    
	public function sentInvite($iId,$aVals)
	{
		$oParseInput = Phpfox::getLib('parse.input');
		$aFundraising = $this->database()->select('campaign_id, user_id, title')
			->from($this->_sTable)
			->where('campaign_id = ' . (int) $iId)
			->execute('getSlaveRow');
				
		if (isset($aVals['emails']) || isset($aVals['invite']))
		{		
			$aInvites = $this->database()->select('invited_user_id, invited_email')
				->from(Phpfox::getT('fundraising_invited'))
				->where('campaign_id = ' . (int) $iId)
				->execute('getRows');
                    
			$aInvited = array();
			foreach ($aInvites as $aInvite)
			{
				$aInvited[(empty($aInvite['invited_email']) ? 'user' : 'email')][(empty($aInvite['invited_email']) ? $aInvite['invited_user_id'] : $aInvite['invited_email'])] = true;
			}			
		}
		
		if (isset($aVals['emails']))
		{
			{
				$aEmails = explode(',', $aVals['emails']);
				
				$aCachedEmails = array();
				foreach ($aEmails as $sEmail)
				{
					$sEmail = trim($sEmail);
					if (!Phpfox::getLib('mail')->checkEmail($sEmail))
					{
						continue;
					}
					
					if (isset($aInvited['email'][$sEmail]))
					{
						continue;
					}
					
					if(isset($aCachedEmails[$sEmail]) && $aCachedEmails[$sEmail] == true)
				     {
						continue;
				     }
					
					$sLink = Phpfox::getLib('url')->permalink('fundraising', $aFundraising['campaign_id'], $aFundraising['title']);

					$sMessage = _p('full_name_invited_you_to_the_title', array(
							'full_name' => Phpfox::getUserBy('full_name'),
							'title' => $oParseInput->clean($aVals['title'], 255),
							'link' => $sLink
						)
					);
					
					if (!empty($aVals['personal_message']))
					{
						$sMessage .= ' '._p('full_name_added_the_following_personal_message', array(
								'full_name' => Phpfox::getUserBy('full_name')
							)
						) . "\n\n";
						$sMessage .= $aVals['personal_message'];
					}
					$oMail = Phpfox::getLib('mail');
					if (isset($aVals['invite_from']) && $aVals['invite_from'] == 1)
					{
						$oMail->fromEmail(Phpfox::getUserBy('email'))
								->fromName(Phpfox::getUserBy('full_name'));
					}
					$bSent = $oMail->to($sEmail)
						->subject(array('fundraising.full_name_invited_you_to_the_fundraising_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $oParseInput->clean($aVals['title'], 255))))
						->message($sMessage)
						->send();
						
					if ($bSent)
					{
						$this->_aInvited[] = array('email' => $sEmail);
						
						$aCachedEmails[$sEmail] = true;
						
						$this->database()->insert(Phpfox::getT('fundraising_invited'), array(
								'campaign_id' => $iId,
								'type_id' => 1,
								'user_id' => Phpfox::getUserId(),
								'invited_email' => $sEmail,
								'time_stamp' => PHPFOX_TIME
							)
						);
					}
				}
			}
		}
		
		if (isset($aVals['invite']) && is_array($aVals['invite']))
		{
			$sUserIds = '';
			foreach ($aVals['invite'] as $iUserId)
			{
				if (!is_numeric($iUserId))
				{
					continue;
				}
				$sUserIds .= $iUserId . ',';
			}
			$sUserIds = rtrim($sUserIds, ',');
			
			$aUsers = $this->database()->select('user_id, email, language_id, full_name')
				->from(Phpfox::getT('user'))
				->where('user_id IN(' . $sUserIds . ')')
				->execute('getSlaveRows');
				
			foreach ($aUsers as $aUser)
			{
				if (isset($aCachedEmails[$aUser['email']]))
				{
					continue;
				}	
				
				if (isset($aInvited['user'][$aUser['user_id']]))
				{
					continue;
				}
				
				$sLink = Phpfox::getLib('url')->permalink('fundraising', $aFundraising['campaign_id'], $aFundraising['title']);

				$sMessage = _p('full_name_invited_you_to_the_title', array(
						'full_name' => Phpfox::getUserBy('full_name'),
						'title' => $oParseInput->clean($aVals['title'], 255),
						'link' => $sLink
					), false,null, $aUser['language_id']);
				if (!empty($aVals['personal_message']))
				{
					$sMessage .= _p('full_name_added_the_following_personal_message', array(
							'full_name' => Phpfox::getUserBy('full_name')
						), false, null, $aUser['language_id']
					) .":\n". $aVals['personal_message'];
				}
				$bSent = Phpfox::getLib('mail')->to($aUser['user_id'])						
					->subject(array('fundraising.full_name_invited_you_to_the_fundraising_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $oParseInput->clean($aVals['title'], 255))))
					->message($sMessage)
					->notification('fundraising.invite_to_a_fundraising')
					->send();
						
				if ($bSent)
				{
					$this->_aInvited[] = array('user' => $aUser['full_name']);	
					
					$iInviteId = $this->database()->insert(Phpfox::getT('fundraising_invited'), array(
							'campaign_id' => $iId,								
							'user_id' => Phpfox::getUserId(),
							'invited_user_id' => $aUser['user_id'],
							'time_stamp' => PHPFOX_TIME
						)
					);
					
					(Phpfox::isModule('request') ? Phpfox::getService('request.process')->add('fundraising_invite', $iId, $aUser['user_id']) : null);
				}
			}
		}
	}

			

	public function updateView($iId)
	{
		$this->database()->query("
			UPDATE " . $this->_sTable . "
			SET total_view = total_view + 1
			WHERE campaign_id = " . (int) $iId . "
		");			
		
		return true;
	}	

	public function approve($iId)
	{
		Phpfox::getUserParam('fundraising.can_approve_campaigns', true);
		
		$aFundraising = $this->database()->select('p.*, ' . Phpfox::getUserField())
			->from(Phpfox::getT('fundraising_campaign'), 'p')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
			->where('p.campaign_id = ' . (int) $iId)
			->execute('getRow');
			
		if (!isset($aFundraising['campaign_id']))
		{
			return Phpfox_Error::set(_p('the_fundraising_you_are_trying_to_approve_is_not_valid'));
		}
		
		if ($aFundraising['is_approved'] == '1')
		{
			return false;
		}
		
		$this->database()->update(Phpfox::getT('fundraising_campaign'), array('is_approved' => '1', 'time_stamp' => PHPFOX_TIME), 'campaign_id = ' . $aFundraising['campaign_id']);
		
//		$iCatId = (int)Phpfox::getService('fundraising.category')->getCategoriesByCampaignId($iId);
		
//		$this->database()->query("UPDATE " . Phpfox::getT('fundraising_category') . "
//					  SET used = used + 1
//					  WHERE category_id = " . $iCatId . "
//					");		

		if (Phpfox::isModule('feed') && $aFundraising['status'] == 2)
		{
               $aCallback = ($aFundraising['module_id'] != 'fundraising' ? Phpfox::callback('fundraising.addFundraising', $aFundraising['campaign_id']) : null);
		   (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback($aCallback)->add('fundraising', $iId, $aFundraising['privacy'], $aFundraising['privacy_comment'], $aFundraising['item_id'], $aFundraising['user_id']) : null);
		}		
		
		if (Phpfox::isModule('notification'))
		{
			Phpfox::getService('notification.process')->add('fundraising_approved', $aFundraising['campaign_id'], $aFundraising['user_id']);
		}
		
		if ($aFundraising['is_approved'] == '9')
		{
			$this->database()->updateCounter('user', 'total_spam', 'user_id', $aFundraising['user_id'], true);
		}
		
		Phpfox::getService('user.activity')->update($aFundraising['user_id'], 'fundraising');
		
		// Send the user an email
		$sLink = Phpfox::getLib('url')->permalink('fundraising', $aFundraising['campaign_id'], $aFundraising['title']);
		Phpfox::getLib('mail')->to($aFundraising['user_id'])
			->subject(array('fundraising.your_fundraising_has_been_approved_on_site_title', array('site_title' => Phpfox::getParam('core.site_title'))))
			->message(array('fundraising.your_fundraising_has_been_approved_on_site_title_message', array('site_title' => Phpfox::getParam('core.site_title'), 'link' => $sLink)))
			->notification('fundraising.fundraising_is_approved')
			->send();			
		$this->cache()->remove('fundraising', 'substr');
		return true;
	}	
	
        
     public function feature($iId, $sType=0)
	{
		Phpfox::getUserParam('fundraising.can_feature_campaign', true);		
		
		if($this->database()->update($this->_sTable, array('is_featured' => ($sType == '1' ? 1 : 0)), 'is_approved = 1 AND campaign_id = ' . (int) $iId))
		{
			$this->cache()->remove('fundraising_featured');
			return true;
		}
		return false;
	}
        //P_Check
	public function directsign($iId, $sType=0)
	{
		Phpfox::isAdmin(true);
		
		$this->database()->update($this->_sTable, array('is_directsign' => 0),'1=1');

		$this->database()->update($this->_sTable, array('is_directsign' => ($sType == '1' ? 1 : 0)), 'campaign_id = ' . (int) $iId);
		
		$this->cache()->remove('fundraising','substr');
  
		return true;                		
	}
	
	public function delete($iId)
	{          
		(($sPlugin = Phpfox_Plugin::get('fundraising.service_process_delete__start')) ? eval($sPlugin) : false);
               
		$aFundraising = Phpfox::getService('fundraising.campaign')->getBasicInfoOfCampaign($iId);
                
		if (!isset($aFundraising['campaign_id']))
		{
			return false;
		}
          
            $iUserId = Phpfox::getService('fundraising.campaign')->hasAccess($iId, 'delete_own_campaign', 'delete_user_campaign');
      
            if (!$iUserId)
            {
               return false;
            }
		
		$aTemp = $this->database()->select("image_path")->from(Phpfox::getT('fundraising_image'))->where("campaign_id = '$iId'")->execute("getRows");
		$this->database()->delete(Phpfox::getT('fundraising_image'), "campaign_id = '$iId'");
		$aThumbs = array();
		foreach($aTemp as $aRow)
		{
		    $aThumbs[] = $aRow["image_path"];
		}
		
		foreach($aThumbs as $sImagePath)
		{
			//$aFundraising['image_path'] = $sImagePath;
			if (!empty($sImagePath))
			{
				$aImages = array(
					    Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, ''),
					    Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, '_120'),
					    Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, '_300'),
					    Phpfox::getParam('core.dir_pic') . sprintf($sImagePath, '_600')
					    );
				$iFileSizes = 0;
				
				foreach ($aImages as $sImage)
				{
					if (file_exists($sImage))
					{
						$iFileSizes += filesize($sImage);
						if ($sPlugin = Phpfox_Plugin::get('fundraising.service_process_delete__pre_unlink')){return eval($sPlugin);}
						@unlink($sImage);
					}
				}
				    
//				if ($iFileSizes > 0)
//				{
//					if ($sPlugin = Phpfox_Plugin::get('fundraising.service_process_delete__pre_space_update')){return eval($sPlugin);}
//					Phpfox::getService('user.space')->update($aFundraising['user_id'], 'fundraising', $iFileSizes, '-');
//				}
			    }
		}
		   
		  $this->database()->delete(Phpfox::getT('fundraising_campaign'), "campaign_id = " . (int) $iId);
		  $this->database()->delete(Phpfox::getT('fundraising_text'), "campaign_id = " . (int) $iId);		
//        	$this->database()->delete(Phpfox::getT('fundraising_sign'), 'campaign_id  = ' . (int)$iId);
            $this->database()->delete(Phpfox::getT('fundraising_news'), 'campaign_id  = ' . (int)$iId);
            $this->database()->delete(Phpfox::getT('fundraising_invited'), 'campaign_id  = ' . (int)$iId);
		
		(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('fundraising',(int) $iId) : null);
		(Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('comment_fundraising', $iId) : null);		
		
            //Delete pages feed
            if($aFundraising['module_id'] == 'pages')
            {
               $sType = 'fundraising';
               
               $aFeeds = $this->database()->select('feed_id, user_id')
			->from(Phpfox::getT($aFundraising['module_id'] . '_feed'))
			->where('type_id = \'' . $sType . '\' AND item_id = ' . (int) $iId )
			->execute('getRows');
			
               foreach ($aFeeds as $aFeed)
               {			
                  $this->database()->delete(Phpfox::getT($aFundraising['module_id'] . '_feed'), 'feed_id = ' . $aFeed['feed_id']);
               }
            }            
		// Update user activity
		Phpfox::getService('user.activity')->update($aFundraising['user_id'], 'fundraising', '-');
		
		$this->cache()->remove('fundraising', 'substr');		
		$aRows = $this->database()->select('campaign_id, category_id')
			->from(Phpfox::getT('fundraising_category_data'))
			->where('campaign_id = ' . (int) $iId)
			->execute('getRows');
				
		if (count($aRows))
		{
			foreach ($aRows as $aRow)
			{
				$this->database()->delete(Phpfox::getT('fundraising_category_data'), "campaign_id = " . (int) $aRow['campaign_id'] . " AND category_id = " . (int) $aRow['category_id']);				
				$this->database()->updateCount('fundraising_category_data', 'category_id = ' . (int) $aRow['category_id'], 'used', 'fundraising_category', 'category_id = ' . (int) $aRow['category_id']);			
			}
		}	
		(($sPlugin = Phpfox_Plugin::get('fundraising.service_process_delete')) ? eval($sPlugin) : false);
		return true;
	}
	
	public function deleteInline($iId)
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.service_process_deleteinline__start')) ? eval($sPlugin) : false);
		if (($iUserId = Phpfox::getService('fundraising')->hasAccess($iId, 'delete_own_campaign', 'delete_user_campaign')))
		{
			$this->delete($iId);
			
			(Phpfox::isModule('attachment') ? Phpfox::getService('attachment.process')->deleteForItem($iUserId, $iId, 'fundraising') : null);
			(Phpfox::isModule('comment') ? Phpfox::getService('comment.process')->deleteForItem($iUserId, $iId, 'fundraising') : null);
			(Phpfox::isModule('tag') ? Phpfox::getService('tag.process')->deleteForItem($iUserId, $iId, 'fundraising') : null);
			
			// Update user activity
			//Phpfox::getService('user.activity')->update($iUserId, 'fundraising', '-');				
			$this->cache()->remove('fundraising', 'substr');
			return true;
		}
		(($sPlugin = Phpfox_Plugin::get('fundraising.service_process_deleteinline__end')) ? eval($sPlugin) : false);
		return false;
	}
	
	public function setDefault($iImageId)
	{
	    $aFundraising = $this->database()->select('pimg.image_path, pimg.server_id, p.campaign_id, p.user_id')
		->from(Phpfox::getT('fundraising_image'), 'pimg')
		->join($this->_sTable, 'p', 'p.campaign_id = pimg.campaign_id')
		->where('pimg.image_id = ' . (int) $iImageId)
		->execute('getSlaveRow');
		
	    if (!isset($aFundraising['user_id']))
	    {
		return Phpfox_Error::set(_p('unable_to_find_the_image'));
	    }
    
	    if (!Phpfox::getService('user.auth')->hasAccess('fundraising', 'campaign_id', $aFundraising['campaign_id'], 'fundraising.delete_own_campaign', 'fundraising.delete_user_campaign', $aFundraising['user_id']))
	    {
		return Phpfox_Error::set(_p('you_do_not_have_sufficient_permission_to_modify_this_fundraising'));
	    }            
	    
	    $this->database()->update($this->_sTable, array('image_path' => $aFundraising['image_path'], 'server_id' => $aFundraising['server_id']), 'campaign_id = ' . $aFundraising['campaign_id']);
	    
	    return true;
	}
	
	public function deleteImage($iImageId)
	{
	    $aFundraising = $this->database()->select('pimg.image_id, pimg.image_path, pimg.server_id, p.user_id, p.campaign_id, p.image_path AS default_image_path')
		->from(Phpfox::getT('fundraising_image'), 'pimg')
		->join($this->_sTable, 'p', 'p.campaign_id = pimg.campaign_id')
		->where('pimg.image_id = ' . (int) $iImageId)
		->execute('getSlaveRow');
				
	    if (!isset($aFundraising['user_id']))
	    {
		return Phpfox_Error::set(_p('unable_to_find_the_image'));
	    }    
    
	    if (!Phpfox::getService('user.auth')->hasAccess('fundraising', 'campaign_id', $aFundraising['campaign_id'], 'fundraising.delete_own_campaign', 'fundraising.delete_user_campaign', $aFundraising['user_id']))
	    {
		return Phpfox_Error::set(_p('you_do_not_have_sufficient_permission_to_modify_this_fundraising'));
	    }            
	     
	    $iFileSizes = 0;
	    $aSizes = array('', 50, 120, 300);
	    foreach ($aSizes as $iSize)
	    {
		$sImage = Phpfox::getParam('core.dir_pic') . sprintf($aFundraising['image_path'], (empty($iSize) ? '' : '_' ) . $iSize);
		if (file_exists($sImage))
		{
		    $iFileSizes += filesize($sImage);
		    
		    @unlink($sImage);
		}
	    }

	    $this->database()->delete(Phpfox::getT('fundraising_image'), 'image_id = ' . $aFundraising['image_id']);
	    $bIsNewImage = 0;
         if ($aFundraising['default_image_path'] == $aFundraising['image_path'])
	    {
		  $aFundraisingImages = Phpfox::getService('fundraising')->getImages($aFundraising['campaign_id']);
		  if(!empty($aFundraisingImages))
		  {
			   $bIsNewImage = $aFundraisingImages[0]['image_id'];
		  }
		  $this->database()->update($this->_sTable, array('image_path' => (empty($aFundraisingImages) ? null : $aFundraisingImages[0]['image_path'] ), 'server_id' => (empty($aFundraisingImages) ? null : $aFundraisingImages[0]['server_id'])), 'campaign_id = ' . $aFundraising['campaign_id']);
	    }
          
	    if ($iFileSizes > 0)
	    {
		  Phpfox::getService('user.space')->update($aFundraising['user_id'], 'fundraising', $iFileSizes, '-');
	    }
	    
	    return $bIsNewImage;
	}
      
	 public function updateCounter($iId, $bMinus = false)
	{
		$this->database()->query("
			UPDATE " . $this->_sTable . "
			SET total_comment = total_comment " . ($bMinus ? "-" : "+") . " 1
			WHERE campaign_id = " . (int) $iId . "
		");	
	}

	public function __call($sMethod, $aArguments)
	{
		if ($sPlugin = Phpfox_Plugin::get('fundraising.service_process__call'))
		{
			return eval($sPlugin);
		}
		
		Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
	}	
}

?>
