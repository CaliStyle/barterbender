<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		VuDP, AnNT
 * @package  		Module_jobposting
 */

class Jobposting_Component_Controller_Add extends Phpfox_Component 
{
 	private function _getValidationParams($aVals = array()) {

        $aParam = array(
            'title' => array(
                'def' => 'required',
                'title' => _p('fill_in_a_title_for_your_job'),
            ),
            'description' => array(
                'def' => 'required',
                'title' => _p('add_some_content_to_your_description'),
            ),
            'skills' => array(
                'def' => 'required',
                'title' => _p('add_some_content_to_your_skills'),
            ),
        );

        return $aParam;
    }
	
	private function payPackage($aVals, $iId)
    {
    	
        if (isset($aVals['publish']) && $aVals['publish'])
        {
            if (isset($aVals['packages']) && $aVals['packages'])
            {
                $iPackage = $aVals['packages'];
                $featureJob = (isset($aVals['feature']) && $aVals['feature']) ? $iId : 0;
                $sUrl = Phpfox::getLib('url')->permalink('jobposting', $iId, $aVals['title']);
                
                if ($aVals['paypal'] == 0) //select existing packages
                {
                    $aPackage = Phpfox::getService('jobposting.package')->getByDataId($iPackage, true);
                    if (!$aPackage)
                    {
                        return Phpfox_Error::set('Invalid package.');
                    }
                    
                    Phpfox::getService('jobposting.package.process')->updateRemainingPost($iPackage);
                    Phpfox::getService('jobposting.job.process')->publish($iId);
                    if ($featureJob)
                    {
                        Phpfox::getService('jobposting.job.process')->payForFeature($iId, $sUrl);
                    }
                }
                elseif ($aVals['paypal'] == 1) //buy new
                {
                    $aPackage = Phpfox::getService('jobposting.package')->getById($iPackage);
                    if (!$aPackage)
                    {
                        return Phpfox_Error::set('Invalid package.');
                    }
                    
                    Phpfox::getService('jobposting.package.process')->pay(array($iPackage), $aVals['company_id'], $sUrl, false, $iId, $featureJob);
                }
            }
        }
	}
 
	public function process(){
		Phpfox::getUserParam('jobposting.can_add_job', true);
		
		$aValidationParam = $this->_getValidationParams();
		
        $oValid = Phpfox::getLib('validator')->set(array(
                'sFormName' => 'ync_edit_jobposting_form',
                'aParams' => $aValidationParam
            )
        );
		$bIsEdit = false;
		$iEditId = 0;

        $aForms = array(
            'category' => null
        );

        //0 is value default in admincp, 2 is job
        // GET ELEMENT OF CUSTOM FIELD
        $aFields = Phpfox::getService('jobposting.custom')->getByCompanyId(0, 2);

        //check status job default is draft
		$draft = true;
		$iFeature = true;
		$sUrl = "";
		if ($iEditId = $this->request()->get('req3')){
			$sUrl = Phpfox::getLib('url')->makeUrl('jobposting').$iEditId."/";
            //GET VALUE OF CUSTOM FIELD
            $aFields = Phpfox::getService('jobposting.custom')->getByObjectId($iEditId, 2, true);

            if (($aJob = Phpfox::getService('jobposting.job')->getJobByJobId($iEditId))){
				
				//check draft
				if($aJob['post_status'] == 1)
				{
					$draft = false;
				}
				if($aJob['is_featured'] == 1)
				{
					$draft = false;
				}
				
				if($aJob['time_expire']<=PHPFOX_TIME){
					PHpfox::getLib("url")->send('subscribe');
				}
				$bIsEdit = true;
				$this->setParam('aJob', $aJob);
				$aForms = $aJob;
				$this->template()->assign(array(
					'aForms' => $aJob, 
				));
			}
		}

        //get custom field
        foreach($aFields as $k=>$aField)
        {
            if($aField['var_type'] != 'text' && $aField['var_type'] != 'textarea' && empty($aField['option']))
            {
                unset($aFields[$k]);
            }
        }

        // get all companies
		$aCompanies = Phpfox::getService('jobposting.company')->getCompaniesHasCreateJob(($bIsEdit ? $aJob['user_id'] : Phpfox::getUserId()));
		//Check permission can add job

		if(count($aCompanies) == 0)
		{
			$str = _p('you_can_not_create_a_job_please_create_a_company_first',array(
				'link' => Phpfox::permalink('jobposting.company.add', null)
			));
			Phpfox_Error::display($str);
			return;
		}
		$company_id = $aCompanies[0]['company_id'];
		
		//get company from url
		if($iEditId)
		{
			// edit job
			$company_id = $aJob['company_id'];


		} 
		
		else{
			//create job from URL 
			$companyUrl = $this->request()->getInt('company');
			if($companyUrl){
				$company_id = $companyUrl;
			}
		}
		
		if ($aVals = $this->request()->getArray('val'))
		{
			
            
			$valuefeatue = PHpfox::getParam("jobposting.fee_feature_job");
			if ($this->_verifyCustomForm($aFields,isset($aVals['custom'])?$aVals['custom']:array()) && $oValid->isValid($aVals)){
				if (Phpfox_Error::isPassed())
				{	//edit job
					if($bIsEdit){
						$aVals['job_id'] = $iEditId;
						$aVals['company_id'] = $aJob['company_id'];
						Phpfox::getService('jobposting.job.process')->update($aVals);
						
						$this->payPackage($aVals, $iEditId);
						$this->url()->permalink('jobposting', $iEditId, $aVals['title'], true, _p('job_successfully_updated'));
					}
					else
					{	// add new job
						if ($iId = Phpfox::getService('jobposting.job.process')->add($aVals))
						{
							$this->payPackage($aVals, $iId);
							$this->url()->permalink('jobposting', $iId, $aVals['title'], true, _p('job_successfully_added'));
						}
					}
				}
			}
		}

        if(isset($aVals['custom']) && count($aVals['custom']))
        {
            $aFields = $this->_setValuesCustomForm($aFields,$aVals['custom']);
        }

        //change block popup payment
		$aPackages = Phpfox::getService('jobposting.package')->getBoughtPackages($company_id, true);
		$aTobuyPackages = Phpfox::getService('jobposting.package')->getToBuyPackages($company_id);
		$bCanFeature = Phpfox::getService('jobposting.permission')->canFeaturePublishedJob($iEditId);
		$sCategories = Phpfox::getService('jobposting.catjob')->getForAdd(3, $aForms['category']);
		
		$buy_packages= Phpfox::getService('jobposting.company')->isAdminPermission($company_id, Phpfox::getUserId(), 'buy_packages');
		$title = _p('create_new_job');

		if ($bIsEdit)
		{
			$title = _p('edit_job');

	        $this->template()->buildPageMenu('ync_edit_jobposting_form', [], [
	           'link' => Phpfox::permalink('jobposting', $iEditId, $aJob['title']),
	           'phrase' => _p('view_job')
	       ]);
		}
		
		$this->template()->setBreadcrumb(_p('job_posting'), $this->url()->makeUrl('jobposting'))
						 ->setBreadcrumb($title, ($iEditId > 0 ? $this->url()->makeUrl('jobposting.add', $iEditId) : $this->url()->makeUrl('jobposting.add')), false);

		$this->template()->assign(array(
			'sCreateJs' => $oValid->createJS(),
			'sGetJsForm' => $oValid->getJsForm(),
			'sCategories' => $sCategories,
			'bIsEdit' => $bIsEdit,
			'job_id' => $iEditId,
			'company_id' => $company_id,
			'aPackages' => $aPackages,
			'draft' => $draft,
			'buy_packages' => $buy_packages,
			'aCompanies' => $aCompanies,
			'iFeature' => $iFeature,
			'aTobuyPackages' => $aTobuyPackages,
			'bCanFeature' => $bCanFeature,
			'featurefee' => PHpfox::getService('jobposting.helper')->getTextParseCurrency(Phpfox::getParam("jobposting.fee_feature_job")),
			'sUrl'=>$sUrl,
            'aFields' => $aFields,
 		))

			->setEditor(array('wysiwyg' => 1))
            ->setPhrase(array(
                'notice',
                'please_select_a_package_to_publish_this_job'
            ))
			->setHeader('cache', array(
				'jquery/plugin/jquery.highlightFade.js' => 'static_script',
				'switch_legend.js' => 'static_script',
				'switch_menu.js' => 'static_script',
				'quick_edit.js' => 'static_script',
				'pager.css' => 'style_css',
				'jquery.magnific-popup.js' => 'module_jobposting',
				'share.js' => 'module_attachment', 
				'addjob.js' => 'module_jobposting',
				'map.js' => 'module_jobposting',
                'job-add.js' => 'module_jobposting',    
                'country.js' => 'module_core',                        
			));
		
		
		 if (Phpfox::isModule('attachment')) {
            $this->template()->assign(array('aAttachmentShare' => array(
                    'type' => 'jobposting',
                    'id' => 'ync_edit_jobposting_form',
                    'edit_id' => ($bIsEdit ? $iEditId : 0),
                    'inline' => false
                )
                )
            );
        }
		Phpfox::getService('jobposting.helper')->buildMenu();
        (($sPlugin = Phpfox_Plugin::get('jobposting.component_controller_add_end')) ? eval($sPlugin) : false);
		  
	}

    private function _verifyCustomForm($aFields, $aVals)
    {
        foreach($aFields as $k=>$aField)
        {
            if($aField['var_type'] != 'text' && $aField['var_type'] != 'textarea' && empty($aField['option']))
            {
                continue;
            }

            if($aField['is_required'] && empty($aVals[$aField['field_id']]))
            {
                $this->template()->assign(array('aFields' => $aFields));
                return Phpfox_Error::set(_p('custom_field_is_required', array('custom_field' => _p($aField['phrase_var_name']))));
            }
        }
        return true;
    }

    private function _setValuesCustomForm($aFields, $aVals)
    {

        foreach($aFields as $k=>$aField)
        {
            if($aField['var_type'] != 'text' && $aField['var_type'] != 'textarea' && empty($aField['option']))
            {
                continue;
            }
            if(isset($aVals[$aField['field_id']]))
                $aFields[$k]['value'] = $aVals[$aField['field_id']];
        }
        return $aFields;
    }

    /**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		
		(($sPlugin = Phpfox_Plugin::get('jobposting.Jobposting_Component_Controller_Add_clean')) ? eval($sPlugin) : false);
	}

}

