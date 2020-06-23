<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Controller_Profile extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$this->setParam('bIsProfile', true);
		$aUser = $this->getParam('aUser');
		
		$this->template()->setMeta('keywords', _p('full_name_s_fundraisings', array('full_name' => $aUser['full_name'])));
		$this->template()->setMeta('description', _p('full_name_s_fundraisings_on_site_title', array('full_name' => $aUser['full_name'], 'site_title' => Phpfox::getParam('core.site_title'))));
		
		Phpfox::getComponent('fundraising.index', array('bNoTemplate' => true), 'controller');
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_controller_profile_clean')) ? eval($sPlugin) : false);
	}
}

?>
