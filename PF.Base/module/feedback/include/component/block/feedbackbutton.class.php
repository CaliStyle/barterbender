<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FeedBack
 * @version          2.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<?php
class FeedBack_Component_Block_FeedBackButton extends Phpfox_Component
{
	public function process()
	{
		if(phpfox::isAdminPanel())
		{
			return false;
		}
		$core_url= Phpfox::getParam('core.path');
		$this->template()
		->assign(array(
			'core_url' => $core_url,
		));
		$this->template()
		->setHeader(array(
							'jquery/plugin/jquery.highlightFade.js' => 'static_script',				
							'quick_edit.js' => 'static_script',	
							'pager.css' => 'style_css',
							'switch_legend.js' => 'static_script',
							'switch_menu.js' => 'static_script',
		                    'feedback.js' => 'module_feedback',
							'feed.js' => 'module_feed',
							'country.js' => 'module_core',
		));
		return;
	}
}
?>
