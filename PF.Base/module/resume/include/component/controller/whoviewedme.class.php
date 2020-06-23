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
class Resume_Component_Controller_whoviewedme extends Phpfox_Component
{
	public function process()
	{
		Phpfox::isUser(TRUE);
		
		$iViewerId = Phpfox::getUserId();
		
		// Setup breadcrumb
		$this->template()
			->setBreadcrumb(_p('resume.resume'),$this->url()->makeUrl('resume'));
		
		// Build filter section menu on left side
        Phpfox::getService('resume')->buildSectionMenu();
		
		// Set action url for searching
		$sActionUrl = $this->url()->makeUrl('resume.whoviewedme');
		
		// Check "Who Viewed Me" Service Registration
		$bWhoViewRegistration = Phpfox::getService('resume.account')->checkWhoViewRegistration($iViewerId);

		$iCnt = 0;
		if($bWhoViewRegistration)
		{
			// Set up variables and search fields
			$sSearchNumber = Phpfox::getParam('resume.total_resume_display');

			if($sSearchNumber)
			{
				$aSearchNumber = explode(',',str_replace(" ", "", Phpfox::getParam('resume.total_resume_display')));
			}
			else 
			{
				$aSearchNumber = array(5,10,15,20,25);
			}
			// Setup search conditions

			$aCurrentPublishedResume = Phpfox::getService('resume')->getPublishedResumeByUserId($iViewerId);

			$this->search()->setCondition("and rv.owner_id = {$iViewerId}");

			if($aCurrentPublishedResume)
			{
                $aCondx = "";
                foreach($aCurrentPublishedResume as $key => $aCurrent){
                    if($key == 0)
                    {
                        $aCondx .= $aCurrent['resume_id'];
                    }
                    else
                    {
                        $aCondx .= ','.$aCurrent['resume_id'];
                    }
                }
				$this->search()->setCondition("and rv.resume_id IN (".$aCondx.")");
			}
			else
			{
				$this->search()->setCondition("and rv.resume_id = 0");
			}



			$aBrowseParams = array(
				'module_id' => 'resume',
				'alias' => 'rv',
				'field' => 'resume_id',
				'table' => Phpfox::getT('resume_viewme'),
				'hide_view' => array('my')
			);
			$this->search()->set(
				array(
					'type' => 'resume',
					'field'=> 'rb.resume_id',
					'search' =>	'search',
					'search_tool' => array(
						'table_alias'  => 'rv',
						'search'=> array(
							'action' 	   => $sActionUrl,
							'default_value'=> _p('resume.search_members'),
							'name'		   => 'search',
							'field'		   => 'u1.full_name'
						),
						'sort'	=> array(

							'latest' 		 => array('rv.time_stamp',
								_p('resume.latest')),

							'most-viewed' 	 => array('rv.total_view',
								_p('resume.most_viewed')),
						),
						'show' => $aSearchNumber
					)
				)
			);

			$this->search()->browse()->params($aBrowseParams)
                ->setPagingMode(Phpfox::getParam('resume.resume_paging_mode', 'loadmore'))
                ->execute();
			$aResumes = $this->search()->browse()->getRows();
			$aListId = array();
			foreach ($aResumes as $key => $aResume) {
				if(!in_array($aResume['user_id'], $aListId))
					$aListId[] = $aResume['user_id'];
				else
					unset($aResumes[$key]);
			}
			// Setup pager
			Phpfox::getLib('pager')->set(
				array(
					'page'  => $this->search()->getPage(), 
					'size'  => $this->search()->getDisplay(), 
					'count' => Phpfox::getService('resume.viewme')->TotalResumesViewed(),
                    'paging_mode' => $this->search()->browse()->getPagingMode()
				)
			);

		}
		else
		{
			$iLimit = Phpfox::getUserParam('resume.resume_viewer_numbers');
			list($iCnt,$aResumes) = Phpfox::getService('resume.viewme')->getWhoViewed($iViewerId, $iLimit);
		}

		// Assign variables and set header
		$iPage = $this->search()->getPage();        
		$this -> template()
			->setTitle(_p('who_viewed_me'))
			  -> assign(array(
						'sCorePath'  	  		=> phpfox::getParam('core.path'),
						'aResumes'   	  		=> $aResumes,
						'iCnt'					=> $bWhoViewRegistration?Phpfox::getService('resume.viewme')->TotalResumesViewed():$iCnt,
						'bWhoViewRegistration'	=> $bWhoViewRegistration,
						'iPage'                 => $iPage
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
		(($sPlugin = Phpfox_Plugin::get('resume.component_controller_whoviewedme_clean')) ? eval($sPlugin) : false);
	}
}