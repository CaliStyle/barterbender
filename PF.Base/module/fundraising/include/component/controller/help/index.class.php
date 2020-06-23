<?php

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Controller_Help_Index extends Phpfox_Component
{
	public function process()
	{		
		//View a help
		if($this->request()->getInt('req3') > 0)
		{
			return Phpfox::getLib('module')->setController('fundraising.help.view');						
		}
		else //List all help
		{
			$sView = $this->request()->get('view');
		
			if($sView != 'help')
			{
				$this->url()->send('fundraising', array('view' => $sView));
				return false;
			}
			$this->template()->assign(array('sView' => $sView ));
		
			$aFilterMenu = array();
			
			if (!defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW'))
			{
				$aFilterMenu = array(
					_p('all_fundraisings') => '',
					_p('my_fundraisings') => 'my',
				);
				
				if (!Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend'))
				{
					$aFilterMenu[_p('friends_fundraisings')] = 'friend';
				}
				
				if (Phpfox::getUserParam('fundraising.can_approve_campaigns'))
				{
					$iPendingTotal = Phpfox::getService('fundraising')->getPendingTotal();
					
					if ($iPendingTotal)
					{
						$aFilterMenu[_p('pending_fundraisings') . (Phpfox::getUserParam('fundraising.can_approve_campaigns') ? '<span class="pending">' . $iPendingTotal . '</span>' : 0)] = 'pending';
					}
				}
				$aFilterMenu[_p('help')] =  'fundraising.help.view_help';
				
				$this->template()->buildSectionMenu('fundraising', $aFilterMenu);
			}
			
			$iPage = $this->request()->getInt('page') ? $this->request()->getInt('page')  : 1;		
			$iLimit = 10;
			list($iTotal, $aItems) = Phpfox::getService('fundraising.help')->get($iPage-1, $iLimit);
			
			Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $iTotal));
			
			foreach ($aItems as $iKey => $aItem)
			{
				$this->template()->setMeta('keywords', $this->template()->getKeywords($aItem['title']));					
			}

			$this->template()->setTitle(_p('helps'))
				->setBreadCrumb(_p('helps'), $this->url()->makeUrl('fundraising.help'), true)
				->assign(array(
						'aHelps' => $aItems,
						'bIsViewHelp' => false
					)
				)
				->setHeader( array(
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
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_controller_help_index_clean')) ? eval($sPlugin) : false);
	}
}

?>