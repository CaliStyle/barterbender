<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright       [PHPFOX_COPYRIGHT]
 * @author          VuDP, AnNT
 * @package         Phpfox_jobposting
 * @version         
 */

class JobPosting_Component_Ajax_Ajax extends Phpfox_Ajax
{
    public function applyJobWithoutFee()
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('jobposting.can_apply_job_without_fee',true);
        $jobId = $this->get('id');
        if(!empty($jobId)) {
            $currency = Phpfox::getService('jobposting.helper')->getDefaultCurrency();
            $aInvoice = [
                'jobID' => $jobId,
                'package_data' => 0,
                'jobApplicationFee' => 0,
                'sCurrency' => $currency
            ];

            $aTransaction = array(
                'invoice' => serialize($aInvoice),
                'user_id' => Phpfox::getUserId(),
                'item_id' => $jobId,
                'time_stamp' => PHPFOX_TIME,
                'amount' => 0,
                'currency' => $currency,
                'status' => Phpfox::getService('jobposting.transaction')->getStatusIdByName('completed'),
                'payment_type' => 8 //type for apply job without fee
            );
            $transactionId = Phpfox::getService('jobposting.transaction.process')->add($aTransaction);

            $p = PHPFOX_DIR_FILE . PHPFOX_DS . 'pic' . PHPFOX_DS . 'jobposting' . PHPFOX_DS;
            if (!is_dir($p)) {
                @mkdir($p, 0777, 1);
            }

            $returnUrl = Phpfox::getLib('url')->permalink('jobposting.applyjob', !empty($transactionId) ? $transactionId : '');
            $this->call('window.location.href = \'' . $returnUrl . '\'');
        }
    }

    public function popupPublishJob()
    {
        $id = $this->get('id');
        $this->setTitle(_p('publish_job'));
        Phpfox::getBlock('jobposting.job.publishJob', array('id' => $id));
    }
	
    public function popupApplyJobPackage()
    {
        $id = $this->get('id');
        $this->setTitle(_p('apply_job'));
        Phpfox::getBlock('jobposting.job.applyjobpackage', array('id' => $id));
    }

    public function publishJob()
    {
        $iId = $this->get('id');
        $package = $this->get('package');
        $paypal = $this->get('paypal');
        $feature = $this->get('feature');
        
        $aJob = Phpfox::getService('jobposting.job')->getGeneralInfo($iId);
        if (!$aJob)
        {
            return Phpfox_Error::set(_p('unable_to_find_the_job_you_want_to_publish'));
        }
        
        if (!$package)
        {
            return Phpfox_Error::set(_p('please_select_a_package_to_publish_this_job'));
        }
        
        $bSuccess = false;
        
        $featureJob = (isset($feature) && $feature) ? $iId : 0;
        $sUrl = Phpfox::getLib('url')->makeUrl('jobposting.company.add.jobs', array('id' => $aJob['company_id']));
        
        if ($paypal == 0) //select existing packages
        {
            $aPackage = Phpfox::getService('jobposting.package')->getByDataId($package, true);
            if (!$aPackage)
            {
                return Phpfox_Error::set('Invalid package.');
            }
            
            Phpfox::getService('jobposting.package.process')->updateRemainingPost($package);
            Phpfox::getService('jobposting.job.process')->publish($iId);
            if ($featureJob)
            {
                $sCheckoutUrl = Phpfox::getService('jobposting.job.process')->payForFeature($iId, $sUrl, true);
                if ($sCheckoutUrl === true)
                {
                    Phpfox::getService("jobposting.job.process")->featureJobs($iId, 1);
                    $bSuccess = true;
                }
                elseif (is_string($sCheckoutUrl))
                {
                    $this->call("$('#js_job_publish_loading').html($.ajaxProcess('Processing transaction')).show();");
                    $this->call("location.href = '".$sCheckoutUrl."';");
                    return;
                }
            }
            else
            {
                $bSuccess = true;
            }
        }
        elseif ($paypal == 1) //buy new
        {
            $aPackage = Phpfox::getService('jobposting.package')->getById($package);
            if (!$aPackage)
            {
                return Phpfox_Error::set('Invalid package.');
            }
            
            $iUserCompany = Phpfox::getService('jobposting.company')->getCompanyIdByUserId(Phpfox::getUserId());
            $sCheckoutUrl = Phpfox::getService('jobposting.package.process')->pay(array($package), $iUserCompany, $sUrl, true, $iId, $featureJob);
            if ($sCheckoutUrl === true)
            {
                if ($featureJob)
                {
                    Phpfox::getService("jobposting.job.process")->featureJobs($iId, 1);
                }
                $bSuccess = true;
            }
            elseif (is_string($sCheckoutUrl))
            {
                $this->call("$('#js_job_publish_loading').html($.ajaxProcess('"._p('processing_transaction')."')).show();");
                $this->call("location.href = '".$sCheckoutUrl."';");
                return;
            }
        }

        if ($bSuccess)
        {
            $sHtmlJobRow = Phpfox::getService('jobposting.job')->buildHtmlRow($iId);
            $this->html('#js_jp_job_'.$iId, $sHtmlJobRow);
            $this->call('tb_remove();');
        }
        else
        {
            $this->hide("#js_job_publish_loading");
            $this->call("$('.js_job_publish_btn').attr('disabled', false);");
        }
		
		 $sUrl = Phpfox::getLib('url')->makeUrl('jobposting').$iId.'/';
		 $this->call("location.href = '".$sUrl."';");
    }

    public function subscribe(){
        Phpfox::isUser(true);
        $this->setTitle('Subscribe Job');
        
        Phpfox::getComponent('jobposting.subscribe', array(), 'controller');
        
    }
	
	function view_more_jobs(){
		$company_id = $this->get('company_id');
		$iPage = $this->get('iPage')+1;
		$iLimit = 10;
		$ViewMore = 0;
		$aConds = 'job.company_id = '.$company_id;
		$aCompany = Phpfox::getService('jobposting.company')->getForEdit($company_id);	
		if(isset($aCompany['user_id']))
		{
			if($aCompany['user_id'] == PHpfox::getUserId() || PHpfox::isAdmin()){
				
			}
			else
			{
				$aConds.= " and job.post_status = 1 and job.time_expire>".PHPFOX_TIME;
			}
		}
		list($iCntSearch, $aJobsSearch) = Phpfox::getService("jobposting.job")->searchJobs($aConds, 'job.title ASC', $iPage, $iLimit);
		if(($iPage*$iLimit)<$iCntSearch)
		{
			$ViewMore = 1;
		}
		$hrefviewmore = "<a href='#' onclick=\"$.ajaxCall('jobposting.view_more_jobs','iPage={$iPage}&company_id={$company_id}');return false;\">"._p('view_more')."</a>";
	
		Phpfox::getLib('template')
			->assign(array(
				'aJobsSearch' => $aJobsSearch,
				'iCntSearch' => $iCntSearch,
				'ViewMoreJob' => $ViewMore,
				'iPage' => $iPage
			))
			->getTemplate('jobposting.block.job.mini_job_viewmore');
		
		if($ViewMore==0)
		{
			$hrefviewmore = "";
		}
		$this->append('#view_more_jobs', $this->getContent(false));
		$this->html('#href_view_more', $hrefviewmore);
		$this->call("\$Core.loadInit();");
	}
	
	function view_more_employee(){
		$company_id = $this->get('company_id');
		$iPage = $this->get('iPage')+1;
		$iLimit = 6;
		$ViewMore = 0;
		$sCond = "uf.company_id = ".$company_id;
		list($iCntEmployee, $aParticipant) = Phpfox::getService('jobposting.company')->searchEmployees($sCond, $iPage, $iLimit);
		if((($iPage * $iLimit)) < $iCntEmployee)
		{
			$ViewMore = 1;
		}

		$hrefviewmore = "<a href='#' onclick=\"$.ajaxCall('jobposting.view_more_employee','iPage={$iPage}&company_id={$company_id}');return false;\">"._p('view_more')."</a>";

		Phpfox::getLib('template')
			->assign(array(
				'aParticipant' => $aParticipant,
				'iCntEmployee' => $iCntEmployee,
				'ViewMore' => $ViewMore,
				'iPage' => $iPage,
                'isCompanyOwnerOrAdmin' => Phpfox::getService('jobposting.permission')->isCompanyOwnerOrAdmin($company_id)
			))
			->getTemplate('jobposting.block.company.mini_participant_company');
		
		if($ViewMore==0)
		{
			$hrefviewmore = "";
		}
		$this->append('#view_more_employee', $this->getContent(false));
		$this->html('#href_view_more_employee', $hrefviewmore);
	}
	
    
    public function activepackage(){
        $active = $this->get('active');
        $id = $this->get('id');
        
        if(Phpfox::getService('jobposting.package.process')->activepackage($id, $active))
        {
            if($active==1)
            {
                $this->call("$('#showpackage_{$id}').show();");
                $this->call("$('#hidepackage_{$id}').hide();");
            }    
            else {
                $this->call("$('#showpackage_{$id}').hide();");
                $this->call("$('#hidepackage_{$id}').show();");
            }
        }
    }
    
    public function activepapplyjobackage(){
        $active = $this->get('active');
        $id = $this->get('id');
        
        if(Phpfox::getService('jobposting.applyjobpackage.process')->activepackage($id, $active))
        {
            if($active==1)
            {
                $this->call("$('#showpackage_{$id}').show();");
                $this->call("$('#hidepackage_{$id}').hide();");
            }    
            else {
                $this->call("$('#showpackage_{$id}').hide();");
                $this->call("$('#hidepackage_{$id}').show();");
            }
        }
    }
    
    public function deletepackage(){
        $id = $this->get('id');
        if(Phpfox::getService('jobposting.package.process')->delete($id))
        {
            $this->call('setTimeout(function() {window.location.href = window.location.href},100);');
        }
    }
    
    public function deleteapplyjobpackage(){
        $id = $this->get('id');
        if(Phpfox::getService('jobposting.applyjobpackage.process')->delete($id))
        {
            $this->call('setTimeout(function() {window.location.href = window.location.href},100);');
        }
    }

    public function deleteImage()
    {
        $id = $this->get('id'); //image_id
        $iNewImage = Phpfox::getService('jobposting.company.process')->deleteImage($id);
        $this->call('$("#js_photo_holder_' . $id . '").remove(); onAfterDeletePhotoSuccess(' . $iNewImage . ');');
    }
    
    public function setDefaultImage()
    {
        $id = $this->get('id'); //image_id
        Phpfox::getService('jobposting.company.process')->setDefaultImage($id);
    }
    
    public function deleteLogo()
    {
        $id = $this->get('id'); //company_id
        Phpfox::getService('jobposting.company.process')->deleteLogo($id);
    }
    
    public function controllerAddField()
    {
        Phpfox::getComponent('jobposting.company.add-field', array(), 'controller');
    }

	public function controllerAddFieldBackEnd()
    {
        $this->setTitle(_p('add_field_question'));
        Phpfox::getComponent('jobposting.admincp.managecustomfield.add', array(), 'controller');
    }
    
    public function addField()
    {
        $aVals = $this->get('val');
		
        list($iFieldId, $aOptions) = Phpfox::getService('jobposting.custom.process')->add($aVals);
        if(!empty($iFieldId))
        {
            $aFields = Phpfox::getService('jobposting.custom')->getByCompanyId($aVals['company_id']);
            $sHtml = Phpfox::getService('jobposting.custom')->buildHtmlForReview($aFields);
            $this->html('#js_custom_field_review_holder', $sHtml);
            $this->call('tb_remove();');
        }
        
        $this->call("$('#js_add_field_loading').hide();");
        $this->call("$('#js_add_field_button').attr('disabled', false);");
		
		if(isset($aVals['type']) && Phpfox_Error::isPassed())
		{
			$this->call("window.location.href = window.location.href");
		}			
    }
    
    public function updateField()
    {
        $aVals = $this->get('val');
        if(Phpfox::getService('jobposting.custom.process')->update($aVals['id'], $aVals))
        {
            $aFields = Phpfox::getService('jobposting.custom')->getByCompanyId($aVals['company_id']);
            $sHtml = Phpfox::getService('jobposting.custom')->buildHtmlForReview($aFields);
            $this->html('#js_custom_field_review_holder', $sHtml);
            $this->call('tb_remove();');
        }
        
        $this->call("$('#js_add_field_loading').hide();");
        $this->call("$('#js_add_field_button').attr('disabled', false);");
		
		if(isset($aVals['type']))
			$this->call("window.location.href = window.location.href");
    }
    
    public function deleteField()
    {
        $id = $this->get('id');
        if (Phpfox::getService('jobposting.custom.process')->delete($id))
        {
            $this->remove('#js_custom_field_'.$id);
        }
    }
    
    public function deleteOption()
    {
        $id = $this->get('id');
        $company_id = $this->get('company_id');
        if (Phpfox::getService('jobposting.custom.process')->deleteOption($id))
        {
            $aFields = Phpfox::getService('jobposting.custom')->getByCompanyId($company_id);
            $sHtml = Phpfox::getService('jobposting.custom')->buildHtmlForReview($aFields);
            $this->html('#js_custom_field_review_holder', $sHtml);
            $this->remove('#js_current_value_'.$id);
        }
        else
        {
            $this->alert(_p('could_not_delete'));
        }
    }
    
    public function sponsorCompany()
    {
    	if (!Phpfox::getUserParam('jobposting.can_sponsor_company'))
    	{
    		return Phpfox_Error::set(_p('you_do_not_have_permission_to_sponsor_company'));
    	}
		
        $id = $this->get('id');
        
        $aCompany = Phpfox::getService('jobposting.company')->getGeneralInfo($id);
        if (!$aCompany)
        {
            return Phpfox_Error::set(_p('unable_to_find_the_company_you_want_to_sponsor'));
        }
		
		if (!$aCompany['is_approved'])
		{
			return Phpfox_Error::set(_p('this_company_is_pending_for_approve'));
		}
        
        if (Phpfox::getService('jobposting.company')->isSponsor($id))
        {
            return Phpfox_Error::set(_p('this_company_has_been_sponsored'));
        }

		if(Phpfox::isAdmin())
		{
			Phpfox::getService('jobposting.company.process')->sponsor($id);
			$this->alert('Company successfully sponsored.',_p('sponsor_company'),300,100,true);
        	$this->call('setTimeout(function() { location.href = location.href;}, 1500);');
			return;
		}
        
		
        $sReturnUrl = urlencode(Phpfox::getLib('url')->permalink('jobposting.company', $id, $aCompany['name']));
        $sCheckoutUrl = Phpfox::getService('jobposting.company.process')->payForSponsor($id, $sReturnUrl, true);

        if ($sCheckoutUrl === true)
        {
            if (Phpfox::getService('jobposting.company.process')->sponsor($id))
            {
                $this->alert(_p('company_successfully_sponsored'));
            }
        }
        elseif (is_string($sCheckoutUrl))
        {
            $this->call("$('.js_jc_add_loading').html($.ajaxProcess('"._p('processing_transaction')."')).show();");
            $this->call("location.href = '".$sCheckoutUrl."';");
        }
        else
        {
            $this->hide(".js_jc_add_loading");
            $this->call("$('.js_jc_sponsor_btn').removeClass('button_off').attr('disabled', false);");
        }
    }
    
	public function unsponsorCompany()
    {
    	if (!Phpfox::isAdmin())
    	{
    		return Phpfox_Error::set(_p('you_do_not_have_permission_to_un_sponsor_company'));
    	}
		
        $id = $this->get('id');
        
        $aCompany = Phpfox::getService('jobposting.company')->getGeneralInfo($id);
        if (!$aCompany)
        {
            return Phpfox_Error::set(_p('unable_to_find_the_company_you_want_to_sponsor'));
        }
		
		if (!$aCompany['is_approved'])
		{
			return Phpfox_Error::set(_p('this_company_is_pending_for_approve'));
		}
        
        if (!Phpfox::getService('jobposting.company')->isSponsor($id))
        {
            return Phpfox_Error::set(_p('this_company_has_been_un_sponsored'));
        }
        
		if(Phpfox::isAdmin())
		{
			Phpfox::getService('jobposting.company.process')->sponsor($id,0);
			$this->alert(_p('company_successfully_un_sponsore'),_p('un_sponsor_company'),300,100,true);
        	$this->call('setTimeout(function() { location.href = location.href;}, 1500);');
			return;
		}
    }

	public function activatedCompany()
    {
        $id = $this->get('id');
        
        $aCompany = Phpfox::getService('jobposting.company')->getGeneralInfo($id);
        if (!$aCompany)
        {
            return Phpfox_Error::set(_p('unable_to_find_the_company'));
        }
		
		if (!$aCompany['is_approved'])
		{
			return Phpfox_Error::set(_p('this_company_is_pending_for_approve'));
		}

		if (!Phpfox::isAdmin() && $aCompany['user_id'] != Phpfox::getUserId())
    	{
    		return Phpfox_Error::set(_p('you_do_not_have_permission_to_active_this_company'));
    	}
     
		Phpfox::getService('jobposting.company.process')->activate($id,1);
		$this->alert(_p('company_successfully_activated'),_p('activated_company'),300,100,true);
    	$this->call('setTimeout(function() { location.href = location.href;}, 1500);');
		return;
    }
	
	public function deactivatedCompany()
    {
        $id = $this->get('id');
        
        $aCompany = Phpfox::getService('jobposting.company')->getGeneralInfo($id);
        if (!$aCompany)
        {
            return Phpfox_Error::set(_p('unable_to_find_the_company'));
        }
		
		if (!$aCompany['is_approved'])
		{
			return Phpfox_Error::set(_p('this_company_is_pending_for_approve'));
		}

		if (!Phpfox::isAdmin() && $aCompany['user_id'] != Phpfox::getUserId())
    	{
    		return Phpfox_Error::set(_p('you_do_not_have_permission_to_deactivate_this_company'));
    	}
        
		Phpfox::getService('jobposting.company.process')->activate($id,0);
		$this->alert(_p('company_successfully_deactivated'),_p('deactivated_company'),300,100,true);
    	$this->call('setTimeout(function() { location.href = location.href;}, 1500);');
		return;
    }

	
    
    public function payPackages()
	{
		$iCompanyId = $this->get('id');
		$aVals = $this->get('val');
		
		$sUrl = Phpfox::getLib('url')->permalink('jobposting.company.add.packages', 'id_'.$iCompanyId);
        $sCheckoutUrl = Phpfox::getService('jobposting.package.process')->pay($aVals['packages'], $iCompanyId, $sUrl, true);
		if($sCheckoutUrl===true)
        {
        	$this->alert(_p('pay_packages_successfully'));

            $sHtmlBoughtPackages = Phpfox::getService('jobposting.package')->buildHtmlBoughtPackages($iCompanyId);
            $sHtmlToBuyPackages = Phpfox::getService('jobposting.package')->buildHtmlToBuyPackages($iCompanyId);
	        $this->hide(".js_jc_add_loading");
	        $this->call("$('.js_jc_pay_packages_btn').removeClass('button_off').attr('disabled', false);");
            $this->html('#js_jc_bought_packages', $sHtmlBoughtPackages);
            $this->html('#js_jc_tobuy_packages', $sHtmlToBuyPackages);
        }
		elseif(is_string($sCheckoutUrl))
        {
            $this->call("$('.js_jc_add_loading').html($.ajaxProcess('"._p('processing_transaction')."')).show();");
            $this->call("location.href = '".$sCheckoutUrl."';");
        }
		else
		{
	        $this->hide(".js_jc_add_loading");
	        $this->call("$('.js_jc_pay_packages_btn').removeClass('button_off').attr('disabled', false);");
		}
	}
    
    public function blockViewApplication()
    {
        $id = $this->get('id');
        Phpfox::getBlock('jobposting.application.view', array('id' => $id));
    }
    
    public function updateApplicationStatus()
    {
        $id = $this->get('id');
        $status = $this->get('status');
        if(Phpfox::getService('jobposting.application.process')->updateStatus($id, $status))
        {
            $sHtmlApplicationRow = Phpfox::getService('jobposting.application')->buildHtmlRow($id);
            $this->html('#js_ja_'.$id, $sHtmlApplicationRow);
        }
    }
    
    public function deleteApplication()
    {
        $id = $this->get('id');
        if(Phpfox::getService('jobposting.application.process')->delete($id))
        {
            $this->remove('#js_ja_'.$id);
            $this->alert(_p('application_successfully_deleted'));
        }
    }
    
    public function changeJobHide()
    {
        $id = $this->get('id');
        if(Phpfox::getService('jobposting.job.process')->changeHide($id))
        {
            $job = Phpfox::getService('jobposting.job')->getJobByJobId($id, true);
            Phpfox_Template::instance()->assign('aJob', $job)->getTemplate('jobposting.block.company.posted-job-entry');
            $contentJob = $this->getContent(false);
            $this->html('#js_jp_job_'.$id, $contentJob);
        }
    }
    
    public function deleteJob()
    {
        $id = $this->get('id');
        if(Phpfox::getService('jobposting.job.process')->delete($id))
        {
            $this->remove('#js_jp_job_'.$id);
            $this->alert(_p('job_successfully_deleted'));
        }
    }
    
    public function blockPromoteJob()
    {
        $id = $this->get('id');
        Phpfox::getBlock('jobposting.job.promote', array('id' => $id));
    }
    
    public function changePromoteCode()
    {
        $id = $this->get('id');
        $val = $this->get('val');
        $en_photo = !empty($val['en_photo']) ? 1 : 0;
        $en_description = !empty($val['en_description']) ? 1 : 0;
        
        $sPromoteCode = Phpfox::getService('jobposting.job')->getPromoteCode($id, $en_photo, $en_description);
        
        $this->html('#js_jp_promote_code_textarea', htmlentities($sPromoteCode));
        $this->html('#js_jp_promote_iframe', $sPromoteCode);
    }
    
    public function blockInvite()
    {
        $sType = $this->get('type');
        $iId = $this->get('id');
        
        Phpfox::getBlock('jobposting.invite', array('type' => $sType, 'id' => $iId));
        $this->call('<script>$Core.loadInit();</script>');
    }
    
    public function changeFavorite()
    {
        $sType = $this->get('type');
        $iId = $this->get('id');
        $iCurrent = $this->get('current');
        $iUserId = Phpfox::getUserId();
        
        $sHtmlFavorite = '<a href="#" onclick="$.ajaxCall(\'jobposting.changeFavorite\', \'type='.$sType.'&id='.$iId.'&current=0\'); return false;">'._p('favorite').'</a>';
        $sHtmlUnFavorite = '<a href="#" onclick="$.ajaxCall(\'jobposting.changeFavorite\', \'type='.$sType.'&id='.$iId.'&current=1\'); return false;">'._p('unfavorite').'</a>';
        
        if(!$iCurrent)
        {
            $iIsFavorited = (Phpfox::getService('jobposting')->isFavorited($sType, $iId, $iUserId) ? 1 : 0);
            
            if($iIsFavorited)
            {
                $this->html('#js_jp_favorite_link', $sHtmlUnFavorite);
                $this->alert(_p('you_have_favorited_this').' '.$sType.'.');
            }
            elseif(Phpfox::getService('jobposting.process')->favorite($sType, $iId, $iUserId))
            {
                $this->html('#js_jp_favorite_link', $sHtmlUnFavorite);
                $this->alert(_p('favorite_successfully'));
            }
        }
        else
        {
            if(Phpfox::getService('jobposting.process')->unfavorite($sType, $iId, $iUserId))
            {
                $this->html('#js_jp_favorite_link', $sHtmlFavorite);
                $this->alert(_p('unfavorite_successfully'));
            }
        }
    }
    
    public function changeFollow()
    {
        $sType = $this->get('type');
        $iId = $this->get('id');
        $iCurrent = $this->get('current');
        $iUserId = Phpfox::getUserId();
        
        $sHtmlFollow = '<a href="#" onclick="$.ajaxCall(\'jobposting.changeFollow\', \'type='.$sType.'&id='.$iId.'&current=0\'); return false;">'._p('follow').'</a>';
        $sHtmlUnFollow = '<a href="#" onclick="$.ajaxCall(\'jobposting.changeFollow\', \'type='.$sType.'&id='.$iId.'&current=1\'); return false;">'._p('unfollow').'</a>';
        
        if(!$iCurrent)
        {
            $iIsFollowed = (Phpfox::getService('jobposting')->isFollowed($sType, $iId, $iUserId) ? 1 : 0);
            
            if($iIsFollowed)
            {
                $this->html('#js_jp_follow_link', $sHtmlUnFollow);
                $this->alert(_p('you_have_followed_this').$sType.'.');
            }
            elseif(Phpfox::getService('jobposting.process')->follow($sType, $iId, $iUserId))
            {
                $this->html('#js_jp_follow_link', $sHtmlUnFollow);
                $this->alert(_p('follow_successfully'));
            }
        }
        else
        {
            if(Phpfox::getService('jobposting.process')->unfollow($sType, $iId, $iUserId))
            {
                $this->html('#js_jp_follow_link', $sHtmlFollow);
                $this->alert(_p('unfollow_successfully'));
            }
        }
    }

    public function updateFeatured()
    {
        // Get Params
        $job_id = (int)$this->get('job_id');
        $iIsFeatured = (int)$this->get('active');

        $oJobsProcess = Phpfox::getService('jobposting.job.process');
        if ($job_id) {
            $oJobsProcess->feature($job_id, $iIsFeatured);
        }
    }

    public function updateSponsor()
    {
        // Get Params
        $company_id = (int)$this->get('company_id');
        $iIsSponsor = (int)$this->get('active');

        $oCompaniesProcess = Phpfox::getService('jobposting.company.process');
        if ($company_id) {
            $oCompaniesProcess->feature($company_id, $iIsSponsor);
        }
    }
	 
	 public function applyjob(){
	 	$job_id = $this->get('job_id');
		 Phpfox::getBlock('jobposting.job.apply',
             array(                  
             	'job_id' => $job_id,
             )
         );
	 }
    
	public function addFeedComment()
	{
		Phpfox::isUser(true);
		
		$aVals = (array) $this->get('val');	
		
		if (Phpfox::getLib('parse.format')->isEmpty($aVals['user_status']))
		{
			$this->alert(_p('user.add_some_text_to_share'));
			$this->call('$Core.activityFeedProcess(false);');
			return;			
		}		
		
		$aCompany = Phpfox::getService('jobposting.company')->getForEdit($aVals['callback_item_id'], true);
		
		if (!isset($aCompany['company_id']))
		{
			$this->alert(_p('event.unable_to_find_the_event_you_are_trying_to_comment_on'));
			$this->call('$Core.activityFeedProcess(false);');
			return;
		}
		
		$sLink = Phpfox::permalink('jobposting.company', $aCompany['company_id'], $aCompany['name']);
		$aCallback = array(
			'module' => 'jobposting',
			'table_prefix' => 'jobposting_',
			'link' => $sLink,
			'email_user_id' => $aCompany['user_id'],
			'subject' => _p('full_name_wrote_a_comment_on_your_jobposting_name', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aCompany['name'])),
			'message' => _p('full_name_wrote_a_comment_on_your_jobposting_message', array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aCompany['name'])),
			'notification' => 'jobposting_comment',
			'feed_id' => 'jobposting_comment',
			'item_id' => $aCompany['company_id']
		);
		
		$aVals['parent_user_id'] = $aVals['callback_item_id'];
		
		if (isset($aVals['user_status']) && ($iId = Phpfox::getService('feed.process')->callback($aCallback)->addComment($aVals)))
		{
			Phpfox::getLib('database')->updateCounter('jobposting_company', 'total_comment', 'company_id', $aCompany['company_id']);		
			
			Phpfox::getService('feed')->callback($aCallback)->processAjax($iId);
		}
		else 
		{
			$this->call('$Core.activityFeedProcess(false);');
		}		
	}	

	public function workingcompany(){
        // init
        $company_id = $this->get('company_id');        
        $working = $this->get('working');
        $tmp = !$working;

        // process 
        if($working==1){
            // add request into working company    
            $ownerID = Phpfox::getService('jobposting')->getOwner('company', $company_id);
            if(Phpfox::getUserId() != $ownerID){
                // is not company owner
                Phpfox::getService('jobposting.process')->addWorkingCompanyRequest(Phpfox::getUserId(), $company_id);
                $phrase = '';
                $alert = _p('you_have_joined_to_the_compnay_approve');
                $text = "";

                // notify for company owner
                Phpfox::getService('jobposting.process')->addNotification('join', 'company', $company_id, Phpfox::getUserId(), true, true, false);            

                // send email to company owner
                $aCompany = Phpfox::getService('jobposting.company')->getGeneralInfo($company_id);
                Phpfox::getLib('mail')->to($ownerID)
                    ->subject(_p('new_user_joined_to_your_company_company_title', array(
                        'company_title' => $aCompany['name']
                    )))
                    ->message(_p('join_user_fullname_joined_your_company_company_title_please_check_at_a_href_employees_link_employees_a_tab_on_your_company', array(
                        'join_user_fullname' => Phpfox::getUserBy('full_name'),
                        'company_title' => $aCompany['name'],
                        'employees_link' => Phpfox::getLib('url')->permalink('jobposting.company', $aCompany['company_id'], $aCompany['name']) . '#tabs-4', 
                    )))
                    ->send();                               
            } else {
                // is company owner
                 $phrase = _p('leave_this_company');
                 $alert = _p('you_have_joined_to_the_compnay_successfully');
                $text = "<input type='button' class='button btn btn-success' onclick=\" workingCompany(".$company_id.", 0) \" value=\"$phrase\"/>";

                Phpfox::getLib("database")->update(Phpfox::getT('user_field'), array(
                    'company_id' => $company_id
                ), 'user_id = ' . Phpfox::getUserId());

                // notify for company owner
                Phpfox::getService('jobposting.process')->addNotification('join', 'company', $company_id, Phpfox::getUserId(), true, true, false);            
            }

        }

        if($working==0){
            // remove working company 
            $phrase = _p('working_at_this_company');
            $alert = _p('you_have_left_to_the_compnay_successfully');
			$text = "<input type='button' class='button btn btn-success' onclick=\" workingCompany(".$company_id.", 1) \" value=\"$phrase\"/>";
			
            $company_id = 0;
            Phpfox::getLib("database")->update(Phpfox::getT('user_field'), array(
                'company_id' => $company_id
            ), 'user_id = ' . Phpfox::getUserId());
        }

        // inform requester
        $this->html('#join_leave_company',$text);
        //$this->alert($alert);
		
		$this->call("window.location.href = window.location.href;");

        // end 
	}

    public function acceptWorkingCompany(){
        // init
        $companyID = $this->get('companyID');        
        $userID = $this->get('userID');

        // process
        // remove record
        Phpfox::getService('jobposting.process')->deleteWorkingCompanyRequest($userID, $companyID);
        // update user_field with companyID <> 0
        Phpfox::getLib("database")->update(Phpfox::getT('user_field'), array(
            'company_id' => $companyID
        ), 'user_id = ' . $userID);

        // send notification for requester (approved)
        Phpfox::getService('notification.process')->add('jobposting_' . 'accept' . 'working'
            , $companyID
            , $userID
        );

        // send email to for requester (approved)
        $aCompany = Phpfox::getService('jobposting.company')->getGeneralInfo($companyID);
        Phpfox::getLib('mail')->to($userID)
            ->subject(_p('notification_from_company_company_title', array(
                'company_title' => $aCompany['name']
            )))
            ->message(_p('you_have_been_accepted_working_on_company_company_title_1_please_check_more_information_at_a_href_company_url_company_title_2_a', array(
                'company_title_1' => $aCompany['name'],
                'company_url' => Phpfox::getLib('url')->permalink('jobposting.company', $aCompany['company_id'], $aCompany['name']) . '#tabs-4', 
                'company_title_2' => $aCompany['name'],
            )))
            ->send();                               

        $text = '<a class="remove" href="#" onclick=" $(\'employee_action_loader\').show(); $.ajaxCall(\'jobposting.removeWorkingCompany\', \'type=company&userID=' . $userID . '&companyID=' . $companyID . '\'); return false;" title="' . _p('em_remove') . '"></a>';

        // end 
        $this->hide('#employee_action_loader');
        $this->html('#employee_' . $userID, $text);
        
		$this->call("window.location.href = window.location.href;");
        //$this->alert(_p('alert_accept_success'));
    }

    public function rejectWorkingCompany(){
        // init
        $companyID = $this->get('companyID');        
        $userID = $this->get('userID');

        // process
        // remove record
        Phpfox::getService('jobposting.process')->deleteWorkingCompanyRequest($userID, $companyID);

        // send notification for requester (rejected)
        Phpfox::getService('notification.process')->add('jobposting_' . 'reject' . 'working'
            , $companyID
            , $userID
        );

        // end 
        $this->hide('#employee_action_loader');
        $this->call('$("#employee_parent_' . $userID . '").remove();');
		
		//$this->alert(_p('alert_reject_success'));
		$this->call("window.location.href = window.location.href;");
		
        	
    }

    public function removeWorkingCompany(){
        // init
        $companyID = $this->get('companyID');        
        $userID = $this->get('userID');

        // process
        // update user_field with companyID = 0
        Phpfox::getLib("database")->update(Phpfox::getT('user_field'), array(
            'company_id' => 0
        ), 'user_id = ' . $userID);

        // send notification for requester (removed)
        Phpfox::getService('notification.process')->add('jobposting_' . 'remove' . 'working'
            , $companyID
            , $userID
        );

        // end 
        $this->hide('#employee_action_loader');
        $this->call('$("#employee_parent_' . $userID . '").remove();');
        //$this->alert(_p('alert_remove_success'));
		
		$this->call("window.location.href = window.location.href;");
    }

	public function deleteCompany(){
		Phpfox::isUser(true);
		$iCompany = $this->get('company_id');
		if(!Phpfox::getService('jobposting.permission')->canDeleteCompany($iCompany, Phpfox::getUserId()))
    	{
    		$this->alert(_p('you_can_not_perform_this_action'),"Delete Company",300,100,true);
    		exit;
    	}
		if (Phpfox::getService('jobposting.company.process')->delete($iCompany))
    	{
    		$this->alert(_p('company_successfully_deleted'),"Delete Company",300,100,true);

    		$sUrl = Phpfox::getLib('url')->makeUrl('jobposting.company'); 
    		Phpfox::addMessage(_p('company_successfully_deleted'));
            
            $this->call('setTimeout(function() { location.href = \'' . $sUrl . '\';}, 1500);');
    		
    	}
    	else
    	{
    		$this->alert(_p('you_can_not_perform_this_action'),_p('delete_company'),300,100,true);
    	}
	}
	
	public function deleteJob_View(){
		Phpfox::isUser(true);
		$job_id = $this->get('job_id');
                $page_view = $this->get('page_view');
		if(!Phpfox::getService('jobposting.permission')->canDeleteJob($job_id, Phpfox::getUserId()))
    	{
    	
    		$this->alert(_p('you_can_not_perform_this_action'),_p('delete_job'),300,100,true);
    		exit;
    	}
		
		if (Phpfox::getService('jobposting.job.process')->delete($job_id))
    	{
    		$this->alert(_p('job_successfully_deleted'),_p('delete_job'),300,100,true);

    		$sUrl = Phpfox::getLib('url')->makeUrl('jobposting'); 
    		Phpfox::addMessage(_p('job_successfully_deleted'));
            if($page_view==2)
            {
                $company_id = $this->get('company_id');
                $sUrl = Phpfox::getLib('url')->makeUrl('jobposting.company').$company_id."/";
            }
            $this->call('setTimeout(function() { location.href = \'' . $sUrl . '\';}, 1500);');
    		
    	}
    	else
    	{
    		$this->alert(_p('you_can_not_perform_this_action'),_p('delete_job'),300,100,true);
    	}
	}

	public function featureJob()
    {
		Phpfox::isUser(true);
        
		$job_id = $this->get('job_id');
        
		$aJob = Phpfox::getService('jobposting.job')->getJobByJobId($job_id);
        
        if ($aJob['is_featured'])
        {
            $this->alert(_p('this_job_has_been_featured'), _p('feature_job'));
            return;
        }
		
		if(Phpfox::isAdmin())
		{
			Phpfox::getService("jobposting.job.process")->featureJobs($job_id, 1);
			$this->alert(_p('job_successfully_featured'),_p('feature_job'),300,100,true);
        	$this->call('setTimeout(function() { location.href = location.href;}, 1500);');
			return;
		}
			
		$sUrl = Phpfox::getLib('url')->permalink('jobposting', $job_id, $aJob['title']);
        
		$sCheckoutUrl = Phpfox::getService('jobposting.job.process')->payForFeature($job_id, $sUrl, true);
		
        if ($sCheckoutUrl === true)
        {
            Phpfox::getService("jobposting.job.process")->featureJobs($job_id, 1);
            $this->alert(_p('job_successfully_featured'), _p('feature_job'));
        }
        elseif (is_string($sCheckoutUrl))
        {
            $this->call("location.href = '".$sCheckoutUrl."';");
        }
	}

	public function unfeatureJob()
    {
		Phpfox::isUser(true);
        
		$job_id = $this->get('job_id');
        
		$aJob = Phpfox::getService('jobposting.job')->getJobByJobId($job_id);
        
        if ($aJob['is_featured']==0)
        {
            $this->alert(_p('this_job_has_been_un_featured'), _p('un_feature_job'));
            return;
        }
		
		if(Phpfox::isAdmin())
		{
			Phpfox::getService("jobposting.job.process")->featureJobs($job_id, 0, 0);
			$this->alert(_p('job_successfully_un_feature'),_p('un_feature_job'),300,100,true);
        	$this->call('setTimeout(function() { location.href = location.href;}, 1500);');
			return;
		}
		
	}
	
	public function approveCompany()
    {
		Phpfox::isUser(true);
		$iCompany = $this->get('id');
        
		if(!Phpfox::getService('jobposting.permission')->canApproveCompany($iCompany, Phpfox::getUserId()))
    	{
    		return $this->alert(_p('you_can_not_perform_this_action'),_p('approve_company'),300,100,true);
    	}
		if (Phpfox::getService('jobposting.company.process')->approveCompany($iCompany))
    	{
    		$this->alert(_p('company_successfully_approved'),_p('approve_company'),300,100,true);
            $this->call('setTimeout(function() { location.href = location.href;}, 1500);');
    	}
    	else
    	{
    		$this->alert(_p('you_can_not_perform_this_action'),_p('approve_company'),300,100,true);
    	}
	}
	
	public function approveJob()
    {
		Phpfox::isUser(true);
		$job_id = $this->get('job_id');
        
		if(!Phpfox::getService('jobposting.permission')->canApproveJob($job_id, Phpfox::getUserId()))
    	{
    		return $this->alert(_p('you_can_not_perform_this_action'),_p('approve_job'),300,100,true);
    	}
		if (Phpfox::getService('jobposting.job.process')->approveJob($job_id))
    	{
    		$this->alert(_p('job_successfully_approved'),_p('approve_job'),300,100,true);
            $this->call('setTimeout(function() { location.href = location.href;}, 1500);');
    	}
    	else
    	{
    		$this->alert(_p('you_can_not_perform_this_action'),_p('approve_job'),300,100,true);
    	}
	}
    
    public function moderationJob()
	{
		Phpfox::isUser(true);
		
		switch ($this->get('action'))
		{
			case 'approve':
				Phpfox::getUserParam('jobposting.can_approve_job', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
					Phpfox::getService('jobposting.job.process')->approveJob($iId);
					$this->remove('#js_jp_job_entry_' . $iId);
				}
				$this->updateCount();
				$sMessage = _p('job_s_successfully_approved');
				break;			
			case 'delete':
				Phpfox::getUserParam('jobposting.can_delete_job_other_user', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
					Phpfox::getService('jobposting.job.process')->delete($iId);
					$this->slideUp('#js_jp_job_entry_' . $iId);
				}
				$sMessage = _p('job_s_successfully_deleted');
				break;
		}
		
		$this->alert($sMessage, 'Moderation', 300, 150, true);
		$this->hide('.moderation_process');
		$this->reload();
	}
    
    public function moderationCompany()
	{
		Phpfox::isUser(true);
		
		switch ($this->get('action'))
		{
			case 'approve':
				Phpfox::getUserParam('jobposting.can_approve_company', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
					Phpfox::getService('jobposting.company.process')->approveCompany($iId);
					$this->remove('#js_jp_company_entry_' . $iId);
				}
				$this->updateCount();
				$sMessage = _p('company_s_successfully_approved');
				break;			
			case 'delete':
				Phpfox::getUserParam('jobposting.can_delete_company_other_user', true);
				foreach ((array) $this->get('item_moderate') as $iId)
				{
					Phpfox::getService('jobposting.company.process')->delete($iId);
					$this->slideUp('#js_jp_company_entry_' . $iId);
				}
				$sMessage = _p('company_s_successfully_deleted');
				break;
		}
		
		$this->alert($sMessage, _p('moderation'), 300, 150, true);
		$this->hide('.moderation_process');
		$this->reload();
	}
    
    public function unfavorite()
    {
        Phpfox::isUser(true);
        
        $sType = $this->get('type');
        $iId = $this->get('id');
        $iUserId = Phpfox::getUserId();
        
        if(Phpfox::getService('jobposting.process')->unfavorite($sType, $iId, $iUserId))
        {
            $this->slideUp('#js_jp_'.$sType.'_entry_' . $iId);
            $this->alert(_p('unfavorite_successfully'), _p('unfavorite'), 300, 150, true);
        }
    }
    
    public function unfollow()
    {
        Phpfox::isUser(true);
        
        $sType = $this->get('type');
        $iId = $this->get('id');
        $iUserId = Phpfox::getUserId();
        
        if(Phpfox::getService('jobposting.process')->unfollow($sType, $iId, $iUserId))
        {
            $this->slideUp('#js_jp_'.$sType.'_entry_' . $iId);
            $this->alert(_p('unfollow_successfully'), _p('unfollow'), 300, 150, true);
        }
    }
    
	public function changeAdminCompany()
	{
		$user_id = $this->get('user_id');
        $id = $this->get('id');
		
		$row = Phpfox::getService('jobposting.company')->getAdminPermission($id, $user_id);
		echo json_encode($row);
	}
	
	public function changeOwner()
	{
		$admin_id = $this->get('admin_id');
		
		if (Phpfox::getService('jobposting.company')->hasCompany($admin_id))
		{
			$this->call("$('#ynjb_loading').hide(); $('#js_jobposting_company_block_admins').show();");
			$this->alert(_p('you_can_not_change_owner_of_this_company_please_choose_another_person'), null, null, null, true);
			return;
		}	
	
		
        $id = $this->get('id');
		
		$aUpdate = array(
			'user_id' => $admin_id
		);
		
		//update user_in in company table
		Phpfox::getLib("database")->update(Phpfox::getT('jobposting_company'), $aUpdate,'company_id = '.$id);
		
		
		$aUpdate = array(
			'user_id' => Phpfox::getUserId(),
			'add_photo' => 1 , 
			'delete_photo' => 1, 
			'buy_packages' => 1, 
			'edit_submission_form' => 1, 
			'add_job' => 1, 
			'edit_job' => 1, 
			'delete_job' => 1, 
			'view_application' => 1, 
			'download_resumes' => 1
		);
		
		//change user_id in company admin and set full permission
		Phpfox::getLib("database")->update(Phpfox::getT('jobposting_company_admin'), $aUpdate,'company_id = '.$id .' AND user_id ='.$admin_id);		
		
		// send notification for requester (approved)
        Phpfox::getService('notification.process')->add('jobposting_' . 'change' . 'owner'
            , $id
            , $admin_id
        );
		
		try{
			// send email to for requester (approved)
	        $aCompany = Phpfox::getService('jobposting.company')->getGeneralInfo($id);
	        $this->sendEmailTransferOwner($admin_id, $aCompany);
				
		}
		catch(Exception $e){
			
		}
		
		$url = Phpfox::getLib('url')->makeUrl('jobposting.company.add', array('id' => $id));		
		$this->call("window.location.href = '" . $url ."';");
	}

	private function sendEmailTransferOwner($admin_id, $aCompany)
	{		  		
		return Phpfox::getLib('mail')->to($admin_id)
            ->subject(_p('owner_has_just_transferred_company_company_title_to_you', array(
                'owner' => Phpfox::getUserBy('full_name'),
                'company_title' => $aCompany['name']
            )))
            ->message(_p('owner_has_just_transferred_company_company_title_to_you_please_check_more_information_at_a_href_company_url_company_title_a', array(
                'owner' => Phpfox::getUserBy('full_name'),
                'company_title' => $aCompany['name'],
                'company_url' => Phpfox::getLib('url')->permalink('jobposting.company', $aCompany['company_id'], $aCompany['name']), 
            )))
            ->send();
		
	}
	public function getJobFriends()
	{
		$sAdmin = $this->get('sAdmin');
        $sAdmin = ltrim($sAdmin, ',');

		$sAdmin = str_replace("$", "'", $sAdmin);
		if($sAdmin!='')
		{
			$aRows = Phpfox::getLib("database")->select('f.*, ' . Phpfox::getUserField())
			->from(Phpfox::getT('friend'), 'f')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_id')
			->where('f.is_page = 0 AND f.friend_user_id NOT IN (' .$sAdmin. ') AND f.user_id = ' . Phpfox::getUserId())
			->limit(Phpfox::getParam('friend.friend_cache_limit'))
			->order('u.last_activity DESC')
			->execute('getSlaveRows');
		}
		else{
			$aRows = Phpfox::getLib("database")->select('f.*, ' . Phpfox::getUserField())
			->from(Phpfox::getT('friend'), 'f')
			->join(Phpfox::getT('user'), 'u', 'u.user_id = f.friend_user_id')
			->where('f.is_page = 0 AND' . ' f.user_id = ' . Phpfox::getUserId())
			->limit(Phpfox::getParam('friend.friend_cache_limit'))
			->order('u.last_activity DESC')
			->execute('getSlaveRows');
		}
		
		foreach ($aRows as $iKey => $aRow)
		{		
			if (Phpfox::getUserId() == $aRow['user_id'])
			{
				unset($aRows[$iKey]);
				
				continue;
			}
			
			$aRows[$iKey]['full_name'] = html_entity_decode(Phpfox::getLib('parse.output')->split($aRow['full_name'], 20), null, 'UTF-8');						
			$aRows[$iKey]['user_profile'] = ($aRow['profile_page_id'] ? Phpfox::getService('pages')->getUrl($aRow['profile_page_id'], '', $aRow['user_name']) : Phpfox::getLib('url')->makeUrl($aRow['user_name']));
			$aRows[$iKey]['is_page'] = ($aRow['profile_page_id'] ? true : false);
			$aRows[$iKey]['user_image'] = Phpfox::getLib('image.helper')->display(array(
					'user' => $aRow,
					'suffix' => '_50_square',
					'max_height' => 50,
					'max_width' => 50,
					'return_url' => true
				)
			);
		}		
		$this->call('$Cache.friends = ' . json_encode($aRows) . ';');
		$this->call("$('input[id^=\"search_input_name\"')[0].value = ''");
		
	}	

	public function showPopupConfirmYesNo()
	{
		$function = $this->get('function');
		
		switch ($function) {
			case 'workingcompany':
					Phpfox::getBlock('jobposting.showpopupconfirmyesno', array(
						'function' =>  $this->get('function'),
						'company_id' =>  $this->get('company_id'),
						'working' =>  $this->get('working'),
						'phare' =>  $this->get('phare')			
					));
				break;
			
			case 'removeWorkingCompany':
					Phpfox::getBlock('jobposting.showpopupconfirmyesno', array(
						'function' =>  $this->get('function'),
						'company_id' =>  $this->get('company_id'),
						'user_id' =>  $this->get('user_id'),
						'type' =>  $this->get('type'),
						'phare' =>  $this->get('phare')			
					));
				break;
			case 'rejectWorkingCompany':
			 	 Phpfox::getBlock('jobposting.showpopupconfirmyesno', array(
						'function' =>  $this->get('function'),
						'company_id' =>  $this->get('company_id'),
						'user_id' =>  $this->get('user_id'),
						'type' =>  $this->get('type'),
						'phare' =>  $this->get('phare')			
					));
			 	break;
		}
		
		
	
	}

    public function updateActivity()
    {
        if ($this->get('jobcat', 0)) {
            Phpfox::getService('jobposting.catjob.process')->updateActivity($this->get('id'), $this->get('active'));
        } else {
            Phpfox::getService('jobposting.category.process')->updateActivity($this->get('id'), $this->get('active'));
        }
    }

    public function categoryOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Phpfox::getService('core.process')->updateOrdering(array(
                'table' => 'jobposting_category',
                'key' => 'category_id',
                'values' => $aVals['ordering']
            )
        );

        Phpfox::getLib('cache')->remove('jobposting_category', 'substr');
    }

    public function catjobOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Phpfox::getService('core.process')->updateOrdering(array(
                'table' => 'jobposting_job_category',
                'key' => 'category_id',
                'values' => $aVals['ordering']
            )
        );

        Phpfox::getLib('cache')->remove('jobposting_job', 'substr');
    }
}

?>
