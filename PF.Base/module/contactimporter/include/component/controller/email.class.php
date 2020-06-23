<?php

/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');

require_once (PHPFOX_DIR . 'module' . PHPFOX_DS . 'contactimporter' . PHPFOX_DS . 'include' . PHPFOX_DS . 'service' . PHPFOX_DS . 'abstract.class.php');

if (file_exists(PHPFOX_DIR . 'module' . PHPFOX_DS . 'contactimporter' . PHPFOX_DS . 'include' . PHPFOX_DS . 'component' . PHPFOX_DS . 'controller' . PHPFOX_DS . 'openinviter' . PHPFOX_DS . 'openinviter.php'))
{
	require_once (PHPFOX_DIR . 'module' . PHPFOX_DS . 'contactimporter' . PHPFOX_DS . 'include' . PHPFOX_DS . 'component' . PHPFOX_DS . 'controller' . PHPFOX_DS . 'openinviter' . PHPFOX_DS . 'openinviter.php');
}

class ContactImporter_Component_Controller_Email extends Phpfox_Component
{

	public function process()
	{
		Phpfox_Error::skip(false);

		$sProvider = $this -> request() -> get('req2');

		$iPage = $this -> request() -> get('page', 1);

		$iLimit = $iMaxInvitation = Phpfox::getService('contactimporter') -> getMaxInvitation();

		$iOffset = ($iPage - 1) * $iLimit;

		$oProvider = Phpfox::getService('contactimporter') -> getProvider($sProvider);

		list($iCnt, $aInviteLists, $aJoineds, $aErrors) = $oProvider -> get($iPage, $iLimit);

		if (isset($aErrors['login']) && $aErrors['login'])
		{
			$this -> url() -> send('contactimporter', null, $aErrors['login']);
			exit ;
		}
		if (isset($aErrors['contacts']) && $aErrors['contacts'])
		{
			$this -> url() -> send('contactimporter', null, $aErrors['contacts']);
			exit ;
		}

		$this -> template() -> setHeader(array(
			'contactimporter.js' => 'module_contactimporter',
		));
		$this -> template() -> assign(array(
			'iCnt' => $iCnt,
			'iLimit' => $iLimit,
			'iPage' => $iPage,
			'sProvider' => $sProvider,
			'aInviteLists' => $aInviteLists,
			'aJoineds' => $aJoineds,
			'aError' => $aErrors,
			'sCoreUrl' => Phpfox::getParam('core.path'),
			'sIniviteLink' => Phpfox::getLib('url') -> makeUrl('contactimporter.inviteuser', array('id' => Phpfox::getUserId())),
		));
		Phpfox::getLib('pager') -> set(array(
			'page' => $iPage,
			'size' => $iLimit,
			'count' => $iCnt
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
		))
		-> setHeader(array(
			'rtl.css' => 'module_contactimporter'
			));
	}

}
