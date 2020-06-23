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
class Resume_Component_Controller_Publication extends Phpfox_Component
{
	public function process()
	{
		$iId = $this->request()->get("id");
		// User login requirement
		Phpfox::isUser(true);
		// Edit mode
		$bIsEdit = false;
		//Init variable
		$aRows = array();
		$pub_id = 0;
		
		if($iEditId = $this->request()->getInt("id"))
		{
			$bIsEdit = true;
			$aRows = Phpfox::getService("resume.publication")->getAllPublication($iEditId);
			$aBasic = Phpfox::getService("resume.basic")->getBasicInfo($iEditId);
			if($aBasic['user_id']!=Phpfox::getUserId())
			{
				if(!Phpfox::getUserParam('resume.can_edit_other_resume'))
					Phpfox::getLib("url")->send("subscribe");
			}
			if($pub_id = $this->request()->getInt("exp"))
			{
				$aExp = Phpfox::getService("resume.publication")->getPublication($pub_id);
				
				if(count($aExp)==0)
					Phpfox::getLib("url")->send("resume.publication",array('id' => $iEditId),'This publication is not found!');
				
				if(Phpfox::getLib('parse.format')->isSerialized($aExp['author']))
				{
					$sAuthorList = unserialize($aExp['author']);
					$aExp['author_list'] = $sAuthorList;
					$aExp['array_author_list']= explode(",", $sAuthorList);
				}
				$this->template()->assign(array(
					'aForms' => $aExp,
				));			
			}
            Phpfox::getService('resume')->setPageSectionMenu($aBasic);
		}
		
		if($iId==0)
		{
			Phpfox::getLib("url")->send("resume.add");
		}
		
		$aValidation = array(
			'title' => array(
				'def' => 'required',
				'title' => _p('resume.add_publication_title_to_your_resume')
			),	
		);
		
		$oValid = Phpfox::getLib('validator')->set(array(
				'sFormName' => 'js_resume_add_form', 
				'aParams' => $aValidation
			)
		);
		
		// Inite day to show
		$sDay = array();
		for($i = 1; $i <= 31; $i++)
		{
			$aDay[] = $i;
		}
		//init month to show
		$aMonth = array();
		for($i=1;$i<=12;$i++)
		{
			$aMonth[] = $i;
		}
		//init year to show
		$aYear = array();
		for($i=(int) date('Y') ; $i >= 1900 ; $i--)
		{
			$aYear[] = $i;
		}
		
        $this->template()->assign(array(
        	'aDay'	 => $aDay,
			'aMonth' => $aMonth,
			'aYear'	 =>	$aYear,
			'id' 	 => $iEditId,
			'iExp' 	 => $pub_id,
			'bIsEdit'=> $bIsEdit,
			'aRows'  => $aRows,
			'typesession' => Phpfox::getService("resume.process")->typesesstion($iEditId),
		))
		->setHeader(array(	
			'process_add.js' => 'module_resume',
			'resume.js' => 'module_resume'
		))
       	->setBreadcrumb(_p('resume.resume'), $this->url()->makeUrl('resume'))
		->setBreadcrumb((!empty($iEditId) ? _p('resume.editing_resume') . ': ' . Phpfox::getLib('parse.output')->shorten($aBasic['full_name'], Phpfox::getService('core')->getEditTitleSize(), '...') : _p('resume.create_new_resume')), ($iEditId > 0 ? $this->url()->makeUrl('resume', array('add', 'id' => $iEditId)) : $this->url()->makeUrl('resume', array('add'))), true);
		
		if ($aVals = $this->request()->getArray('val'))
		{
			$bIsFilledType = TRUE;
			if ($aVals['type_id'] == 0 && !$aVals['other_type'])
			{
				$bIsFilledType = FALSE;
				Phpfox_Error::set(_p('resume.add_publication_type_to_your_resume'));
			}
			
			if ($oValid->isValid($aVals) && $bIsFilledType)
			{
				if($pub_id==0)
				{
					$aVals['resume_id'] = $iId;
					if(Phpfox::getService("resume.publication.process")->add($aVals))
					{
						Phpfox::getService('resume')->updateStatus($iEditId);	
						Phpfox::getLib("url")->send("resume.publication",array('id' => $iId),_p('resume.your_publication_added_successfully'));
					}
				}
				else {
					$aVals['pub_id'] = $pub_id;
					if(Phpfox::getService("resume.publication.process")->update($aVals))
					{
						Phpfox::getService('resume')->updateStatus($iEditId);
						Phpfox::getLib("url")->send("resume.publication",array('id' => $iId,'exp'=>$pub_id),_p('resume.your_publication_updated_successfully'));
					}
				}
			}
			else
			{
				if($aVals['author_list'])
				{
					$aVals['array_author_list'] = explode(',', $aVals['author_list']);	
				}
				else 
				{
					$aVals['array_author_list'] = array();
				}
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