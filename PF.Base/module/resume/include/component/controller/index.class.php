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
class Resume_Component_Controller_Index extends Phpfox_Component
{
	/*
	 * Process method which is used to process this component
	 */
	public function process() 
	{
		// Checking user view permission

		if (defined('PHPFOX_IS_USER_PROFILE') && ($sLegacyTitle = $this->request()->get('req3')) && !empty($sLegacyTitle))
		{			
			Phpfox::getService('core')->getLegacyItem(array(
					'field' => array('resume_id', 'headline'),
					'table' => 'resume_basicinfo',		
					'redirect' => 'resume',
					'title' => $sLegacyTitle
				)
			);
		}		
		
		// Check if we are on profile page or not then get user information
		$bIsProfile = false;
		$bIsSelfView = false;
		if (defined('PHPFOX_IS_AJAX_CONTROLLER'))
		{
			$bIsProfile = true;
			$aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
			$this->setParam('aUser', $aUser);
		}
		else 
		{		
			$bIsProfile = $this->getParam('bIsProfile');	
			if ($bIsProfile === true)
			{
				$aUser = $this->getParam('aUser');
			}
		}

		// Build filter section menu on left side
		$this -> template() ->setBreadcrumb(_p('resume.resume'),$this->url()->makeUrl('resume'));
		
		Phpfox::getService('resume')->buildSectionMenu();

		if (Phpfox::getUserParam('resume.can_create_resumes')) {
            if(Phpfox::isModule('socialbridge'))
            {
                $config = Phpfox::getService('socialbridge') -> getSetting('linkedin');
                if(Phpfox::getService('socialbridge') -> hasProvider('linkedin') && !empty($config['api_key']) && !empty($config['secret_key']))
                {
                    sectionMenu('', 'resume.import', array('css_class' => 'hidden js_resume_import_from_linkedin '));
                }
            }
        }

		// Get view mode
		$sView = $this->request()->get('view');
		
		// Set action url for searching
		$sActionUrl = ($bIsProfile === true ?$this->url()->
		makeUrl($aUser['user_name'],
			array('resume')):$this->url()->makeUrl('resume',array('view' => $sView)));
		
		// Set up variables and search fields
		$sSearchNumber = Phpfox::getParam('resume.total_resume_display');
		
		if($sSearchNumber)
		{
			$aSearchNumber = explode(',',str_replace(" ", "", Phpfox::getParam('resume.total_resume_display')));
		}
		else 
		{
			$aSearchNumber = array(10,15,20,25);
		}

		$this->search()->set(
			array(
				'type' => 'resume',
				'field'=> 'rbi.resume_id',
				'search' =>	'search',
				'search_tool' => array(
					'table_alias'  => 'rbi',
					'search'=> array(
						'action' 	   => $sActionUrl,
						'default_value'=> _p('resume.search_resumes'),
						'name'		   => 'search',
						'field'		   => array('rbi.headline','rbi.full_name'),
					),
					'sort'	=> array(
						'latest' 		 => array('rbi.time_stamp', _p('resume.latest')),
						'most-viewed' 	 => array('rbi.total_view', _p('resume.most_viewed')),
						'most-favorited' => array('rbi.total_favorite', _p('resume.most_favorited')),
					),
					'show' => $aSearchNumber
				)
			)
		);
		
		// Setup Approving status
		$bIsApproving = false;
		
		// Filter view mode
		switch ($sView)
		{
			case 'my':
				Phpfox::isUser(true);
				$this->search()->setCondition('AND rbi.user_id = ' . Phpfox::getUserId());	
			
				// Checking edit/delete allowance on item
				$bCanEdit   = true;
				$bCanDelete = Phpfox::getUserParam('resume.can_delete_own_resumes');
				$bIsApproving = Phpfox::getService('resume')
					->checkApprovingStatus(Phpfox::getUserId());
				
				// Set up moderation tab on my recording page
				$this->setParam('global_moderation', array(
						'name' => 'resume',
						'ajax' => 'resume.moderation',
						'menu' => array(
							array(
								'phrase' => _p('resume.delete'),
								'action' => 'delete'
							)
						)
					));
				break;



			case 'noted':
				Phpfox::isUser(true);		
				// Checking edit/delete allowance on item
				$bCanEdit   = TRUE;
				$bCanDelete = Phpfox::getUserParam('resume.can_delete_own_resumes');
				
				$this->search()->setCondition("AND rbi.is_published = 1");
				$this->search()->setCondition("AND rbi.status = 'approved'");
				$this->search()->setCondition("AND rv.note <> ''");
			break;
			case 'favorite':
				Phpfox::isUser(true);				
				// Checking edit/delete allowance on item
				$bCanEdit   = TRUE;
				$bCanDelete = Phpfox::getUserParam('resume.can_delete_own_resumes');
				
				$this->search()->setCondition("AND rbi.is_published = 1");
				$this->search()->setCondition("AND rbi.status = 'approved'");
				break;
			case 'pending':
				Phpfox::isUser(true);
				$bCanEdit   = TRUE;
				$bCanDelete = Phpfox::getUserParam('resume.can_delete_own_resumes');
				$this->search()->setCondition("AND rbi.is_published = 1");
				$this->search()->setCondition("AND rbi.status = 'approving'");
				break;
			default:
					
				// Checking edit/delete allowance on item
				if ($bIsProfile === true)
				{
					$iViewerId = Phpfox::getUserId();
					
					if($iViewerId == $aUser['user_id'])
					{
						$bCanEdit   = TRUE;
						$bCanDelete = Phpfox::getUserParam('resume.can_delete_own_resumes');
						$this->request()->set(array('bIsSelfView' => TRUE));
						$bIsSelfView = TRUE;
					}
					else
					{
						$bCanEdit   = Phpfox::getUserParam('resume.can_edit_other_resume');
						$bCanDelete = Phpfox::getUserParam('resume.can_delete_other_resumes');
						$this->search()->setCondition("AND rbi.is_published = 1");
						$this->search()->setCondition("AND rbi.status = 'approved'");
					}
					$this->search()->setCondition("AND rbi.user_id = {$aUser['user_id']}");
				}
				else
				{
					$bCanEdit   = FALSE;
					$bCanDelete = FALSE;
					$this->search()->setCondition("AND rbi.is_published = 1");
					$this->search()->setCondition("AND rbi.status = 'approved'");
				}
				break;	
		}	
		// Setup search conditions
			
		
		// Check if we are on advanced search mode
		$bIsAdvSearch = FALSE;
		if($this->search()->get('form_flag'))
		{
			$bIsAdvSearch = TRUE;
		}
		elseif($this->search()->get('submit') == _p('resume.reset'))
		{
			$bIsAdvSearch = TRUE;
		}

		if($bIsAdvSearch)
		{
			$aVals = Phpfox::getService('resume')->getAdvSearchFields();
			$_SESSION["searchAdv"] = $aVals;
			Phpfox::getService('resume')->setAdvSearchConditions($aVals);
		}
		$bA = Phpfox_Request::instance()->get("bIsAdvSearch");
		if ($this->search()->getPage()>1)
		{
			if ($bA=="1")
			{
				$aVals =$_SESSION["searchAdv"];
				Phpfox::getService('resume')->setAdvSearchConditions($aVals);

			}
		}
		// For category searching
		if ($this->request()->get(($bIsProfile === true ? 'req3' : 'req2')) == 'category')
		{
			$iCategoryId = $this->request()->get(($bIsProfile === true ? 'req4' : 'req3'));

			if ($aCategory = Phpfox::getService('resume.category')->getCategory($iCategoryId))
			{
				if(!$bIsAdvSearch)
				{
					$this->search()->setCondition('AND (rc.category_id = ' . $iCategoryId.' AND rc.is_active = 1)');
				}
				$this->template()->setTitle(Phpfox::getLib('locale')->convert(_p($aCategory['name'])));
				$this->template()->setBreadCrumb(Phpfox::getLib('locale')->convert($aCategory['name']), $this->url()->makeUrl('current'), true);
				$this->search()->setFormUrl($this->url()->permalink(array('resume.category', 'view' => $sView), $aCategory['category_id'], $aCategory['name']));
			}
		}
                //support Privacy
                //0: Everyone
                //1: Job Seeker Group
                //2: Friend
                //3: Only me
                $user_id_viewer = Phpfox::getUserId();
                $bViewResumeRegistration = Phpfox::getService('resume.account')->checkViewResumeRegistration($user_id_viewer);
                $privacyfriend = 'rbi.privacy=2 and 0<(select count("*") from '.Phpfox::getT('friend').' f where f.user_id = rbi.user_id AND f.friend_user_id = '.$user_id_viewer.')';
                $this->search()->setCondition(' and( rbi.user_id='.$user_id_viewer.' or rbi.privacy=0 or (rbi.privacy=1 and 1="'.$bViewResumeRegistration.'") or (rbi.privacy=1 and rbi.user_id='.$user_id_viewer.') or ('.$privacyfriend.')  )');
                
		// Setup search params
		$aBrowseParams = array(
			'module_id' => 'resume',
			'alias' => 'rbi',
			'field' => 'resume_id',
			'table' => Phpfox::getT('resume_basicinfo'),
			'select' =>'rbi.full_name as resume_full_name,'
		);

		
		if($sView == "noted")
		{
			$aBrowseParams['select'] = "rv.note,";
		}

		$this->search()->browse()->params($aBrowseParams)
            ->setPagingMode(Phpfox::getParam('resume.resume_paging_mode', 'loadmore'))
            ->execute();
		
		// Resume item list
		$aResumes = $this->search()->browse()->getRows();

		// Setup pager
		Phpfox::getLib('pager')->set(
			array(
				'page'  => $this->search()->getPage(), 
				'size'  => $this->search()->getDisplay(), 
				'count' => $this->search()->browse()->getCount(),
                'paging_mode' => $this->search()->browse()->getPagingMode()
			)
		);


		// Get view status and note if have
		$aResumeIds = array(-1);
		foreach($aResumes as $aResume)
		{
			$aResumeIds[] = $aResume['resume_id'];
		}

		// Get viewed resumes of current viewer
		$aViewList = Phpfox::getService('resume.viewme')->getViewList($aResumeIds);
		
		//Get category list of resumes in selected page
		$aCatList = Phpfox::getService('resume.category')
			->getCatNameListFromSelectedResumes($aResumeIds);
		
		foreach($aResumes as $iKey => $aResume)
		{

			$aResumes[$iKey]['is_viewed'] = 0;
			$aResumes[$iKey]['noted'] = "";
			$aResumes[$iKey]['view_time'] = 0;
			$aResumes[$iKey]['sent_messages'] = 0;
			$aResumes[$iKey]['categories'] ="";
			if(!empty($aViewList))
			{
				foreach($aViewList as $aViewItem)
				{
					if($aResume['resume_id'] == $aViewItem['resume_id'])
					{
						$aResumes[$iKey]['is_viewed'] = 1;
						$aResumes[$iKey]['noted'] = Phpfox::getLib('parse.input')->clean($aViewItem['note']);	
						$aResumes[$iKey]['time_view'] = $aViewItem['time_stamp'];
						$aResumes[$iKey]['sent_messages'] = $aViewItem['sent_messages'];
					}
				}
			}

			if(!empty($aCatList))
			{
				foreach($aCatList as $aCat)
				{
					if($aResume['resume_id'] == $aCat['resume_id'])
					{
						$sCatLink = $this->url()->permalink('resume.category',
							$aCat['category_id'], $aCat['name_url']);
						$sCatItem = "<a href ='{$sCatLink}'>".Phpfox::getLib('locale')->convert(_p($aCat['name']))."</a>";
						
						if($aResumes[$iKey]['categories'] == "")
						{
							$aResumes[$iKey]['categories'] .= $sCatItem;
						}
						else
						{
							$aResumes[$iKey]['categories'] .= " | ".$sCatItem;
						}
					}
				}
			}
		}

		// Assign variables and set header
		$this -> template()
                ->setMeta('keywords', Phpfox::getParam('resume.resume_meta_keywords'))
                ->setMeta('description', Phpfox::getParam('resume.resume_meta_description'))
			  -> assign(array(
				  		'current_page'=>$this->search()->getPage(),
						'sCorePath'  	  		=> phpfox::getParam('core.path'),
						'aResumes'   	  		=> $aResumes,
						'bCanEdit'	 	  		=> $bCanEdit,
						'bCanDelete'	  		=> $bCanDelete,
						'sView'			  		=> $sView,
						'bIsProfile'	  		=> $bIsProfile,
						'bIsSelfView'	  		=> $bIsSelfView,
						'bIsAdvSearch'    		=> $bIsAdvSearch,
						'bIsApproving'			=> $bIsApproving,
						'bViewResumeRegistry'	=> Phpfox::getService('resume.account')->checkViewResumeRegistration(Phpfox::getUserId()),
                        'iPage'                 => $this->search()->getPage() ? $this->search()->getPage() : 0,
                        'bCanViewInProfile'     => Phpfox::getService('resume.setting')->getPermissionByName('display_resume_in_profile_info')
				 ))
			  -> setHeader(array(
			  			'resume.js'  => 'module_resume',
						'jquery.atooltip.min.js' => 'module_resume',
						'country.js' => 'module_core'	
			  	 ))
			  -> setPhrase(array(
			  			'resume.publish_resume'
			  	 ));

	}

	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('resume.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}

?>