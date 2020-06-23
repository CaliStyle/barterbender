<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');

class Contactimporter_Component_Controller_Export extends Phpfox_Component
{
	public function process()
	{
		if (!Phpfox::isUser())
		{
			$this -> url() -> send('login', null, _p('need_to_be_logged_in_for_exporting_your_friend_contacts') . '.');
		}

		if ($this -> request() -> get('option'))
		{
			$error = phpfox::getService('contactimporter.export') -> exportCSV(phpfox::getUserId());
			if ($error != '')
			{
				$this -> url() -> send('contactimporter.export', null, $error);
			}
		}
		$this -> template() -> setBreadcrumb(_p('breadcrumb_contactimporter_title'));
		$this -> template() -> assign(array('url' => phpfox::getLib('url') -> makeUrl('contactimporter.export', array('option' => 'export')), ));
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
		))
		-> setHeader(array(
			'pager.css' => 'style_css',
			'pending.js' => 'module_contactimporter',
			'rtl.css' => 'module_contactimporter',
		));
        Phpfox::getService('contactimporter')->buildSectionMenu();
	}

}