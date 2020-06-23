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


class Jobposting_Component_Controller_Index extends Phpfox_Component 
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	
	private $_aParentModule = null;
	 
	private function _buildSubsectionMenu() {
        if ($this->_aParentModule === null && !defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW')) {
            Phpfox::getService('jobposting.helper')->buildMenu();
        }
    }
	private function implementFields($aRows)
	{		
		foreach($aRows as $key=>$aRow)
		{
			$aRow['time_expire_month'] = Phpfox::getTime('n', $aRow['time_expire'],false);
			$aRow['time_expire_day'] = Phpfox::getTime('j', $aRow['time_expire'], false);
			$aRow['time_expire_year'] = Phpfox::getTime('Y', $aRow['time_expire'],false);
			$aRow['time_expire_phrase'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aRow['time_expire']);
			
			$aRow['industrial_phrase'] = Phpfox::getService('jobposting.category')->getPhraseCategory($aRow['company_id']);
			$aRow = PHpfox::getService("jobposting.permission")->allpermissionforJob($aRow,PHpfox::getUserId());
			$aRows[$key] = $aRow;
		}
		
		return $aRows;
	}
	
	 private function _checkIsInHomePage() {
        $bIsInHomePage = false;
        $aParentModule = $this->getParam('aParentModule');
        $sTempView = $this->request()->get('view', false);


        if ($sTempView == "" && !isset($aParentModule['module_id']) && !$this->request()->get('search-id')
        		&& !$this->request()->get('bIsAdvSearch')
                && !$this->request()->get('sort')
				&& !$this->request()->get('when')
				&& !$this->request()->get('type')
                && !$this->request()->get('show')
                && $this->request()->get('req2') == '') {
            if (!defined('PHPFOX_IS_USER_PROFILE')) {
                $bIsInHomePage = true;
            }
        }

        return $bIsInHomePage;
    }
		 
	public function process()
	{
		$aParentModule = $this->getParam('aParentModule');
		if(!Phpfox::getUserParam('jobposting.can_browse_view_job')) {
		    return Phpfox_Module::instance()->setController('jobposting.company.index');
        }

		if ($aParentModule !== null && in_array($aParentModule['module_id'],['pages','groups'])) {
			return Phpfox::getLib('module')->setController('jobposting.company.index');
		}

		if(isset($_POST['search']['country_child_id']))
		{
			$_SESSION['ynjobposting_country_child_id'] = (int)$_POST['search']['country_child_id'];
		}
		$js_end__datepicker = $this->request()->get('js_end__datepicker');
		if(!empty($js_end__datepicker))
		{
			$searchVal = $this->request()->get('val');
			$_SESSION['ynjobposting_end_year'] = $searchVal['end_year'];
			$_SESSION['ynjobposting_end_month'] = $searchVal['end_month'];
			$_SESSION['ynjobposting_end_day'] = $searchVal['end_day'];
		}
		$this->template()->setBreadcrumb(_p('job_posting'), $this->url()->makeUrl('jobposting'));
		
		$bInHomepage = $this->_checkIsInHomePage();
        if ($bInHomepage)
		{
			$this -> template() ->setBreadcrumb(_p('jobs'),'',true);
		}
		$bIsProfile = false;
        $aUser = null;
		
        if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            $bIsProfile = true;
            $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $aUser);
        } else {
            $bIsProfile = $this->getParam('bIsProfile');
            if ($bIsProfile === true) {
                $aUser = $this->getParam('aUser');
            }
        }
	
		$aParentModule = $this->getParam('aParentModule');
        $bIsPage = $aParentModule['module_id'] == 'pages' ? $aParentModule['item_id'] : 0;
 		if ($aParentModule === null && $this->request()->getInt('req2') > 0) {
            return Phpfox::getLib('module')->setController('jobposting.view');
        }
 		$this->_buildSubsectionMenu();

 		$view = $this->request()->get('view', '');
		$aSearchNumber = array(10, 20, 30, 40);
		$sActionUrl = ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'], array('jobposting', 'view' => $view)) : $this->url()->makeUrl('jobposting', array('view' => $view)));
		$this->search()->set(
                array(
                    'type' => 'job',
                    'field' => 'job.job_id',
                    'search' => 'search',
                    'search_tool' => array(
                        'table_alias' => 'job',
                        'search' => array(
                            'action' => $sActionUrl,
                            'default_value' => _p('search_jobs'),
                            'name' => 'search',
                            'field' => 'job.title'
                        ),
                        'sort' => array(
                            'latest' => array('job.time_stamp', _p('latest')),
                            'most-viewed' => array('job.total_view', _p('most_viewed')),
                            'most-favorited' => array('job.total_favorite', _p('most_favorited')),
                        ),
                        'show' => $aSearchNumber
                    )
                )
        );
	
		// Setup search params
        $aBrowseParams = array(
            'module_id' => 'jobposting',
            'alias' => 'job',
            'field' => 'job_id',
            'table' => Phpfox::getT('jobposting_job'),
            'hide_view' => array()
        );
		
		$bIsAdvSearch = FALSE;
		if($this->search()->get('flag_advancedsearch'))
		{
			$bIsAdvSearch = TRUE;
		}

		if($view == 'appliedjob') {

        }
        else {
            if (!empty($view) && ($view == 'pending_jobs'))
            {
                Phpfox::getUserParam('jobposting.can_approve_job',true);
                $this->search()->setCondition(" AND job.is_approved = 0 ");
            }
            else
            {
                $this->search()->setCondition(" AND job.is_approved = 1 AND job.is_hide = 0");
            }
            $this->search()->setCondition(" AND job.is_deleted = 0 AND job.post_status = 1");
            $this->search()->setCondition(" AND job.time_expire > ".PHPFOX_TIME);
        }

		if(empty($view)){
			 $this->search()->setCondition(" AND job.is_activated = 1");
		}

        // search ajax
        $bA = Phpfox_Request::instance()->get("bIsAdvSearch");

        if ($bA=="1")
        {
            if ($this->search()->getPage()>1)
            {
                $aVals = $_SESSION[Phpfox::getParam('core.session_prefix')."ynjobposting_searchAdv"];
                $bIsAdvSearch = true;

            }
        }

		if($bIsAdvSearch){
			$oServiceJob = Phpfox::getService('jobposting.job');

            if ($this->search()->getPage()<=1)
            {
                $aVals = $oServiceJob->getAdvSearchFields();
                $_SESSION[Phpfox::getParam('core.session_prefix')."ynjobposting_searchAdv"] = $aVals;
            }

			$this->template()->setHeader(array(
				'<script type="text/javascript">$Behavior.eventEditIndustry = function(){  var aCategories = explode(\',\', \'' . $aVals['categories'] . '\'); for (i in aCategories) { $(\'#js_mp_holder_\' + aCategories[i]).show(); $(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true); } }</script>'
			));
			
			$this->template()->assign(array(
				'aForms' => $aVals,
			));
            $oServiceJob->setAdvSearchConditions($aVals);
			$this->template()->assign(array(
				'aForms' => $aVals,
			));
        }
		else {
			$this->template()->assign(array(
				'aForms' => array(),
			));
		}
		
		$aSubscribe = PHpfox::getService("jobposting.job")->getSubscribe();
		
		if(isset($aSubscribe['subscribe_id']))
		{
			$aVals['categories'] = "";
			if($aSubscribe['industry']>0)
			{
				$aVals['categories'] = $aSubscribe['industry'];
				if($aSubscribe['industry_child']>0)
				{
					$aVals['categories'] .= ",".$aSubscribe['industry_child'];
				}
			}
			
			$this->template()->setHeader(array(
				'<script type="text/javascript">popup = 0; $Behavior.eventEditIndustry1 = function(){ if(popup==1){var aCategories = explode(\',\', \'' . $aVals['categories'] . '\'); for (i in aCategories) { $(\'#popup_js_mp_holder_\' + aCategories[i]).show(); $(\'#popup_js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true); } }}</script>'
			));
		}
		
		if ($this->request()->get('req2') == 'category')
        {
			$sCategory = $this->request()->getInt('req3');
			if ($aCompanyCategory = Phpfox::getService('jobposting.category')->getForEdit($sCategory))
            {
				$this->search()->setCondition("AND 0<(select(count(*)) from ".Phpfox::getT('jobposting_category_data')." data where data.company_id = job.company_id AND data.category_id in (".$sCategory."))");
    			$aCategories = Phpfox::getService('jobposting.category')->getParentBreadcrumb($sCategory);
                $iCnt = 0;
    			foreach ($aCategories as $aCategory)
    			{
    				$iCnt++;
    				$this->template()->setTitle($aCategory[0]);
    				$this->template()->setBreadcrumb($aCategory[0], $aCategory[1].(isset($view) ? 'view_'.$view.'/' : ''), ($iCnt === count($aCategories) ? true : false));
    			}
			}
		}

		$this->search()->browse()->params($aBrowseParams)->execute();
		
		$aJobs = $this->search()->browse()->getRows();
        $this->search()->browse()->setPagingMode(Phpfox::getParam('jobposting.jobposting_job_paging_mode', 'loadmore'));
		$aJobs = $this->implementFields($aJobs);
		
		Phpfox::getLib('pager')->set(array(
		    'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $this->search()->browse()->getCount(),
            'paging_mode' => $this->search()->browse()->getPagingMode()
        ));
		
		$list_show = Phpfox::getParam('jobposting.company_job_view');
		
		$iFeatureJobFee = Phpfox::isAdmin() ? 0 : Phpfox::getParam('jobposting.fee_feature_job');

		$this -> template() -> setHeader(array(
            'jobposting.js' => 'module_jobposting',
            'search.js' => 'module_jobposting',
			'homepageslider/slides.min.jquery.js' => 'module_jobposting',
			'owl.carousel.min.js' => 'module_jobposting',
            'pager.css' => 'style_css',
            'country.js' => 'module_core',
            'industry.js' => 'module_jobposting'
		));


		if ($this->search()->getPage() >1)
		{
			$bInHomepage = false;
		}
        $aModMenu = [];

		if(Phpfox::getUserParam('jobposting.can_delete_job_other_user'))
        {
            $aModMenu[] = [
                'phrase' => _p('delete'),
                'action' => 'delete'
            ];
        }

        if (!empty($view) && ($view == 'pending_jobs') && Phpfox::getUserParam('jobposting.can_approve_job'))
        {
            $aModMenu[] = array(
                'phrase' => _p('approve'),
                'action' => 'approve'
            );
        }

        $this->setParam('global_moderation', array(
                'name' => 'jobposting_job',
                'ajax' => 'jobposting.moderationJob',
                'menu' => $aModMenu,
            )
        );

        $this->template()->assign(array(
            'bInHomepage' => $bInHomepage,
            'aJobs' => $aJobs,
            'list_show' => $list_show,
            'core_path' => Phpfox::getParam('core.path'),
            'coreUrlModule' => Phpfox::getParam('core.path_file').'module/',
            'iFeatureJobFee' => $iFeatureJobFee,
            'sView' => $view,
            'bIsAdvSearch' => Phpfox_Request::instance()->get("bIsAdvSearch",false),
            'iPage'	=> $this->search()->getPage(),
            'bIsShowModerator' => count($aModMenu)
		));
    }

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('jobposting.Jobposting_Component_Controller_Index_clean')) ? eval($sPlugin) : false);
	}

}

