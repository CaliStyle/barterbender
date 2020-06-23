<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');

class Contactimporter_Component_Controller_invitionknow extends Phpfox_Component
{

	public function process()
	{
		Phpfox::isUser(true);
		$iPage = $this -> request() -> getInt('page');
		$iLimit = 5;

		list($iCnt, $aUsers) = Phpfox::getService('contactimporter') -> getUserInvite($iPage, $iLimit);

		Phpfox::getLib('pager') -> set(array(
			'page' => $iPage,
			'size' => $iLimit,
			'count' => $iCnt
		));

		$this -> template() -> setTitle(_p('people_who_you_may_know')) -> setBreadcrumb(_p('people_who_you_may_know')) -> setHeader('cache', array('pager.css' => 'style_css')) -> assign(array('aUsers' => $aUsers));
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
