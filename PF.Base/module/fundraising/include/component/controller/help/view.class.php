<?php

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Controller_Help_View extends Phpfox_Component
{
	public function process()
	{		
		$iId = $this->request()->getInt('req3');

		$aItem = Phpfox::getService('fundraising.help')->getHelpForEdit($iId);
		
		if (!isset($aItem['help_id']))
		{			
			return Phpfox_Error::display(_p('the_fundraising_help_you_are_looking_for_cannot_be_found'));
		}
		$this->setParam(array('aHelp' => $aItem));
            
		$this->template()->setTitle($aItem['title'])
			->setBreadCrumb(_p('fundraisings_title'), $this->url()->makeUrl('fundraising.help'))
			->setBreadCrumb($aItem['title'], $this->url()->permalink('fundraising.help', $aItem['help_id'], $aItem['title']), true)
			->setMeta('description', $aItem['title'] . '.')
			->setMeta('description', $aItem['content'] . '.')
			->setMeta('keywords', $this->template()->getKeywords($aItem['title']))	
			->assign(array(
					'aItem' => $aItem,
					'bIsViewHelp' => true
				)
			);
	}

	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_controller_help_view_clean')) ? eval($sPlugin) : false);
	}
}

?>