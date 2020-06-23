<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Controller_Admincp_Help extends Phpfox_Component
{
	/**
	 * Class process method which is used to execute this component.
	 */
	public function process()
	{
		if (($iId = $this->request()->getInt('delete')))
		{
			if (Phpfox::getService('petition.help.process')->delete($iId))
			{
				$this->url()->send('admincp.petition.help', null, Phpfox::getPhrase('petition.petition_successfully_deleted'));
			}
		}
		
		$bIsEdit = false;
		if (($iEditId = $this->request()->getInt('id')))
		{
			$aRow = Phpfox::getService('petition.help')->getHelpForEdit($iEditId);
			$bIsEdit = true;
			$this->template()->assign(array('aForms' => $aRow));			
		}
		$iPage = $this->request()->getInt('page') ? $this->request()->getInt('page')  : 1;		
		$iLimit = 5;
	 	list($iTotal, $aHelps) = Phpfox::getService('petition.help')->get($iPage-1, $iLimit,'ASC');
		
		$aValidation = array(
			'title' => array(
				'def' => 'required',
				'title' => Phpfox::getPhrase('petition.fill_in_a_title_for_help')
			),
			'content' => array(
				'def' => 'required',
				'title' => Phpfox::getPhrase('petition.fill_in_a_content_for_your_help')
			)			
		);
				
		$oValid = Phpfox::getLib('validator')->set(array(
				'sFormName' => 'core_js_petition_form', 
				'aParams' => $aValidation
			)
		);
		
		
		if ($aVals = $this->request()->getArray('val'))
		{
			if ($oValid->isValid($aVals))
			{
				if($iId = Phpfox::getService('petition.help.process')->add($aVals)) //Add & Edit help
				{					
					$this->url()->send('admincp.petition.help', array() ,isset($aVals['help_id']) ? Phpfox::getPhrase('petition.petition_help_has_been_updated') : Phpfox::getPhrase('petition.your_petition_has_been_added'));					
				}
			}
		}
		
		Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $iTotal));
		
		$this->template()->setTitle(Phpfox::getPhrase('petition.helps'))
			->setBreadcrumb(Phpfox::getPhrase('petition.manage_helps'), $this->url()->makeUrl('admincp.petition.help'))
			->assign(array(
				'sCreateJs' => $oValid->createJS(),
				'sGetJsForm' => $oValid->getJsForm(),
				'aHelps' => $aHelps,
				'iMaxFileSize' => Phpfox::getParam('petition.help_icon_file_size_limit')
				)
			)
			->setEditor(array('wysiwyg' => 1))
			->setHeader(array(
					'drag.js' => 'static_script',
					'<script type="text/javascript">$Behavior.ynpHelp = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'petition.helpOrdering\'}); } </script>'		
				)
			);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_controller_admincp_help_clean')) ? eval($sPlugin) : false);
	}
}

?>