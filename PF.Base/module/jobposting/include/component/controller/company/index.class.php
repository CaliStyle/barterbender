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


class Jobposting_Component_Controller_Company_Index extends Phpfox_Component 
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
			$aRow['industrial_phrase'] = Phpfox::getService('jobposting.category')->getPhraseCategory($aRow['company_id']);
			$aRow = PHpfox::getService("jobposting.permission")->allpermissionforCompany($aRow,PHpfox::getUserId());
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
                && !$this->request()->get('s')
				&& !$this->request()->get('when')
				&& !$this->request()->get('type')
                && !$this->request()->get('show')
                && $this->request()->get('req3') == '') {
            if (!defined('PHPFOX_IS_USER_PROFILE')) {
                $bIsInHomePage = true;
            }
        }

        return $bIsInHomePage;
    }
		 
	public function process()
	{

		$bInHomepage = $this->_checkIsInHomePage();
        Phpfox::getUserParam('jobposting.can_view_company', true);

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
		//init view
		$sTempView = $this->request()->get('view', false);
		
		$aParentModule = $this->getParam('aParentModule');
        $bIsPage = $aParentModule['module_id'] == 'pages' ? $aParentModule['item_id'] : 0;
 		if ($aParentModule === null && $this->request()->getInt('req3') > 0) {
            return Phpfox::getLib('module')->setController('jobposting.company.view');
        }
		$this->template()->setBreadcrumb(_p('Companies'), $this->url()->makeUrl('jobposting'));
		$this->_buildSubsectionMenu();
		$aSearchNumber = array(10, 20, 30, 40);

		$sActionUrl = (($aParentModule === null ? ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'], array('jobposting.company', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('jobposting.company', array('view' => $this->request()->get('view')))) : $aParentModule['url'] . 'jobposting/view_' . $this->request()->get('view') . '/'));
		$this->search()->set(
                array(
                    'type' => 'company',
                    'field' => 'ca.company_id',
                    'search' => 'search',
                    'search_tool' => array(
                        'table_alias' => 'ca',
                        'search' => array(
                            'action' => $sActionUrl,
                            'default_value' => _p('search_companies'),
                            'name' => 'search',
                            'field' => 'ca.name'
                        ),
                        'sort' => array(
                            'latest' => array('ca.time_stamp', _p('latest')),
                            'most-viewed' => array('ca.total_view', _p('most_viewed')),
                            'most-favorited' => array('ca.total_favorite', _p('most_favorited')),
                        ),
                        'show' => $aSearchNumber
                    )
                )
        );
		
		// Setup search params
        $aBrowseParams = array(
            'module_id' => 'jobposting',
            'alias' => 'ca',
            'field' => 'company_id',
            'table' => Phpfox::getT('jobposting_company')
        );
		
		$bIsAdvSearch = FALSE;
		if($this->search()->get('flag_advancedsearch'))
		{
			$bIsAdvSearch = TRUE;
		}

		if($sTempView == "mycompanies") {
            $this->search()->setCondition("and ca.is_deleted = 0  and ca.user_id = ". Phpfox::getUserId());
        }
        else {
            $this->search()->setCondition(" and ca.is_deleted = 0 AND ca.post_status = 1 ");
            if ($this->request()->get('view') && $this->request()->get('view') == 'pending_companies')
            {
                $this->search()->setCondition(" and ca.is_approved = 0 ");
            }
            else
            {
                $this->search()->setCondition(" and ca.is_approved = 1 ");
            }
        }
		
		if(!$sTempView){
			//company activated
			$this->search()->setCondition(" and ca.is_activated = 1 ");
		}
		if($aParentModule !== null)
		{
			$this->search()->setCondition(' and ca.module_id ="'.$aParentModule['module_id'].'" and ca.item_id ='.(int)$aParentModule['item_id']);
		}
		if($bIsAdvSearch)
		{
			$aVals = Phpfox::getService('jobposting.company')->getAdvSearchFields();
			$_SESSION["ync_company_searchAdv"] = $aVals;
			//var_dump($aVals);
			Phpfox::getService('jobposting.company')->setAdvSearchConditions($aVals);
			//var_dump($this->search()->getConditions());
			//var_dump($aVals);
			//die();
		}
		$bA = Phpfox_Request::instance()->get("bIsAdvSearch");
		//echo $this->search()->getPage()." - ".$bA;
		if ($this->search()->getPage()>1)
		{
			if ($bA=="1")
			{
				$aVals =$_SESSION["ync_company_searchAdv"];
				Phpfox::getService('jobposting.company')->setAdvSearchConditions($aVals);

			}
		}

		
		if($bIsAdvSearch){
			$oServiceCompany = Phpfox::getService('jobposting.company');
			$aVals = $oServiceCompany->getAdvSearchFields();
			
			$this->template()->setHeader(array(
				'<script type="text/javascript">$Behavior.eventEditIndustry = function(){  var aCategories = explode(\',\', \'' . $aVals['categories'] . '\'); for (i in aCategories) { $(\'#js_mp_holder_\' + aCategories[i]).show(); $(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true); } }</script>'
			));
			
			$this->template()->assign(array(
				'aForms' => $aVals,
			));
			
			$oServiceCompany->setAdvSearchConditions($aVals);
		}

		if ($this->request()->get('req3') == 'category')
        {
			$sCategory = $this->request()->getInt('req4');
			if ($aCompanyCategory = Phpfox::getService('jobposting.category')->getForEdit($sCategory))
            {
				$this->search()->setCondition("AND 0<(select(count(*)) from ".Phpfox::getT('jobposting_category_data')." data where data.company_id = ca.company_id and data.category_id in (".$sCategory."))");
                
                $sView = $this->request()->get('view');
    			$aCategories = Phpfox::getService('jobposting.category')->getParentBreadcrumb($sCategory);
                $iCnt = 0;
    			foreach ($aCategories as $aCategory)
    			{
    				$iCnt++;
    				$this->template()->setTitle($aCategory[0]);
    				$this->template()->setBreadcrumb($aCategory[0], $aCategory[1].(isset($sView) ? 'view_'.$sView.'/' : ''), ($iCnt === count($aCategories) ? true : false));
    			}
			}
		}

		$this->search()->browse()->params($aBrowseParams)->execute();
        $this->search()->browse()->setPagingMode(Phpfox::getParam('jobposting.jobposting_company_paging_mode', 'loadmore'));
		$aCompanies = $this->search()->browse()->getRows(); 
		$aCompanies = $this->implementFields($aCompanies);
		
		Phpfox::getLib('pager')->set(array(
		    'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $this->search()->browse()->getCount(),
            'paging_mode' => $this->search()->browse()->getPagingMode()
        ));
		$list_show = Phpfox::getParam('jobposting.company_job_view');

		$this -> template() -> setHeader(array(
			'jobposting.js' => 'module_jobposting',
			'owl.carousel.min.js' => 'module_jobposting',
			'company/company-featured-slideshow.js' => 'module_jobposting',
			'industry.js' => 'module_jobposting'
		));
		if ($this->search()->getPage() >1)
		{
			$bInHomepage = false;
		}


        $aModMenu = [];

        if(Phpfox::getUserParam('jobposting.can_delete_company_other_user'))
        {
            $aModMenu[] = [
                'phrase' => _p('delete'),
                'action' => 'delete'
            ];
        }

        if ($this->request()->get('view') && $this->request()->get('view') == 'pending_companies' && Phpfox::getUserParam('jobposting.can_approve_company'))
        {
            $aModMenu[] = array(
                'phrase' => _p('approve'),
                'action' => 'approve'
            );
        }

        $this->setParam('global_moderation', array(
                'name' => 'jobposting_company',
                'ajax' => 'jobposting.moderationCompany',
                'menu' => $aModMenu
            )
        );


		$this->template()->assign(array(
			'core_path' => Phpfox::getParam('core.path'),
            'coreUrlModule' => Phpfox::getParam('core.path_file').'module/',
            'bInHomepage' => $bInHomepage,
            'aCompanies' => $aCompanies,
            'list_show' => $list_show,
            'sView' => $this->request()->get('view'),
            'bIsAdvSearch' => Phpfox_Request::instance()->get("bIsAdvSearch",false),
            'iPage'	=> $this->search()->getPage(),
            'bIsShowModerator' => count($aModMenu)
		));


		//Special breadcrumb for pages
		if (defined('PHPFOX_IS_PAGES_VIEW') && PHPFOX_IS_PAGES_VIEW && defined('PHPFOX_PAGES_ITEM_TYPE')){
			if (Phpfox::hasCallback(PHPFOX_PAGES_ITEM_TYPE, 'checkPermission') && !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm($aParentModule['item_id'], 'jobposting.view_browse_companies')) {
				$this->template()->assign(['aSearchTool' => []]);
				return \Phpfox_Error::display(_p('Cannot display this section due to privacy.'));
			}
			$this->template()
				->clearBreadCrumb();
			$this->template()
				->setBreadCrumb(Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->getTitle($aParentModule['item_id']), $aParentModule['url'])
				->setBreadCrumb(_p('job_posting'), $aParentModule['url'] . 'jobposting/');
		}
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('jobposting.Jobposting_Component_Controller_Company_Index_clean')) ? eval($sPlugin) : false);
	}

}

