<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Controller_Profile extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{		
		$this->setParam('bIsProfile', true);
		
		$aUser = $this->getParam('aUser');
		
		$this->template()->setMeta('keywords', Phpfox::getPhrase('petition.full_name_s_petitions', array('full_name' => $aUser['full_name'])));
		$this->template()->setMeta('description', Phpfox::getPhrase('petition.full_name_s_petitions_on_site_title', array('full_name' => $aUser['full_name'], 'site_title' => Phpfox::getParam('core.site_title'))));
		
		Phpfox::getComponent('petition.index', array('bNoTemplate' => true), 'controller');
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_controller_profile_clean')) ? eval($sPlugin) : false);
	}
}

?>
