<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		younet
 * @package 		Phpfox_Component
 * @version 		3.01
 */
class Resume_Component_Controller_Language extends Phpfox_Component
{
	public function process()
	{
		
		// User login requirement
		Phpfox::isUser(true);
		// Edit mode
		$bIsEdit = false;
		//Init variable
		$aRows = array();
		$lang_id = 0;
		
		$iId = $this->request()->get("id");
		
		if($iEditId = $this->request()->getInt("id"))
		{
			$bIsEdit = true;
			$aRows = Phpfox::getService("resume.language")->getAllLanguage($iEditId);
			$aBasic = Phpfox::getService("resume.basic")->getBasicInfo($iEditId);
			if($aBasic['user_id']!=Phpfox::getUserId())
			{
				if(!Phpfox::getUserParam('resume.can_edit_other_resume'))
					Phpfox::getLib("url")->send("subscribe");
			}
			if($lang_id = $this->request()->getInt("exp"))
			{
				$aExp = Phpfox::getService("resume.language")->getLanguage($lang_id);
				$this->template()->assign(array(
					'aForms' => $aExp,
				));			
			}
            Phpfox::getService('resume')->setPageSectionMenu($aBasic);
		}
		
		$aValidation = array(
			'name' => array(
				'def' => 'required',
				'title' => _p('resume.add_language_name_to_your_resume')
			),	
		);
		
		$oValid = Phpfox::getLib('validator')->set(array(
				'sFormName' => 'js_resume_add_form', 
				'aParams' => $aValidation
			)
		);
		
		if($iId==0)
		{
			Phpfox::getLib("url")->send("resume.add");
		}
		
    	$this->template()->assign(array(
    		'id' => $iEditId,
			'iExp' => $lang_id,
			'bIsEdit' => $bIsEdit,
			'aRows' => $aRows,
    		'typesession' => Phpfox::getService("resume.process")->typesesstion($iEditId),
		))
		->setHeader(array(	
			'resume.js' => 'module_resume'
		))
        ->setBreadcrumb(_p('resume.resume'), $this->url()->makeUrl('resume'))
		->setBreadcrumb((!empty($iEditId) ? _p('resume.editing_resume') . ': ' . Phpfox::getLib('parse.output')->shorten($aBasic['full_name'], Phpfox::getService('core')->getEditTitleSize(), '...') : _p('resume.create_new_resume')), ($iEditId > 0 ? $this->url()->makeUrl('resume', array('add', 'id' => $iEditId)) : $this->url()->makeUrl('resume', array('add'))), true);

		if ($aVals = $this->request()->getArray('val'))
		{
			if ($oValid->isValid($aVals))
			{
				if($lang_id==0)
				{
					$aVals['resume_id'] = $iEditId;
					Phpfox::getService("resume.language.process")->add($aVals);
					Phpfox::getService('resume')->updateStatus($iEditId);
					Phpfox::getLib("url")->send("resume.language",array('id' => $iId),_p('resume.your_language_added_successfully'));
				}
				else {
					$aVals['lang_id'] = $lang_id;
					Phpfox::getService("resume.language.process")->update($aVals);
					Phpfox::getService('resume')->updateStatus($iEditId);
					Phpfox::getLib("url")->send("resume.language",array('id' => $iId,'exp' =>$lang_id),_p('resume.your_language_updated_successfully'));
				}
			}
			else
			{
				$this->template()->assign(array(
				'aForms' => $aVals
				));
			}
		} 
	}
	
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('resume.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}

?>