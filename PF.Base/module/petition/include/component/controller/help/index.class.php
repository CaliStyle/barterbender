<?php

defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Controller_Help_Index extends Phpfox_Component
{
	public function process()
	{		
		//View a help
		if($this->request()->getInt('req3') > 0)
		{
			return Phpfox::getLib('module')->setController('petition.help.view');						
		}
		else //List all help
		{
			$sView = $this->request()->get('view');
		
			if($sView != 'help')
			{
				$this->url()->send('petition', array('view' => $sView));
				return false;
			}
			$this->template()->assign(array('sView' => $sView ));

			if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
				$bIsProfile = true;
				$aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
				$this->setParam('aUser', $aUser);
			} else {
				$bIsProfile = $this->getParam('bIsProfile');
				if ($bIsProfile === true) {
					$aUser = $this->getParam('aUser');
				}
			}

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
				
				if (Phpfox::getUserParam('petition.can_approve_petitions'))
				{
					$iPendingTotal = Phpfox::getService('petition')->getPendingTotal();
					
					if ($iPendingTotal)
					{
						$aFilterMenu[Phpfox::getPhrase('petition.pending_petitions') . (Phpfox::getUserParam('petition.can_approve_petitions') ? '<span class="pending">' . $iPendingTotal . '</span>' : 0)] = 'pending';
					}
				}
				$aFilterMenu[Phpfox::getPhrase('petition.help')] =  'petition.help.view_help';
				
				$this->template()->buildSectionMenu('petition', $aFilterMenu);
			}
			
			$iPage = $this->request()->getInt('page') ? $this->request()->getInt('page')  : 1;		
			$iLimit = 10;
			list($iTotal, $aItems) = Phpfox::getService('petition.help')->get($iPage-1, $iLimit);
			
			Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $iTotal));
			
			foreach ($aItems as $iKey => $aItem)
			{
				$str = $aItems[$iKey]['content_parsed'];
				 $aItems[$iKey]['content_parsed'] =mb_substr($str,0,280)."...";
				$this->template()->setMeta('keywords', $this->template()->getKeywords($aItem['title']));					
			}

			$this->template()
				->setBreadCrumb(($bIsProfile ? Phpfox::getPhrase('petition.petitions') : Phpfox::getPhrase('petition.petition_title')), ($bIsProfile ? $this->url()->makeUrl($aUser['user_name'], 'petition') : $this->url()->makeUrl('petition')))
				->assign(array(
						'aHelps' => $aItems,
						'bIsViewHelp' => false,
						'iPage' => $iPage
					)
				)
				->setHeader('cache', array(
					'pager.css' => 'style_css'
				)
			);
		}
	}

	public function clean()
	{
            $this->template()->clean(array(
				'aItems'
			)
		);
		(($sPlugin = Phpfox_Plugin::get('petition.component_controller_help_index_clean')) ? eval($sPlugin) : false);
	}
}

?>