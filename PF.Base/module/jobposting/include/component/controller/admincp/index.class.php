<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class JobPosting_Component_Controller_Admincp_Index extends Phpfox_Component
{
	public function process()
	{					
		

		$iPage = $this->request()->getInt('page');
		
		$aPages = array(5, 10, 15, 20);
		$aDisplays = array();
		foreach ($aPages as $iPageCnt)
		{
			$aDisplays[$iPageCnt] = _p('core.per_page', array('total' => $iPageCnt));
		}		
	
		
		$aStatus = array('%' => 'Any',
			'%1%' => _p('sponsor'),
			'%0%' => _p('not_sponsor'),
		);

		$aFilters = array(
			'search' => array(
				'type' => 'input:text',
				'search' => "AND ca.name LIKE '%[VALUE]%'"
			),	
			'user' => array(
				'type' => 'input:text',
				'search' => "AND u.full_name LIKE '%[VALUE]%'"
			),
			'status' => array(
				'type' => 'select',
				'options' => $aStatus,				
				'search' => "AND is_sponsor LIKE '[VALUE]'"
			),
		);		
		
		$oSearch = Phpfox::getLib('search')->set(array(
				'type' => 'jobposting',
				'filters' => $aFilters,
				'search' => 'search'
			)
		);
		
		$iLimit = $oSearch->getDisplay();
		
		$aConds = $oSearch->getConditions();

		list($iCnt, $aCompanies) = Phpfox::getService('jobposting.company')->searchCompanies($aConds, 'ca.time_stamp desc' , $oSearch->getPage(), $iLimit);
		
		Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $oSearch->getSearchTotal($iCnt)));
		
		$this->template()->setTitle("Job Posting")
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_jobposting'), $this->url()->makeUrl('admincp.app').'?id=__module_jobposting')
			->setBreadcrumb("Manage Companies", $this->url()->makeUrl('admincp.jobposting'))
			->assign(array(
					'aCompanies' 	=> $aCompanies,
					'aStatus'	=> $aStatus,
					'iTotalResults' => $iCnt,
				)
			)
			->setHeader('cache', array(
				'quick_edit.js' => 'static_script',
			)			
		);

	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}

?>
