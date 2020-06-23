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
class Resume_Component_Controller_Skill extends Phpfox_Component
{
	public function process()
	{
		$iId = $this->request()->get("id");
		
		// Edit mode
		$bIsEdit = false;
		
		$aMonth = array();
		for($i=1;$i<=12;$i++)
			$aMonth[] = $i;
		$iEditId = 0;
		if($iId==0)
		{
			Phpfox::getLib("url")->send("resume.add");
		}
		
		if($iId!=0)
		{
			$iEditId = $iId;
			$aRow = Phpfox::getService("resume.skill")->getBasicSkill($iId);
			$aBasic = Phpfox::getService("resume.basic")->getBasicInfo($iEditId);
			if($aBasic['user_id']!=Phpfox::getUserId())
			{
				if(!Phpfox::getUserParam('resume.can_edit_other_resume'))
					Phpfox::getLib("url")->send("subscribe");
			}
			$aRow['kill_list']="";
			if(Phpfox::getLib('parse.format')->isSerialized($aRow['skills']))
			{
				$kill_list = unserialize($aRow['skills']);
				$aRow['kill_list']= $kill_list;
				$akill = explode(",", $kill_list);
				$aRow['akill_list']= $akill;
			}
			
			$bIsEdit = true;
			$this->template()->assign(array(
				'aForms' => $aRow,
				)
			);
            Phpfox::getService('resume')->setPageSectionMenu($aBasic);
		}
       	$this->template()->assign(array(
			'aMonth' => $aMonth,
			'id' => $iId,
			'bIsEdit' => $bIsEdit,
			'core_path' => Phpfox::getParam("core.path"),
			'typesession' => Phpfox::getService("resume.process")->typesesstion($iId),
		))
		->setHeader(array(				
			'process_add.js' => 'module_resume',
			'resume.js' => 'module_resume',
		))
              	->setBreadcrumb(_p('resume.resume'), $this->url()->makeUrl('resume'))
		->setBreadcrumb((!empty($iEditId) ? _p('resume.editing_resume') . ': ' . Phpfox::getLib('parse.output')->shorten($aBasic['full_name'], Phpfox::getService('core')->getEditTitleSize(), '...') : _p('resume.create_new_resume')), ($iEditId > 0 ? $this->url()->makeUrl('resume', array('add', 'id' => $iEditId)) : $this->url()->makeUrl('resume', array('add'))), true);
		
		if ($aVals = $this->request()->getArray('val'))
		{
			$aVals['resume_id'] = $iId;
			Phpfox::getService("resume.skill.process")->updateBasicSkill($aVals);
			Phpfox::getService('resume')->updateStatus($iId);
			Phpfox::getLib("url")->send("resume.skill",array('id' => $iId),_p('resume.your_skill_expertise_updated_successfully'));
		}
	}
	
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('resume.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}

?>