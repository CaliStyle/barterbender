<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 * 
 */
class Resume_Component_Controller_Profile extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$this->setParam('bIsProfile', true);
		
		$aUser = $this->getParam('aUser');
		
		$iViewerId = Phpfox::getUserId();
		
		if($iViewerId == $aUser['user_id'] || Phpfox::getUserParam("resume.can_delete_other_resumes"))
		{
			$this->setParam('global_moderation', array(
						'name' => 'resume',
						'ajax' => 'resume.moderation',
						'menu' => array(
							array(
								'phrase' => _p('resume.delete'),
								'action' => 'delete'
							)
						)
			));
		}
		$this->template()->setMeta('keywords', _p('resume.full_name_s_resumes', array('full_name' => $aUser['full_name'])));
		$this->template()->setMeta('description', _p('resume.full_name_s_resumes_on_site_title', array('full_name' => $aUser['full_name'], 'site_title' => Phpfox::getParam('core.site_title'))));
		
		Phpfox::getComponent('resume.index', array('bNoTemplate' => true), 'controller');
	}
	
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('resume.component_controller_index_clean')) ? eval($sPlugin) : false);
		(($sPlugin = Phpfox_Plugin::get('resume.component_controller_profile_clean')) ? eval($sPlugin) : false);
	}	
}

?>