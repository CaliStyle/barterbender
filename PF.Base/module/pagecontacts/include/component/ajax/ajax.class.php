<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		YouNet Company
 * @author  		MinhNTK
 * @package  		Module_PageContacts
 * @version 		3.01
 */
class PageContacts_Component_Ajax_Ajax extends Phpfox_Ajax {

	public function popup()
	{
		$iPageId = $this->get('iPageId');
	
		phpfox::getBlock('pagecontacts.contact', array('iPageId'=>$iPageId));
	}
	
	public function config()
	{
		phpfox::getBlock('pagecontacts.config', array());
	}
	
	public function addConfig()
	{
		$sError = '';
		$sCheck = '';
		$aVals = $this->get('val');
		if(empty($aVals['contact_description']))
		{
			$sError .= _p('pagecontacts.add_description_to_your_contact').'<br/>';
		}
		if(empty($aVals['q']))
		{
			$sError .= _p('pagecontacts.add_topic_to_your_contact').'<br/>';
		}
		else
		{
			foreach($aVals['q'] as $iKey => $aItem)
			{
				if(empty($aItem['question']))
				{
					$sTopicCheck = _p('pagecontacts.add_topic_to_your_contact').'<br/>';
				}
				if(empty($aItem['email']))
				{
					$sEmailCheck = _p('pagecontacts.add_email_to_your_topic').'<br/>';
				}
				else
				{
					if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+(\.([a-zA-Z0-9\._-]+)){1,2}$/", $aItem['email']))
					{
				
					}
					else
					{
						$sEmailCheck = _p('pagecontacts.invalid_email').'<br/>';
					}
				}
			}
		}
		if(!empty($sTopicCheck))
		{
			$sError .= $sTopicCheck;
		}
		if(!empty($sEmailCheck))
		{
			$sError .= $sEmailCheck;
		}
		if(empty($sError))
		{
			$aContact = phpfox::getService('pagecontacts')->getContactOfPage($aVals['page_id']);
			/* if contact is exist, update contact information */
			if(!empty($aContact) && $aContact)
			{
				if(($bIsEdit = phpfox::getService('pagecontacts.process')->update($aVals)))
				{
					$aNewPage = Phpfox::getService('pages')->getForEdit($aVals['page_id']);
					$sComplete = _p('pagecontacts.update_completed');
					$this->call('$(".message").html("' . $sComplete . '").slideDown(200).delay(1500).fadeOut(1500);');				
					//$this->call('window.location.href = \'' . Phpfox::getService('pages')->getUrl($aNewPage['page_id'], $aNewPage['title'], $aNewPage['vanity_url']). '\';');	
				}
				
			}
			/* add new contact information */
			else
			{
				$iId = phpfox::getService('pagecontacts.process')->add($aVals);
				$aNewPage = Phpfox::getService('pages')->getForEdit($aVals['page_id']);
				$sComplete = _p('pagecontacts.update_completed');
				$this->call('$(".message").html("' . $sComplete . '").slideDown(200).delay(1500).fadeOut(1500);');								
				//$this->call('window.location.href = \'' . Phpfox::getService('pages')->getUrl($aNewPage['page_id'], $aNewPage['title'], $aNewPage['vanity_url']). '\';');	
				//return $iId;
			}
		}
		else
		{
			if($aVals['is_active'])
			{
				$this->call('$(".error_message").html("' . $sError . '").slideDown(200).delay(1500).fadeOut(1500);');
			}
			else
			{
				$aContact = phpfox::getService('pagecontacts')->getContactOfPage($aVals['page_id']);
				/* if contact is exist, update contact information */
				if(!empty($aContact) && $aContact)
				{
					if(($bIsEdit = phpfox::getService('pagecontacts.process')->update($aVals)))
					{
						$aNewPage = Phpfox::getService('pages')->getForEdit($aVals['page_id']);
						$sComplete = _p('pagecontacts.update_completed');
						$this->call('$(".message").html("' . $sComplete . '").slideDown(200).delay(1500).fadeOut(1500);');
						//$this->call('window.location.href = \'' . Phpfox::getService('pages')->getUrl($aNewPage['page_id'], $aNewPage['title'], $aNewPage['vanity_url']). '\';');	
					}
					
				}
				/* add new contact information */
				else
				{
					$iId = phpfox::getService('pagecontacts.process')->add($aVals);
					$aNewPage = Phpfox::getService('pages')->getForEdit($aVals['page_id']);
					$sComplete = _p('pagecontacts.update_completed');
					$this->call('$(".message").html("' . $sComplete . '").slideDown(200).delay(1500).fadeOut(1500);');
					//$this->call('window.location.href = \'' . Phpfox::getService('pages')->getUrl($aNewPage['page_id'], $aNewPage['title'], $aNewPage['vanity_url']). '\';');	
					//return $iId;
				}
			}
		}
		$sDisableJs = "$('#btnContactUpdate').removeClass('disabled').removeAttr('disabled');";
		$sJs = 'setTimeout("'.$sDisableJs.'", 2700);';
		$this->call($sJs);
	}
	
	public function sendMail()
	{
		$aVals = $this->get('val');
		$sError = '';
		if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+(\.([a-zA-Z0-9\._-]+)){1,2}$/", $aVals['email']))
		{
			
		}
		else
		{
			$sError = _p('pagecontacts.invalid_email').'<br/>';
		}
		if(empty($aVals['subject']))
		{
			$sError .= _p('pagecontacts.add_subject_to_your_contact').'<br/>';
		}
		if(empty($aVals['full_name']))
		{
			$sError .= _p('pagecontacts.add_full_name_to_your_contact').'<br/>';
		}
		if(empty($aVals['message']))
		{
			$sError .= _p('pagecontacts.add_message_to_your_contact').'<br/>';
		}
		if(empty($sError))
		{
			if(($bIsSendMail = phpfox::getService('pagecontacts.process')->sendMail($aVals)))
			{
				$sComplete = _p('pagecontacts.contact_was_sent');
				$this->call('$("#pagecontact_message").html("' . $sComplete . '").slideDown(200).delay(1500).fadeOut(2000);');
				$sJs = 'setTimeout("js_box_remove($(\'#pagecontact_message\'))", 3000);';
				$this->call($sJs);
			}
		}
		else
		{
			$this->call('$("#pagecontact_error_message").html("' . $sError . '").slideDown(200).delay(1500).fadeOut(1000);');
			$sDisableJs = "$('#btnContactSend').removeClass('disabled').removeAttr('disabled');";
			$sJs = 'setTimeout("'.$sDisableJs.'", 1800);';
			$this->call($sJs);
		}
	}
}

?>