<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');

class Contactimporter_Component_Controller_whyyouwereinvited extends Phpfox_Component
{
	public function process()
	{

		$path = $_SERVER['REQUEST_URI'];
		$encoded = explode('id=', $path);
		$encoded = isset($encoded[1]) ? $encoded[1] : "";
		$email = PREG_REPLACE("'([\S,\d]{2})'e", "chr(hexdec('\\1'))", $encoded);
		$checkMess = _p('you_were_invited_by');
		list($iC, $uInvited) = phpfox::getService('contactimporter') -> getUsersInvite($email);
		$this -> template() -> assign(array(
			'uInvites' => $uInvited,
			'path' => phpfox::getParam('core.path'),
			'checkMess' => $checkMess,
			'SignUp' => phpfox::getLib('url') -> makeUrl('user.register'),
			'login' => phpfox::getLib('url') -> makeUrl('user.login'),
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