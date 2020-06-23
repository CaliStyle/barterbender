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

class JobPosting_Component_Controller_Company_View extends Phpfox_Component 
{
	public function process(){
	   
        Phpfox::getUserParam('jobposting.can_view_company', true);
      
		$sTempView = $this->request()->get('view', false);
		$aCompany = "";
		
        if($sTempView=="mycompany"){
			$aCompany = Phpfox::getService('jobposting.company')->getCompany(Phpfox::getUserId());	
			
			if(!$aCompany)
				Phpfox::getLib("url")->send("jobposting.company.add");
		}
		else {
			$id = $this->request()->get('req3');
                       
			$aCompany = Phpfox::getService('jobposting.company')->getForEdit($id);
                         
		}
                if(!$aCompany){
                    return Phpfox_Error::display(_p('the_company_you_are_looking_for_cannot_be_found'));
                }
	    Phpfox::getService('jobposting.company.process')->updateTotalJob($aCompany['company_id']);  
        
            $iCompanyId = $aCompany['company_id'];
        
        #Permission and privacy
        if (!Phpfox::getService('jobposting.permission')->canViewCompany($iCompanyId, Phpfox::getUserId()))
        {
            return Phpfox_Error::display(_p('the_company_you_are_looking_for_cannot_be_found'));
        }
        
        if (Phpfox::isModule('privacy'))
		{
			Phpfox::getService('privacy')->check('jobposting', $aCompany['company_id'], $aCompany['user_id'], $aCompany['privacy'], $aCompany['is_friend']);
		}
        
        #Send invitations
		$aVals = $this->request()->getArray('val');
		if(!empty($aVals['submit_invite']))
		{
			Phpfox::getService('jobposting.process')->sendInvitations('company', $iCompanyId, $aVals);
		}
        
		$iPage = 1;
		$aConds = 'job.company_id = '.$iCompanyId;
		$iLimit = 50;
		$ViewMore = 0;
		
		if($aCompany['user_id'] == PHpfox::getUserId() || PHpfox::isAdmin()){
			
		}
		else
		{
			$aConds.= " and job.post_status = 1 and job.time_expire>".PHPFOX_TIME;
		}
		
		list($iCntSearch, $aJobsSearch) = Phpfox::getService("jobposting.job")->searchJobs($aConds, 'job.title ASC', $iPage, $iLimit);
		if(($iPage*$iLimit)<$iCntSearch)
		{
			$ViewMore = 1;
		}
        
        #Favorite
        $iIsFavorited = (Phpfox::getService('jobposting')->isFavorited('company', $iCompanyId, Phpfox::getUserId()) ? 1 : 0);
        
		#Follow
        $iIsFollowed = (Phpfox::getService('jobposting')->isFollowed('company', $iCompanyId, Phpfox::getUserId()) ? 1 : 0);
        
		$this->setParam('aCompany',$aCompany);
		
		//Activity Feed
		$bCanPostComment = true;
		if ($aCompany['user_id'] != Phpfox::getUserId()
			&& !Phpfox::getUserParam('privacy.can_comment_on_all_items'))
		{
			$bCanPostComment = false;
		}

		if (Phpfox::getUserId())
		{
			$bIsBlocked = Phpfox::getService('user.block')->isBlocked($aCompany['user_id'], Phpfox::getUserId());
			if ($bIsBlocked)
			{
				$bCanPostComment = false;
			}
		}
		
		$aFields = Phpfox::getService('jobposting.custom')->getByObjectId($iCompanyId, 1);
		//get custom field
		foreach($aFields as $k=>$aField)
        {
            if($aField['var_type'] != 'text' && $aField['var_type'] != 'textarea' && empty($aField['option']))
            {
                unset($aFields[$k]);
            }
        }	

		$this->setParam('aFeedCallback', array(
				'module' => 'jobposting',
				'table_prefix' => 'jobposting_',
				'ajax_request' => 'jobposting.addFeedComment',
				'item_id' => $iCompanyId,
				'disable_share' => ($bCanPostComment ? false : true)
			)
		);
		//end Activity Feed
		if (!empty($aCompany['module_id']) && $aCompany['module_id'] != 'jobposting')
		{
			if (Phpfox::hasCallback($aCompany['module_id'], 'getCompanyDetails'))
			{
				$aCallback = Phpfox::callback($aCompany['module_id'] . '.getCompanyDetails', $aCompany);
			}
			else
			{
				$aCallback = $this->getCompanyDetails($aCompany);

			}
			$this->template()
				->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home'])
				->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
			if (($aCompany['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aCallback['item_id'], '')) || $aCompany['module_id'] == 'groups' && !Phpfox::getService('groups')->hasPerm($aCallback['item_id'], ''))
			{
				return \Phpfox_Error::display(_p('Unable to view this item due to privacy settings'));
			}
		}
		else {
			$this->template()->setTitle($aCompany['name'])
				->setBreadCrumb(_p('job_posting'), $this->url()->makeUrl('jobposting'))
				->setBreadCrumb(_p('company'), $this->url()->makeUrl('jobposting.company'), true)
				->setBreadCrumb('', '', true);
		}

        // Get Image for Facebook
        if (!empty($aCompany['image_path'])) {
            $aCompany['image'] = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aCompany['server_id'],
                'path' => 'core.url_pic',
                'file' => 'jobposting' . PHPFOX_DS . $aCompany['image_path'],
                'suffix' => '_500',
                'return_url' => true
            ));
            $size_img = @getimagesize($aCompany['image']);
            if (!empty($size_img)) {
                $og_image_width = $size_img[0];
                $og_image_height = $size_img[1];
            } else {
                $og_image_width = 640;
                $og_image_height = 442;
            }
            $this->template()
                ->setMeta('og:image', $aCompany['image'])
                ->setMeta('og:image:width', $og_image_width)
                ->setMeta('og:image:height', $og_image_height);
        }
        $aCompany['bookmark_url'] = Phpfox::permalink('jobposting', $aCompany['company_id'], $aCompany['name']);

        $this->template()
        ->setMeta('description', $aCompany['description'])
        ->setMeta('keywords', $this->template()->getKeywords($aCompany['name']))
        ->setHeader(array(
            'feed.js' => 'module_feed',
            'jquery/plugin/jquery.highlightFade.js' => 'static_script',
            'jquery/plugin/jquery.scrollTo.js' => 'static_script',
            'jobposting.js' => 'module_jobposting',
            'thickbox/thickbox.js' => 'static_script',
        ))->assign(array(
            'aCompany' => Phpfox::getService("jobposting.permission")->allPermissionForCompany($aCompany,
                Phpfox::getUserId()),
            'aJobsSearch' => $aJobsSearch,
            'iCntSearch' => $iCntSearch,
            'ViewMoreJob' => $ViewMore,
            'iPage' => $iPage,
            'iIsFavorited' => $iIsFavorited,
            'aFields' => $aFields,
            'iIsFollowed' => $iIsFollowed
        ));
		Phpfox::getService('jobposting.helper')->buildMenu();

	}
	public function getCompanyDetails($aItem)
	{
		Phpfox::getService('pages')->setIsInPage();

		$aRow = Phpfox::getService('groups')->getPage($aItem['item_id']);

		if (!isset($aRow['page_id']))
		{
			return false;
		}

		Phpfox::getService('groups')->setMode();

		$sLink = Phpfox::getService('groups')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

		return array(
			'breadcrumb_title' => _p('Groups'),
			'breadcrumb_home' => \Phpfox_Url::instance()->makeUrl('groups'),
			'module_id' => 'groups',
			'item_id' => $aRow['page_id'],
			'title' => $aRow['title'],
			'url_home' => $sLink,
			'url_home_photo' => $sLink . 'jobposting/',
			'theater_mode' => _p('pages.in_the_page_link_title', array('link' => $sLink, 'title' => $aRow['title']))
		);
	}
}

?>