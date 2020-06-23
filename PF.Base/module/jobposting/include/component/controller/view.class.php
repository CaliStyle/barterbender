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

class JobPosting_Component_Controller_View extends Phpfox_Component 
{

	public function process(){
	
        Phpfox::getUserParam('jobposting.can_browse_view_job', true);
        
		$job_id = $this->request()->get('req2');
		$oServiceJobs = Phpfox::getService('jobposting.job');
		
	
        #Permission and privacy
		if (!Phpfox::getService('jobposting.permission')->canViewJob($job_id, Phpfox::getUserId()))
        {
            return Phpfox_Error::display(_p('the_job_you_are_looking_for_cannot_be_found'));
        }
       
        $aJob = $oServiceJobs->getJobByJobId($job_id);
		
		$this->setParam('time_expire', $aJob['time_expire']);	
		
        if (Phpfox::isModule('privacy'))
		{
			Phpfox::getService('privacy')->check('jobposting', $aJob['job_id'], $aJob['user_id'], $aJob['privacy'], $aJob['is_friend']);
		}
		
		if ($aVals = $this->request()->getArray('val'))
		{
            if(!empty($aVals['submit_invite'])) //Send invitations
    		{
    			Phpfox::getService('jobposting.process')->sendInvitations('job', $job_id, $aVals);
    		}
            elseif($this->_verifyApplyForm($aVals, $aJob['company_id']))
            {
                Phpfox::getService("jobposting.job.process")->addApplication($job_id,$aVals);
                Phpfox::getLib('url')->send('current',array(),"Apply Job successfully");
            }
		}
		
		$aCompany = Phpfox::getService('jobposting.company')->getForEdit($aJob['company_id']);
		
		$this->setParam('aCompany',$aCompany);	
		$aItem = $aJob;
		
		PHpfox::getService('jobposting.company.process')->updateTotalJob($aJob['company_id']);
		$aItem['bookmark_url'] = Phpfox::permalink('jobposting', $aItem['job_id'], $aItem['title']);
		
        #Favorite
        $iIsFavorited = (Phpfox::getService('jobposting')->isFavorited('job', $job_id, Phpfox::getUserId()) ? 1 : 0);
		
		#Follow
		$iIsFollowed = (Phpfox::getService('jobposting')->isFollowed('job', $job_id, Phpfox::getUserId()) ? 1 : 0);
        
        $bIsApplied = Phpfox::getService('jobposting.application')->isApplied($job_id, Phpfox::getUserId());
		
        $iFeatureJobFee = Phpfox::isAdmin() ? 0 : Phpfox::getParam('jobposting.fee_feature_job');
        
		$this->setParam('aFeed', array(				
				'comment_type_id' => 'jobposting_job',
				'privacy' => $aItem['privacy'],
				'like_type_id' => 'jobposting_job',
				'feed_is_liked' => isset($aItem['is_liked']) ? $aItem['is_liked'] : false,
				'feed_is_friend' => $aItem['is_friend'],
				'item_id' => $aItem['job_id'],
				'user_id' => $aItem['user_id'],
				'total_comment' => $aItem['total_comment'],
				'total_like' => $aItem['total_like'],
				'feed_link' => $aItem['bookmark_url'],
				'feed_title' => $aItem['title'],
				'feed_display' => 'view',
				'feed_total_like' => $aItem['total_like'],
				'report_module' => 'jobposting_job',
				'report_phrase' => _p('report_this_job'),
				'time_stamp' => $aItem['time_stamp']
			)
		);
		
		//GET VALUE OF CUSTOM FIELD
		$aFields = Phpfox::getService('jobposting.custom')->getByObjectId($job_id, 2, true);
		//get custom field
		foreach($aFields as $k=>$aField)
        {
            if($aField['var_type'] != 'text' && $aField['var_type'] != 'textarea' && empty($aField['option']))
            {
                unset($aFields[$k]);
            }
        }
        
        $this->template()
            ->setPhrase(array(
                'select_all',
                'un_select_all'
            ))
            ->setTitle($aJob['title'])
            ->setBreadCrumb(_p('job_posting'), $this->url()->makeUrl('jobposting'))
            ->setBreadCrumb('', '', true);
		
		$this -> template() -> setHeader(array(
	   		'feed.js' => 'module_feed',
	   		'quick_edit.js' => 'static_script',
	   		'switch_menu.js' => 'static_script',
			'jquery/plugin/jquery.highlightFade.js' => 'static_script',
			'jquery/plugin/jquery.scrollTo.js' 		=> 'static_script',

            'jobposting.js' => 'module_jobposting',
	  	));
        // Get Image for Facebook
        if (!empty($aCompany['image_path'])) {
            $aJob['image'] = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aCompany['server_id'],
                'path' => 'core.url_pic',
                'file' => 'jobposting' . PHPFOX_DS . $aCompany['image_path'],
                'suffix' => '_500',
                'return_url' => true
            ));
            $size_img = @getimagesize($aJob['image']);
            if (!empty($size_img)) {
                $og_image_width = $size_img[0];
                $og_image_height = $size_img[1];
            } else {
                $og_image_width = 640;
                $og_image_height = 442;
            }
            $this->template()
                ->setMeta('og:image', $aJob['image'])
                ->setMeta('og:image:width', $og_image_width)
                ->setMeta('og:image:height', $og_image_height);
        }
        $aJob['bookmark_url'] = Phpfox::permalink('jobposting', $aJob['job_id'], $aJob['title']);

		$this->template()
            ->setMeta('description', $aJob['description'])
            ->setMeta('keywords', $this->template()->getKeywords($aJob['title']))
            ->assign(array(
			'aJob' => PHpfox::getService("jobposting.permission")->allpermissionforJob($aJob,Phpfox::getUserId()),
			'aCompany' => $aCompany,
            'iIsFavorited' => $iIsFavorited,
            'iIsFollowed' => $iIsFollowed,
            'bIsApplied' => $bIsApplied,
            'aFields' => $aFields,
            'iFeatureJobFee' => $iFeatureJobFee
		));

		Phpfox::getService('jobposting.helper')->buildMenu();
        (($sPlugin = Phpfox_Plugin::get('jobposting.component_controller_view_end')) ? eval($sPlugin) : false);
        
	}
    
    private function _verifyApplyForm($aVals, $iCompanyId)
    {
        #Custom fields
        $aFields = Phpfox::getService('jobposting.custom')->getByCompanyId($iCompanyId);
        foreach($aFields as $k=>$aField)
        {
            if($aField['var_type'] != 'text' && $aField['var_type'] != 'textarea' && empty($aField['option']))
            {
                continue;
            }
            
            if($aField['is_required'] && empty($aVals['custom'][$aField['field_id']]))
            {
                return Phpfox_Error::set(_p('custom_field_is_required', array('custom_field' => _p($aField['phrase_var_name']))));
            }
        }
        
		$aCompany = Phpfox::getService('jobposting.company')->getForEdit($iCompanyId);
		if($aCompany){
			if($aCompany['candidate_name_enable']==1 && $aCompany['candidate_name_require']==1 && strlen(trim($aVals['name']))==0)
			{
				return Phpfox_Error::set(_p('name_isn_t_allowed_empty'));
			}
		}		
        return true;
    }
}

?>