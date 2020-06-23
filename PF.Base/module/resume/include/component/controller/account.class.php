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
class Resume_Component_Controller_Account extends Phpfox_Component
{
	public function process()
	{
		// Edit mode
		Phpfox::isUser(true);
		$bIsEdit = false;
		$iEditId = 0;
		$convertgroup = false;
		$aAccount = Phpfox::getService("resume.account")->getAccount();
		$view_resume = -1;
		
		$itype = $this->request()->get("type");
		$itmptype = $itype;
		$user_group_id = Phpfox::getUserBy("user_group_id");
		$aCurrentInfo = Phpfox::getService("resume.account")->getInfoGroup($user_group_id);
		$aNewInfo = array();
		if(isset($aAccount['account_id']))
		{
			$iEditId = $aAccount['account_id'];
		}
		if($iEditId)
		{
			
			$bIsEdit = true;
			$aRow = Phpfox::getService("resume.account")->getAccount();
			
			$view_resume = $aRow['view_resume'];
			
			if($itype=="")
			{
				$itype = "employee";
				if($aRow['view_resume']==1)
					$itype="employer";
				if($aRow['view_resume']==2)
					$itype="employ";
			}
			else
			{
				if($itype=="employer" && $aRow['view_resume']==0)
				{
					$itype = "employ";
				}		
			}
			
			$new_group_id = Phpfox::getService("resume.account")->getUserGroupId($itype);		
			$aNewInfo = Phpfox::getService("resume.account")->getInfoGroup($new_group_id);	
			if($user_group_id!=$new_group_id)
				$convertgroup = true;		
			$this->template()->assign(array(
				'aForms' => $aRow,
				'aNewInfo' => $aNewInfo,
			));
		}
		else
		{
			if(Phpfox::isAdmin())
			{
				$itype="employ";
				$view_resume = 2;
			}
			$aForms = array('account_id'=>0);
			$this->template()->assign(array(
				'aForms' => $aForms,
			));
		}

        Phpfox::getService('resume')->buildSectionMenu();
		$this -> template() -> assign(array(
			'bIsEdit' => $bIsEdit,
			'aCurrentInfo' => $aCurrentInfo,
			'convertgroup' => $convertgroup,
			'type' => $itype,
			'url' => Phpfox::getLib("url")->makeUrl("resume"),
			'view_resume' => $view_resume,
			'itmptype' => $itmptype,
			'aAccount' => $aAccount,
		));
		$this -> template() ->setBreadcrumb(_p('resume.resume'),Phpfox::getLib("url")->makeUrl('resume'));
		
		
		$aValidation = array(
			'name' => array(
				'def' => 'required',
				'title' => _p('resume.add_full_name_to_your_account')
			),	
			'email' => array(
				'def' => 'required',
				'title' => _p('resume.add_email_to_your_account')
			),
		);
		
		if($itype=="employer" || $itype=="employ")
		{
			$aValidation['company_name'] = array(
				'def' => 'required',
				'title' => _p('resume.add_company_name_to_your_account')
			);
		}
		
		$oValid = Phpfox::getLib('validator')->set(array(
				'sFormName' => 'js_resume_account_form', 
				'aParams' => $aValidation
			)
		);
		
		if($aVals = $this->request()->getArray('val'))
		{
			if ($oValid->isValid($aVals))
			{
				if(!$bIsEdit)
				{
					$iId = Phpfox::getService("resume.account.process")->add($aVals);
					if($iId!=0)
					Phpfox::getLib("url")->send("resume.account"."/type_".$itype,array(),_p('resume.your_account_added_successfullly'));
				}
				else {
					$aVals['account_id'] = $iEditId;		
					if(Phpfox::getService("resume.account.process")->update($aVals))
					{
						if(isset($aAccount['view_resume']) && $aAccount['view_resume']==1 && $itype=="employee")
						{
							Phpfox::getLib("url")->send("resume.account",array(),_p('resume.your_account_updated_successfully'));
						}
						else {
						Phpfox::getLib("url")->send("resume.account"."/type_".$itype,array(),_p('resume.your_account_updated_successfully'));
						}
					}
				}
			}
		}
	}

	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('resume.component_controller_index_clean')) ? eval($sPlugin) : false);
		(($sPlugin = Phpfox_Plugin::get('resume.component_controller_account_clean')) ? eval($sPlugin) : false);
	}
}