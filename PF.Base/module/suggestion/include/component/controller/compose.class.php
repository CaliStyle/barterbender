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
 */
class Suggestion_Component_Controller_Compose extends Phpfox_Component
{
	private $_bReturn = null;
	
	public function process()
	{			
		Phpfox::isUser(true);		
		Phpfox::getUserParam('mail.can_compose_message', true);		
                
                if (!Phpfox::isModule('mail')){
                    exit('Please enable module: mail.');
                }
                $iUserId = $this->request()->get('id');
                $sTitle = $this->request()->get('link');
                $sLink = $sTitle;
                
                if ((int)$iUserId>0){
                    $sUserLink = Phpfox::getService('suggestion')->getUserLink($iUserId);
                    $sPhrases = _p('suggestion.send_a_personal_message_to_user');
                    $sPhrases = preg_replace('/{{user_name}}/', $sUserLink, $sPhrases);
                    
                    $sHeader = _p('suggestion.you_might_need_user_name_permission_to_join_data');                    
                    $sHeader = preg_replace('/{{user_name}}/', $sUserLink, $sHeader);
                    /*merge # link to current pages or blogs items*/
                    $sHeader = preg_replace('/{{data}}/', $sLink, $sHeader);
                }else{
                    $sPhrases = '';
                    $sHeader = '';
                }
                
		if (Phpfox::getParam('mail.spam_check_messages') && Phpfox::isSpammer())
		{			
			return Phpfox_Error::display(_p('mail.currently_your_account_is_marked_as_a_spammer'));
		}
		
		$aUser = array();
		if (($iUserId = $this->request()->get('id')) || ($iUserId = $this->getParam('id')))
		{
			$aUser = Phpfox::getService('user')->getUser($iUserId, Phpfox::getUserField());			
			if (isset($aUser['user_id']))
			{
				if (Phpfox::getService('mail')->canMessageUser($aUser['user_id']) == false)
				{
                                        $sUserLink = Phpfox::getService('suggestion')->getUserLink($iUserId);
                                        
					return Phpfox_Error::display(preg_replace('/{{user_name}}/', $sUserLink, _p('suggestion.unable_to_send_a_private_message_to_this_user_at_the_moment')));
				}
				
				$this->template()->assign('aUser', $aUser);
			}
			
			(($sPlugin = Phpfox_Plugin::get('mail.component_controller_compose_controller_to')) ? eval($sPlugin) : false);
		}
		
		$aValidation = array(
			'subject' => _p('mail.provide_subject_for_your_message'),
			'message' => _p('mail.provide_message')
		);		
		
		if (Phpfox::isModule('captcha') && Phpfox::getUserParam('mail.enable_captcha_on_mail'))
		{
			$aValidation['image_verification'] = _p('captcha.complete_captcha_challenge');
		}		
		
		(($sPlugin = Phpfox_Plugin::get('mail.component_controller_compose_controller_validation')) ? eval($sPlugin) : false);

		$oValid = Phpfox::getLib('validator')->set(array(
				'sFormName' => 'js_form', 
				'aParams' => $aValidation
			)
		);
		
		if ($aVals = $this->request()->getArray('val'))
		{			
			// Lets make sure they are actually trying to send someone a message.			
			if (((!isset($aVals['to'])) || (isset($aVals['to']) && !count($aVals['to']))) && (!isset($aVals['copy_to_self']) || $aVals['copy_to_self'] != 1))
			{
				Phpfox_Error::set(_p('mail.select_a_member_to_send_a_message_to'));
			}
						
			if ($oValid->isValid($aVals))
			{				
				if (Phpfox::getParam('mail.mail_hash_check'))
				{
					Phpfox::getLib('spam.hash', array(
								'table' => 'mail_hash',
								'total' => Phpfox::getParam('mail.total_mail_messages_to_check'),
								'time' => Phpfox::getParam('mail.total_minutes_to_wait_for_pm'),
								'content' => $aVals['message']
							)				
						)->isSpam();					
				}
				
				if (Phpfox::getParam('mail.spam_check_messages'))
				{
					if (Phpfox::getLib('spam')->check(array(
								'action' => 'isSpam',										
								'params' => array(
									'module' => 'comment',
									'content' => Phpfox::getLib('parse.input')->prepare($aVals['message'])
								)
							)
						)
					)
					{
						Phpfox_Error::set(_p('mail.this_message_feels_like_spam_try_again'));
					}
				}				
				
				if (Phpfox_Error::isPassed())
				{
					if ($aIds = Phpfox::getService('mail.process')->add($aVals))
					{
						if (PHPFOX_IS_AJAX)
						{
							$this->_bReturn = true;
							
							return true;
						}
						
						if (count($aIds) > 1)
						{
							$this->url()->send('mail.view' , array('id' => base64_encode(serialize($aIds))));
						}
						elseif(isset($aIds[0]))
						{
							$this->url()->send('mail.view' , array('id' => $aIds[0]));
						}
					}
					else 
					{
						if (PHPFOX_IS_AJAX)
						{
							$this->_bReturn = false;

							return false;
						}
					}					
				}
				else 
				{
					if (PHPFOX_IS_AJAX)
					{
						$this->_bReturn = false;
						
						return false;
					}
				}
			}
			else 
			{
				if (PHPFOX_IS_AJAX)
				{
					$this->_bReturn = false;
						
					return false;
				}				
			}
		}
		
		Phpfox::getService('mail')->buildMenu();
		if (Phpfox::isModule('friend'))
		{
			$this->template()->setPhrase(array('loading'));
		}
		if (Phpfox::getParam('core.wysiwyg') == 'tiny_mce')
		{
				Phpfox::getService('tinymce')->load();
				$sTinyMceCode = str_replace('\'', '\\\'', Phpfox::getService('tinymce')->getJsCode());
				$sTinyMceCode = str_replace( array("\r\n", "\n", "\r"), '', $sTinyMceCode);
				
				
		}
		$this->template()->setTitle(_p('mail.compose_new_message'))
			->setBreadcrumb(_p('mail.mail'), $this->url()->makeUrl('mail'))
			->setBreadcrumb(_p('mail.compose_new_message'), $this->url()->makeUrl('mail.compose'), true)
			->setPhrase(array(
					'mail.add_new_folder',
					'mail.adding_new_folder',
					'mail.view_folders',
					'mail.edit_folders',
					'mail.you_will_delete_every_message_in_this_folder',
					
				)
			)
			->setEditor()
			->setHeader('cache', array(
					'switch_legend.js' => 'static_script',
					'switch_menu.js' => 'static_script',			
					'jquery/plugin/jquery.highlightFade.js' => 'static_script',
					'jquery/plugin/jquery.scrollTo.js' => 'static_script',
					'mail.js' => 'module_mail',
					'pager.css' => 'style_css'
				)
			)
			->assign(array(
				'sCreateJs' => $oValid->createJS(),
				'sGetJsForm' => $oValid->getJsForm(),
				'iMaxRecipients' => Phpfox::getUserParam('mail.send_message_to_max_users_each_time'),
				'sPhrases' => $sPhrases,
				'sHeader' => $sHeader
			)
		);
		
		$this->setParam('attachment_share', array(		
				'type' => 'mail',
				'inline' => true,
				'id' => 'js_form_mail'
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('suggestion.component_controller_compose_clean')) ? eval($sPlugin) : false);
	}
	
	public function getReturn()
	{
		if (!$this->_bReturn)
		{
			Phpfox::getLib('ajax')->call('$Core.processForm(\'#js_mail_compose_submit\', true);');
		}
		
		return $this->_bReturn;
	}
}

?>