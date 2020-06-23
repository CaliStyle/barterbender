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
class Resume_Component_Controller_Add extends Phpfox_Component
{
	public function process()
	{
		// User login requirement
		Phpfox::isUser(true);
		// Edit mode
		$bIsEdit = false;
		
		// Setup some variables for layout block displays 
		$iMinPredefined = 1;
        $iMaxPredefined = 5;
		$iMinPredefined_imail = 1;
        $iMaxPredefined_imail = 5;
		$iMinPredefined_phone = 1;
        $iMaxPredefined_phone = 5;
		
        $oSetting = Phpfox::getService("resume.setting");
        $aPers = $oSetting->getAllPermissions();
        
		$numberofresume = Phpfox::getService("resume.basic")->getItemCount('rbi.user_id='.Phpfox::getUserId());
		$total_allowed = Phpfox::getUserParam("resume.maximum_resumes");
		$is_import = true;
		if($total_allowed > 0 && $numberofresume >= $total_allowed)
		{
			$is_import = false;
		}
		if ($iEditId = $this->request()->getInt("id"))
		{
			$bIsEdit = true;
			$aRow = Phpfox::getService("resume.basic")->getBasicInfo($iEditId);
            $aRow['birth_day_full'] = date('F j, o', (int) $aRow['birthday_search']);
			$aRow['gender_phrase'] = Phpfox::GetService('resume')->getGenderPhrase($aRow['gender']);
			$aRow['marital_status_phrase'] = Phpfox::GetService('resume')->getMaritalStatusPhrase($aRow['marital_status']);
			
			if ($aRow['user_id'] != Phpfox::getUserId())
            {
				if(!Phpfox::getUserParam('resume.can_edit_other_resume'))
                {
                    $this->url()->send("subscribe");
                }
			}
			if(!isset($aRow['resume_id']))
			{
				$this->url()->send("resume.add");
			}
			$this->template()->assign(array(
				'aForms' => $aRow,
			));

            Phpfox::getService('resume')->buildSectionMenu();
		}
		else
        {
			$aInfoUser = Phpfox::getService('resume')->getExtraInfo();
            
            // Default is synchronize.
            $aInfoUser['is_synchronize'] = 1;
            $aInfoUser['display_date_of_birth'] = 1;
            $aInfoUser['display_gender'] = 1;
            $aInfoUser['display_marital_status'] = 1;
            $aInfoUser['city'] = $aInfoUser['city_location'];
            $aInfoUser['zip_code'] = $aInfoUser['postal_code'];
            $aInfoUser['birth_day_full'] = date('F j, o', (int) $aInfoUser['birthday_search']);
            $aInfoUser['gender_phrase'] = Phpfox::GetService('resume')->getGenderPhrase($aInfoUser['gender']);
            $aInfoUser['marital_status_phrase'] = Phpfox::GetService('resume')->getMaritalStatusPhrase($aInfoUser['marital_status']);
            
			$this->template()->assign(array(
				'aForms' => $aInfoUser
			));		
		
			if(!Phpfox::getUserParam('resume.can_create_resumes'))
            {
                $this->url()->send("subscribe");
            }
				
		}
		
		$aValidation = array(
			'full_name' => array(
				'def' => 'required',
				'title' => _p('resume.add_full_name_to_your_resume')
			),
            'gender' => array(
                'def' => 'required',
                'title' => 'Add gender to your resume!'
            )
		);
        
		$oValid = Phpfox::getLib('validator')->set(array(
				'sFormName' => 'js_resume_add_form', 
				'aParams' => $aValidation
			)
		);
		
		
		$sOutJs = '';
		$aCustomFields = Phpfox::getService('resume.custom')->getFields($iEditId);
		foreach($aCustomFields as $iKey => $aField)
        {
            if($aField['is_required'] == 1)
            {
                $aRequired[] = '{"field_name":"'.$aField['field_name'].'", "phrase_name":"'._p($aField['phrase_var_name']).'","var_type":"'.$aField['var_type'].'"}';
            }
        }
		if(isset($aRequired))
        	$sOutJs = '[' . join(',', $aRequired) . ']';
        $this->setParam('aCustomFields',$aCustomFields);
	
		
		
		// Assign Variables, Set header and phrases
        if ($bIsEdit && !empty($aRow)) {
		    Phpfox::getService('resume')->setPageSectionMenu($aRow);
        }
        $this->template()->assign(array(
        	'sDobStart' => Phpfox::getParam('user.date_of_birth_start'),
			'sDobEnd' => Phpfox::getParam('user.date_of_birth_end'),
			'id' => $iEditId,
			'bIsEdit' => $bIsEdit,
			'sCreateJs' => $oValid->createJS(),
			'total_allowed' => $total_allowed,
			'sOutJs' => $sOutJs,
			'is_import' => $is_import,
			'typesession' => Phpfox::getService("resume.process")->typesesstion($iEditId),
            'aPers' => $aPers
		))
		->setHeader(array(				
			'add.js' => 'module_resume',
			'progress.js' => 'static_script',
			'resume.js' => 'module_resume',
			'<script type="text/javascript">$Behavior.setMinPredefined = function() {iMaxPredefined_phone = ' . $iMaxPredefined_phone . '; iMinPredefined_phone = ' . $iMinPredefined_phone . ';iMaxPredefined = ' . $iMaxPredefined . '; iMinPredefined = ' . $iMinPredefined . ';iMaxPredefined_imail = ' . $iMaxPredefined_imail . '; iMinPredefined_imail = ' . $iMinPredefined_imail . ';}</script>',
		))
		->setPhrase(array(
					'resume.you_must_have_a_minimum_of_total_predefined',
					'resume.you_reach_the_maximum_of_total_predefined',
				)
		)	

		->setBreadcrumb(_p('resume.resume'), $this->url()->makeUrl('resume'))
		->setBreadcrumb((!empty($iEditId) ? _p('resume.editing_resume') . ': ' . Phpfox::getLib('parse.output')->shorten($aRow['full_name'], Phpfox::getService('core')->getEditTitleSize(), '...') : _p('resume.create_new_resume')), ($iEditId > 0 ? $this->url()->makeUrl('resume', array('add', 'id' => $iEditId)) : $this->url()->makeUrl('resume', array('add'))), true);
		
		$this->template()->setPhrase(array(
            "resume.the_field_field_name_is_required"
        ));
		
		// Add/Edit resume basic info process		
		if ($aVals = $this->request()->getArray('val'))
		{

			$is_validate = true;
			if ($oValid->isValid($aVals))
			{
				$is_validate = false;
				
				if (!$bIsEdit)
				{
					$image = $this->request()->get("image");
					$iId = Phpfox::getService("resume.basic.process")->add($aVals);

					if ($iId != 0)
					{
						$this->url()->send("resume.summary",array('id' => $iId),
							_p('resume.your_basic_information_added_successfully'));
					}
					else
                    {
                        $is_validate = true;
                    }	
				}
				else 
				{
					$image = $this->request()->get("image");
					$aVals['resume_id'] = $iEditId;

					if(Phpfox::getService("resume.basic.process")->update($aVals))
					{
						Phpfox::getService('resume')->updateStatus($iEditId);
						$this->url()->send("resume.summary",array('id' => $iEditId),_p('resume.your_basic_information_updated_successfully'));
					}
					else
					{
						$is_validate = true;
					}
				}
			}
			if($is_validate)
			{
				// Re-correct data style to display on form again
				// Email
				$aVals['email'] = $aVals['emailaddress'];
				
				// Instant Message
				$aVals['imessage'] = array();
				if(count($aVals['homepage'])>0)
				{
					$homestyle =array();
					foreach($aVals['homepage'] as $key=>$homepage)
					{
						$homestyle[$key]['text'] = $homepage;
						$homestyle[$key]['type'] = $aVals['homepagestyle'][$key];
					}
					if(count($homestyle)>0)
					{
						$aVals['imessage'] = $homestyle;
					}
				}
				
				// Phone
				if(count($aVals['phone'])>0)
				{
					$phonestyle = array();
					foreach($aVals['phone'] as $key=>$phone)
					{
						$phonestyle[$key]['text'] = $phone;
						$phonestyle[$key]['type'] = $aVals['phonestyle'][$key];
					}
					if(count($phonestyle)>0)
					{
						$aVals['phone'] = $phonestyle;
					}
				}
				else {
					$aVals['phone'] = null;
				}
				
				// Image Picture
				if($bIsEdit)
				{
					$aVals['server_id'] = $aRow['server_id'];
					$aVals['image_path'] = $aRow['image_path'];
				}
				$aInfoUser = Phpfox::getService('resume')->getExtraInfo();   
                $aVals = array_merge($aVals,$aInfoUser);	
				$this->template()->assign(array(
				'aForms' => $aVals,
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