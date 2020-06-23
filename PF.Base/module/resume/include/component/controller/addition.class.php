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
class Resume_Component_Controller_Addition extends Phpfox_Component
{
	public function process()
	{
		$iId = $this->request()->get("id");
		// Edit mode
		$bIsEdit = false;
		$iEditId = 0;
		
		if($iId==0)
		{
			Phpfox::getLib("url")->send("resume.add");
		}
		
		if($iId!=0)
		{
			$aRow = Phpfox::getService("resume.addition")->getAddition($iId);
			$iEditId = $iId;
			$aBasic = Phpfox::getService("resume.basic")->getBasicInfo($iEditId);
			if($aBasic['user_id']!=Phpfox::getUserId())
			{
				if(!Phpfox::getUserParam('resume.can_edit_other_resume'))
					Phpfox::getLib("url")->send("subscribe");
			}
			$bIsEdit = true;
				$this->template()->assign(array(
						'aForms' => $aRow,
					)
				);
            Phpfox::getService('resume')->setPageSectionMenu($aBasic);
		}
		
		$iMinPredefined = 1;
        $iMaxPredefined = 5;
        $this->template()->assign(array(
        	'sDobStart' => Phpfox::getParam('user.date_of_birth_start'),
			'sDobEnd' => Phpfox::getParam('user.date_of_birth_end'),
			'id' => $iId,
			'bIsEdit' => $bIsEdit,
			'typesession' => Phpfox::getService("resume.process")->typesesstion($iId),
		))
		->setHeader(array(				
			'add.js' => 'module_resume',
			'resume.js' => 'module_resume',
			'<script type="text/javascript">$Behavior.setMinPredefined = function() {iMaxPredefined = ' . $iMaxPredefined . '; iMinPredefined = ' . $iMinPredefined . ';}</script>',
		))
		->setPhrase(array(
					'resume.you_must_have_a_minimum_of_total_predefined',
					'resume.you_reach_the_maximum_of_total_predefined',
				)
		)
       	->setBreadcrumb(_p('resume.resume'), $this->url()->makeUrl('resume'))
		->setBreadcrumb((!empty($iEditId) ? _p('resume.editing_resume') . ': ' . Phpfox::getLib('parse.output')->shorten($aBasic['full_name'], Phpfox::getService('core')->getEditTitleSize(), '...') : _p('resume.create_new_resume')), ($iEditId > 0 ? $this->url()->makeUrl('resume', array('add', 'id' => $iEditId)) : $this->url()->makeUrl('resume', array('add'))), true);
        
		if ($aVals = $this->request()->getArray('val'))
		{
			if(!isset($aRow['resume_id']))
			{
				$bIsEdit = 0;
			}
			if($bIsEdit==0)
			{
				$aVals['resume_id'] = $iId;
				if(Phpfox::getService("resume.addition.process")->add($aVals))
				{
					Phpfox::getService('resume')->updateStatus($iId);
					Phpfox::getLib("url")->send("resume",array('view'=>'my'),_p('resume.your_addition_information_added_successfully'));
				}
			}
			else {
				$aVals['resume_id'] = $iId;
				if(Phpfox::getService("resume.addition.process")->update($aVals))
				{
					Phpfox::getService('resume')->updateStatus($iId);
					Phpfox::getLib("url")->send("resume.addition",array('id'=>$iId),_p('resume.your_addition_information_updated_successfully'));
				}
			}
		}		
	}
	
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('resume.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}

?>