<?php

/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');

class Contactimporter_Component_Controller_Addcontact extends Phpfox_Component
{

	public function process()
	{
		/**
		 * skip this check
		 */
		//if (!Phpfox::isUser()){	return Phpfox_Error::display(_p('need_to_be_logged_in'));}

		$request = $this -> request();
		$sProvider = $request -> get("provider_box");
		$sMessage = $request -> get("message");
		$sContacts = $request -> get("contacts");
		$aContacts = explode(',', $sContacts);

		$iUserId = Phpfox::getUserId();
		$sSubject = NULL;
		$aErrors = array();
		$iMaxInvitation = Phpfox::getService('contactimporter') -> getMaxInvitation();
		$oi_session_id = $request -> get('oi_session_id');

		if (empty($sContacts))
		{
			$aErrors[] = _p('you_haven_t_selected_any_contacts_to_invite') . '!';
		}
		else
		if ($oi_session_id != 0)
		{
			Phpfox::getService('contactimporter.process') -> sendInvite($sProvider, $iUserId, $aContacts, $sSubject, $sMessage, $sLink = NULL);
		}

		$this -> template() -> assign(array(
			'aErrors' => $aErrors,
			'contactimporter_link' => phpfox::getLib('url') -> makeUrl('contactimporter'),
			'homepage' => phpfox::getParam('core.path'),
		));

		$this -> template() -> setPhrase(array(
			'contactimporter.are_you_sure_you_want_to_delete',
			'contactimporter.are_you_sure_you_want_to_delete_this_action_will_delete_all_feeds_belong_to',
			'contactimporter.you_can_send',
			'contactimporter.invitations_per_time',
			'contactimporter.you_have_selected',
			'contactimporter.contacts',
			'contactimporter.select_current_page',
			'contactimporter.unselect_current_page',
			'contactimporter.your_email_is_empty',
			'contactimporter.this_mail_domain_is_not_supported',
			'contactimporter.email_should_not_be_left_blank',
			'contactimporter.no_contacts_were_selected',
			'contactimporter.updating'
		));
	}

}
