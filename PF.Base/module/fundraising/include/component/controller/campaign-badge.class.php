<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Controller_Campaign_Badge extends Phpfox_Component
{
	public function process()	
	{
		Phpfox::getService('fundraising')->buildMenu();
		$iCampaignId = $this->request()->get('id');
		$iStatus = $this->request()->get('status', false);

		$this->template()->assign(array(
			'iStatus' => $iStatus,
			'iCampaignId' => $iCampaignId
		));
		$this->template()->setHeader('cache', array(
					'jquery/plugin/jquery.highlightFade.js' => 'static_script',
					'quick_edit.js' => 'static_script',
					'comment.css' => 'style_css',
					'pager.css' => 'style_css',
					'feed.js' => 'module_feed',
				)
		);
	}
}

?>
