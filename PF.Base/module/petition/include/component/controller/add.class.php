<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Controller_Add extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		Phpfox::isUser(true);
        $current_tab = 1;

		if (isset( $_SESSION['ynpetition_tabright']))
		{
			$current_tab = $_SESSION['ynpetition_tabright'];
			unset($_SESSION['ynpetition_tabright']);
		}

		$sFriendMessageTemplate = '';
		$bIsEdit = false;
		$bCanEditPersonalData = true;
		$aCallback = false;
		$sModule = $this->request()->get('module', false);
		$iItem =  $this->request()->getInt('item', false);
		$iMaxUpload = Phpfox::getUserParam('petition.total_photo_upload_limit');
		$iDefaultSignature = Phpfox::getParam('petition.default_signature_goal');
        $aCallback = false;

		if ($sModule !== false && $iItem !== false) //&& Phpfox::hasCallback($sModule, 'getPetitionDetails')
		{
			if (($aCallback = Phpfox::callback('petition.getPetitionDetails', array('item_id' => $iItem))))
			{
				$this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
				$this->template()->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
				if ($sModule == 'pages' && !Phpfox::getService('pages')->hasPerm($iItem, 'petition.share_petitions'))
				{
					return Phpfox_Error::display(Phpfox::getPhrase('petition.unable_to_view_this_item_due_to_privacy_settings'));
				}
			}
		}

		if (($iEditId = $this->request()->getInt('id')) && !isset($_POST['val']['add']))
		{
			$oPetition = Phpfox::getService('petition')->callback($aCallback);

			if($aRow = $oPetition->getPetitionForEdit($iEditId))
			   {

				 if ($aRow['module_id'] != 'petition')
				 {
					  $sModule = $aRow['module_id'];
					  $iItem = $aRow['item_id'];
				 }

				 if (Phpfox::isModule('tag'))
				 {
					  $aTags = Phpfox::getService('tag')->getTagsById('petition', $aRow['petition_id']);
					  if (isset($aTags[$aRow['petition_id']]))
					  {
						   $aRow['tag_list'] = '';
						   foreach ($aTags[$aRow['petition_id']] as $aTag)
						   {
							    $aRow['tag_list'] .= ' ' . $aTag['tag_text'] . ',';
						   }
						   $aRow['tag_list'] = trim(trim($aRow['tag_list'], ','));
					  }
				 }

				 if(!(Phpfox::getService('petition')->hasAccess($aRow['petition_id'], 'edit_own_petition', 'edit_user_petition')))
				 {
				    return false;
				 }


				 if (Phpfox::getUserParam('petition.edit_user_petition') && Phpfox::getUserId() != $aRow['user_id'])
				 {
					  $bCanEditPersonalData = false;
				 }

				 $bIsEdit = true;

				 $aRow['end_time_month'] = date('n', $aRow['end_time']);
				 $aRow['end_time_day'] = date('j', $aRow['end_time']);
				 $aRow['end_time_year'] = date('Y', $aRow['end_time']);

				 if ($bIsEdit)
				 {
					  $aMenus = array(
						   'detail' => Phpfox::getPhrase('petition.main_info'),
						   'photos' => Phpfox::getPhrase('petition.photos'),
						   'invite' => Phpfox::getPhrase('petition.invite_friends'),
						   'letter' => Phpfox::getPhrase('petition.petition_letter')
					  );
				   $sLetter = Phpfox::getParam('petition.friend_letter_template');

				   $sFriendMessageTemplate = Phpfox::getService('petition')->parseVar($sLetter,$aRow);
				 }
				 $sTab = $this->request()->get('tab');

				$aImages = Phpfox::getService('petition')->getImages($aRow['petition_id']);
				$iMaxUpload = $iMaxUpload - count($aImages);

				 $this->template()->assign(array('aForms' => $aRow,'sTab' => $sTab));

				 (($sPlugin = Phpfox_Plugin::get('petition.component_controller_add_process_edit')) ? eval($sPlugin) : false);
			   }
			   else
			   {
				 Phpfox_Error::set(Phpfox::getPhrase('petition.unable_to_find_the_petition_you_are_trying_to_edit'));
			   }
		}
		else
		{
			Phpfox::getUserParam('petition.add_new_petition', true);
		}

		if ($sModule && $iItem && Phpfox::hasCallback($sModule, 'viewPetition'))
		{
			$aCallback = Phpfox::callback($sModule . '.viewPetition', $iItem);
			$this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);
			$this->template()->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
			if ($sModule == 'pages' && !Phpfox::getService('pages')->hasPerm($iItem, 'petition.share_petitions'))
			{
				return Phpfox_Error::display(Phpfox::getPhrase('petition.unable_to_view_this_item_due_to_privacy_settings'));
			}
		}

		$aValidation = array(
			'target' => array(
				'def' => 'required',
				'title' => Phpfox::getPhrase('petition.fill_target_for_petition')
			),
			'title' => array(
				'def' => 'required',
				'title' => Phpfox::getPhrase('petition.fill_title_for_petition')
			),
			'petition_goal' => array(
				'def' => 'required',
				'title' => Phpfox::getPhrase('petition.fill_in_a_petition_goal_for_your_petition')
			),
			'short_description' => array(
				'def' => 'required',
				'title' => Phpfox::getPhrase('petition.add_description_to_petition')
			),



		);

		(($sPlugin = Phpfox_Plugin::get('petition.component_controller_add_process_validation')) ? eval($sPlugin) : false);

		$oValid = Phpfox::getLib('validator')->set(array(
				'sFormName' => 'core_js_petition_form',
				'aParams' => $aValidation
			)
		);

		if ($aVals = $this->request()->getArray('val'))
		{
			$iDefaultSignature = $aVals['signature_goal'];
			if ($oValid->isValid($aVals))
			{
				// Add the new petition
				if (isset($aVals['victory']))
				{
					$aVals['petition_status'] = 3;
					$sMessage = Phpfox::getPhrase('petition.petition_successfully_saved');
				}
				else if (isset($aVals['closed']))
				{
					$aVals['petition_status'] = 1;
					$sMessage = Phpfox::getPhrase('petition.your_petition_has_been_closed');
				}
				else
				{
					$sMessage = Phpfox::getPhrase('petition.your_petition_has_been_added');
				}

				if (($iFlood = Phpfox::getUserParam('petition.flood_control_petition')) !== 0 && !$bIsEdit)
				{
					$aFlood = array(
						'action' => 'last_post', // The SPAM action
						'params' => array(
							'field' => 'time_stamp', // The time stamp field
							'table' => Phpfox::getT('petition'), // Database table we plan to check
							'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
							'time_stamp' => $iFlood * 60 // Seconds);
						)
					);

					// actually check if flooding
					if (Phpfox::getLib('spam')->check($aFlood))
					{
						Phpfox_Error::set(Phpfox::getPhrase('petition.your_are_posting_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
					}
				}

				if (Phpfox_Error::isPassed())
				{
                           if ($sModule && $iItem && !$bIsEdit)
                           {
                              $aVals['module_id']  = $sModule;
                              $aVals['item_id'] = $iItem;
                           }

                           $aSendParam = array();
                           if($sModule)
                              $aSendParam['module'] = $sModule;
                           if($iItem)
                              $aSendParam['item'] = $iItem;
                           // Update a petition
                           if ($bIsEdit)
                           {
                              // Update the petition

                              if($iId = Phpfox::getService('petition.process')->update($aRow['petition_id'], $aRow['user_id'], $aVals, $aRow))
                              {

                                    $aSendParam['id'] = $iId;
						            $sMessage = Phpfox::getPhrase('petition.petition_updated');
							        $_SESSION['ynpetition_tabright'] = 1;
							    if(isset($aVals['submit_photo']))
								{
								  $_SESSION['ynpetition_tabright']=2;

								}
								if(isset($aVals['submit_invite']))
								{
								  $_SESSION['ynpetition_tabright']=3;

								}
								if(isset($aVals['submit_letter']))
								{
								  $_SESSION['ynpetition_tabright']=4;

								}

								$current_tab = $_SESSION['ynpetition_tabright'];


                                if($this->request()->get('req3') == 'setup')
                                {

                                      $sTab = $this->request()->get('tab');

								if(isset($aVals['submit_photo']))
								{
								  $sMessage = Phpfox::getPhrase('petition.successfully_added_photo_s_to_your_petition');
								  $_SESSION['ynpetition_tabright']=1;
								  $current_tab = 1;


								}

								else if(isset($aVals['submit_invite']))
								{
								  $sMessage = Phpfox::getPhrase('petition.successfully_invited_users');
								}
								else if(isset($aVals['submit_letter']))
								{
								  $sMessage = Phpfox::getPhrase('petition.petition_letter_successfully_updated');
								}

                                      switch($sTab)
                                      {
                                        case 'photos':
								    if(isset($aVals['submit_photo']))
								    {
									 //$aSendParam['tab'] = 'invite';
									$aSendParam['tab'] = 'photos';


									 $this->url()->send('petition.add.setup', $aSendParam, $sMessage);

								    }
								    else
								    {
									  $aSendParam['tab'] = 'photos';
								    }
								    break;
                                        case 'invite':
								    if(isset($aVals['submit_invite']))
								    {
									 $aSendParam['tab'] = 'letter';
									 $this->url()->send('petition.add.setup', $aSendParam , $sMessage);
								    }
								    else
								    {
									  $aSendParam['tab'] = 'invite';
								    }
								   break;
								case 'letter':
								    $aSendParam['tab'] = 'letter';
								    $this->url()->send('petition.add', $aSendParam, $sMessage);
								    break;
                                      }

							   $this->url()->send('petition.add.setup', $aSendParam , $sMessage);
                                }
                                $this->url()->send('petition.add', $aSendParam, $sMessage);
                              }
						else
						{
							$iMaxUpload = Phpfox::getUserParam('petition.total_photo_upload_limit');
							$aImages = Phpfox::getService('petition')->getImages($aRow['petition_id']);
							$iMaxUpload = $iMaxUpload - count($aImages);

						}
                           }
                           else
							   //Add new petition
							if ($iId = Phpfox::getService('petition.process')->add($aVals))
                           {
                              $aSendParam['id'] = $iId;
                              $aSendParam['tab'] = 'photos';
                              $this->url()->send('petition.add.setup', $aSendParam, Phpfox::getPhrase('petition.your_petition_has_been_added'));
                           }
				}

			}
		}

		$aCategories = Phpfox::getService('petition.category')->getCategories('c.user_id = 0');

		$this->template()->setHeader(array("<script type=\"text/javascript\">
						   function checkSignatureGoal()
						   {
							$('#core_js_petition_form_msg').html('');
							var val = $('#signature_goal').val();
							if ( val.search(/^-?[0-9]+$/) != 0 || parseInt(val,10) < 0)
							{
							     bIsValid = false;
							     $('#core_js_petition_form_msg').message('" . Phpfox::getPhrase('petition.signature_goal_must_be_a_integer_number') ."', 'error');
							     $('#signature_goal').addClass('alert_input');
							     $('html, body').animate({ scrollTop: 0 }, 0);
							     return false;
							 }
							 else if (parseInt(val,10) > 4294967295)
							 {
								bIsValid = false;
							     $('#core_js_petition_form_msg').message('" . Phpfox::getPhrase('petition.signature_goal_must_be_less_than_4294967295') ."', 'error');
							     $('#signature_goal').addClass('alert_input');
							     $('html, body').animate({ scrollTop: 0 }, 0);
								return false;
							 }
							 else
							 {
							     return true;
							 }
						    }
                               function IsEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}
							function checkEmails()
							{
							    var sEmails = $('#target_email').val();                                          
							    if(sEmails.length == 0)
								 return true;                                          
							    var aEmails = sEmails.split(',');
							    
							    if(aEmails.length == 0)
							    {								
								 $('html, body').animate({scrollTop:0}, 0);
								 return false;
							    }
							    for (var i = 0; i < aEmails.length; i++)
							    {
							     if (!(IsEmail($.trim(aEmails[i]))))
								 {
								    bIsValid = false;
									$('#core_js_petition_form_msg').html('');
								    $('#core_js_petition_form_msg').message('" . Phpfox::getPhrase('petition.provide_a_valid_email_address') . "', 'error');
								    $('#target_email').addClass('alert_input');
								    $('html, body').animate({scrollTop:0},0);
								    return false;
								 }
								  
							    }
		
							    return true;
							} 
						    </script>"
						    )
					     );
		$this->template()->setTitle(($bIsEdit ? Phpfox::getPhrase('petition.editing_petition') . ': ' . $aRow['title'] : Phpfox::getPhrase('petition.adding_a_new_petition')))
			->setBreadcrumb(Phpfox::getPhrase('petition.petitions'), ($aCallback === false ? $this->url()->makeUrl('petition') : $this->url()->makeUrl($aCallback['url_home_pages'])))
			->setBreadcrumb(($bIsEdit ? Phpfox::getPhrase('petition.editing_petition') . ': ' . Phpfox::getLib('parse.output')->shorten($aRow['title'], Phpfox::getService('core')->getEditTitleSize(), '...') : Phpfox::getPhrase('petition.adding_a_new_petition')),
                                  ($iEditId > 0 ? ($aCallback == false ? $this->url()->makeUrl('petition', array('add','id' => $iEditId)) : $this->url()->makeUrl('petition', array('add', 'id' => $iEditId,'module'=>$aCallback['module_id'],'item'=>$aCallback['item_id'])) ) : ($aCallback == false ? $this->url()->makeUrl('petition', array('add')) :  $this->url()->makeUrl('petition', array('add','module' => $aCallback['module_id'], 'item' => $aCallback['item_id'])))), true)
			->setFullSite()
			->assign(array(
					'current_tab'=>$current_tab,
					'sCreateJs' => $oValid->createJS(),
					'sGetJsForm' => $oValid->getJsForm(),
					'sModule' => ($aCallback !== false ? $sModule : ''),
					'iItem' => ($aCallback !== false ? $iItem : ''),
					'bIsEdit' => $bIsEdit,
					'sFriendMessageTemplate' => $sFriendMessageTemplate,
					'bCanEditPersonalData' => $bCanEditPersonalData,
					'aCategories' => $aCategories,
					'iMaxUpload' => $iMaxUpload,
					'iDefaultSignature' => $iDefaultSignature,
					'iMaxFileSize' => (Phpfox::getUserParam('petition.max_upload_size_petition') === 0 ? null : Phpfox::getLib('phpfox.file')->filesize((Phpfox::getUserParam('petition.max_upload_size_petition') / 1024) * 1048576)),
				)
			)
			->setEditor(array('wysiwyg' => true))
			->setHeader('cache',array(
				'petition.js'  => 'module_petition',
				'jquery/plugin/jquery.highlightFade.js' => 'static_script',
				'switch_legend.js' => 'static_script',
				'switch_menu.js' => 'static_script',
				'quick_edit.js' => 'static_script',
				'pager.css' => 'style_css',
				'progress.js' => 'static_script',
				'<script type="text/javascript">$Behavior.petitionProgressBarSettings = function(){ if ($Core.exists(\'#js_petition_block_photos_holder\')) { oProgressBar = {holder: \'#js_petition_block_photos_holder\', progress_id: \'#js_progress_bar\', uploader: \'#js_progress_uploader\', add_more: false, max_upload: ' . $iMaxUpload . ', total: 1, frame_id: \'js_upload_frame\', file_id: \'image[]\'}; $Core.progressBarInit(); } }</script>'
				)
			);

		//if (Phpfox::isModule('attachment') && Phpfox::getUserParam('petition.can_attach_on_petition'))
		$this->template()->setPhrase(array(
			'petition.signature_goal_must_be_a_integer_number',
			'petition.provide_a_valid_email_address'
		));
        if (Phpfox::isModule('attachment'))
		{
			$this->template()->assign(array('aAttachmentShare' => array(
					'type' => 'petition',
					'id' => 'core_js_petition_form',
					'edit_id' => ($bIsEdit ? $iEditId : 0),
					'inline'=>false
					)
				)
			);

			$this->setParam('attachment_share', array(
					'type' => 'petition',
					'id' => 'core_js_petition_form',
					'edit_id' => ($bIsEdit ? $iEditId : 0)
				)
			);






		}
		$aFilterMenu = array();

		if (!defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW'))
		{
			$aFilterMenu = array(
				Phpfox::getPhrase('petition.all_petitions') => '',
				Phpfox::getPhrase('petition.my_petitions') => 'my',
			);

			if (!Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend'))
			{
				$aFilterMenu[Phpfox::getPhrase('petition.friends_petitions')] = 'friend';
			}
			/*
        list($iTotalFeatured, $aFeatured) = Phpfox::getService('petition')->getFeatured();
        if ($iTotalFeatured)
        {
            $aFilterMenu[Phpfox::getPhrase('petition.featured_petitions') . '<span class="pending">' . $iTotalFeatured . '</span>'] = 'featured';
        }
        */
			if (Phpfox::getUserParam('petition.can_approve_petitions'))
			{
				$iPendingTotal = Phpfox::getService('petition')->getPendingTotal();

				if ($iPendingTotal)
				{
					$aFilterMenu[Phpfox::getPhrase('petition.pending_petitions') . (Phpfox::getUserParam('petition.can_approve_petitions') ? ' <span class="pending">' . $iPendingTotal . '</span>' : 0)] = 'pending';
				}
			}
			$aFilterMenu[Phpfox::getPhrase('petition.help')] =  'petition.help.view_help';

			$this->template()->buildSectionMenu('petition', $aFilterMenu);
		}
		(($sPlugin = Phpfox_Plugin::get('petition.component_controller_add_process')) ? eval($sPlugin) : false);
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
            $this->template()->clean(array(
				'bIsEdit',
				'aCategories'
			)
		);
		(($sPlugin = Phpfox_Plugin::get('petition.component_controller_add_clean')) ? eval($sPlugin) : false);
	}
}

?>
