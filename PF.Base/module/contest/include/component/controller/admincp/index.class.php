<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Controller_Admincp_Index extends Phpfox_Component
{
	/**
	 * Class process method which is used to execute this component.
	 */
	public function process()
	{	
		if (($iId = $this->request()->getInt('approve')))
		{			
			if (Phpfox::getService('contest.contest.process')->approve($iId))
			{
				$this->url()->send('admincp.contest', null, _p('contest.contest_successfully_approved'));
			}
		}		
						
		if (($iId = $this->request()->getInt('delete')))
		{
			if (Phpfox::getService('contest.contest.process')->delete($iId))
			{
				$this->url()->send('admincp.contest', null, _p('contest.contest_successfully_deleted'));
			}
		}
		
		$iPage = $this->request()->getInt('page');
		
		$aPages = array(5, 10, 15, 20);
		$aDisplays = array();
		foreach ($aPages as $iPageCnt)
		{
			$aDisplays[$iPageCnt] = _p('core.per_page', array('total' => $iPageCnt));
		}		
		
		$aSorts = array(
			'end_time' => _p('contest.end_date'),
			'total_like' => _p('contest.most_liked'),
			'total_vote' => _p('contest.most_voted'),
		);
		$aAllContestStatus = Phpfox::getService('contest.constant')->getAllContestStatus();

		$aStatus = array('%' => _p('contest.any'),
			
		);

		foreach ($aAllContestStatus as $aContestStatus) {
			$aStatus[$aContestStatus['id']] = _p('contest.' . $aContestStatus['name']);
		}
		$aFeatured = array('%'=>_p('contest.any'),
				   '%1%'=>_p('core.yes'),
				   '%0%'=>_p('core.no')
				  );

		$aFilters = array(
			'search' => array(
				'type' => 'input:text',
				'search' => "AND contest.contest_name LIKE '%[VALUE]%'"
			),	
			'user' => array(
				'type' => 'input:text',
				'search' => "AND u.full_name LIKE '%[VALUE]%'"
			),
			'status' => array(
				'type' => 'select',
				'options' => $aStatus,				
				'search' => "AND contest.contest_status LIKE '%[VALUE]%'"
			),
			'featured' => array(
				'type' => 'select',
				'options' => $aFeatured,
				'search' => "AND contest.is_feature LIKE '[VALUE]'",
			),
			'premium' => array(
				'type' => 'select',
				'options' => $aFeatured,
				'search' => "AND contest.is_premium LIKE '[VALUE]'",
			),
			'ending_soon' => array(
				'type' => 'select',
				'options' => $aFeatured,
				'search' => "AND contest.is_ending_soon LIKE '[VALUE]'",
			),
                        'active' => array(
                            'type' => 'select',
                            'options' => $aFeatured,
                            'search' => "AND contest.is_active LIKE '[VALUE]'",
                        ),
			'approved' => array(
				'type' => 'select',
				'options' => $aFeatured,
				'search' => "AND contest.is_approved LIKE '[VALUE]'",
			),
			'display' => array(
				'type' => 'select',
				'options' => $aDisplays,
				'alias' => 'p',
				'default' => '10'
			),
			'sort' => array(
				'type' => 'select',
				'options' => $aSorts,
				'default' => 'start_time',
				'alias' => 'contest'
			),
			'sort_by' => array(
				'type' => 'select',
				'options' => array(
					'DESC' => _p('core.descending'),
					'ASC' => _p('core.ascending')
				),
				'default' => 'DESC'
			),
		);		
		
		$oSearch = Phpfox::getLib('search')->set(array(
				'type' => 'contest',
				'filters' => $aFilters,
				'search' => 'search'
			)
		);
		
		$iLimit = $oSearch->getDisplay();
		
		list($iCnt, $aContests) = Phpfox::getService('contest.contest')->searchContests($oSearch->getConditions(), $oSearch->getSort(), $oSearch->getPage(), $iLimit);
				
		Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $oSearch->getSearchTotal($iCnt)));

		foreach ($aContests as &$aContest) {
             $aContest = Phpfox::getService('contest.contest')->retrieveContestPermissions($aContest);
        }
		
		$this->template()->setTitle(_p('contest.contest'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_contest'), $this->url()->makeUrl('admincp.app').'?id=__module_contest')
			->setBreadcrumb(_p('contest.manage_contests'), $this->url()->makeUrl('admincp.contest'))
			->assign(array(
					'aContests' 	=> $aContests,
					'aStatus'	=> $aStatus,
					'iTotalResults' => $iCnt,
					'aContestStatus' => $aAllContestStatus
				)
			)
			->setHeader('cache', array(
				'quick_edit.js' => 'static_script',
                'admin.js'      => 'module_contest'
			)			
		);

	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('contest.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}

?>
