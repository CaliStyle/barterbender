<?php

/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          3.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');

class Contactimporter_Component_Controller_Queueemails extends Phpfox_Component
{

	public function process()
	{
		
		if (!Phpfox::isUser())
		{
			$path = phpfox::getParam('core.path') . 'user/login';
			$this -> url() -> send('login', null, _p('need_to_be_logged_in_for_showing_your_queue_emails'));
		}

		if ($iQueue = $this -> request() -> getInt('del'))
		{
			Phpfox::getService('contactimporter.process') -> deleteQueue((array)$iQueue);
			$this -> url() -> send('contactimporter.queueemails', null, _p('queue_delete_succesful'));
		}
		elseif ($sQueue = $this -> request() -> get('selectedVals'))
		{
			$aQueues = explode(',', $sQueue);
			Phpfox::getService('contactimporter.process') -> deleteQueue($aQueues);
			$this -> url() -> send('contactimporter.queueemails', null, _p('queue_delete_succesful'));
		}
		$iPage = $this -> request() -> getInt('page');
		
		$iPageSize = (int)Phpfox::getParam('contactimporter.how_many_pendings_to_show_per_page');
	
		list($iCnt, $aQueues) = Phpfox::getService('contactimporter') -> getQueue(array('iUser' => Phpfox::getUserId(), 'iPage' => $iPage, 'iPageSize' => $iPageSize, 'sType' => 'email'));
		Phpfox::getLib('pager') -> set(array(
			'page' => $iPage,
			'size' => $iPageSize,
			'count' => $iCnt,
            'paging_mode' => Phpfox::getParam('contactimporter.contactimporter_paging_mode', 'loadmore')
		));
		
		$this -> template() -> setTitle(_p('queue_emails')) -> setBreadcrumb(_p('queue_emails')) -> assign(array(
			'aQueues' => $aQueues,
			'iPage' => $iPage,
			'sCoreUrl' => Phpfox::getParam('core.path'),			
		)) -> setHeader(array(
			'pager.css' => 'style_css',
			'pending.js' => 'module_contactimporter',
			'rtl.css' => 'module_contactimporter',
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
        Phpfox::getService('contactimporter')->buildSectionMenu();
        $this->setParam('global_moderation', array(
                'name' => 'contactimporter_queueemail',
                'ajax' => 'contactimporter.moderationQueue',
                'menu' => [
                    [
                        'phrase' => _p('delete'),
                        'action' => 'delete'
                    ]
                ]
            )
        );
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('invite.component_controller_invitations_clean')) ? eval($sPlugin) : false);
	}

}
?>