<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Controller_Admincp_Index extends Phpfox_Component
{
	/**
	 * Class process method which is used to execute this component.
	 */
	public function process()
	{	
		if (($iId = $this->request()->getInt('approve')))
		{			
			if (Phpfox::getService('fundraising.campaign.process')->approve($iId))
			{
				$this->url()->send('admincp.fundraising', null, _p('fundraising_successfully_approved'));
			}
		}		
						
		if (($iId = $this->request()->getInt('delete')))
		{
			if (Phpfox::getService('fundraising.campaign.process')->delete($iId))
			{
				$this->url()->send('admincp.fundraising', null, _p('fundraising_successfully_deleted'));
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
			'time_stamp' => _p('latest'),
			'end_time' => _p('expired_date'),
			'total_view' => _p('most_viewed'),
			'total_like' => _p('most_liked'),
			'total_donor' => _p('most_donated')
		);
		
		$aStatus = array('%' => _p('any'),
			Phpfox::getService('fundraising.campaign')->getStatusCode('closed') => _p('closed'),
			Phpfox::getService('fundraising.campaign')->getStatusCode('ongoing') => _p('on_going'),
			Phpfox::getService('fundraising.campaign')->getStatusCode('expired') => _p('expired'),
			Phpfox::getService('fundraising.campaign')->getStatusCode('reached') => _p('reached'),
			Phpfox::getService('fundraising.campaign')->getStatusCode('draft') => _p('draft'),
			Phpfox::getService('fundraising.campaign')->getStatusCode('pending') => _p('pending'),
		);
		$aFeatured = array('%'=>_p('any'),
				   '%1%'=>_p('core.yes'),
				   '%0%'=>_p('core.no')
				  );

		$aFilters = array(
			'search' => array(
				'type' => 'input:text',
				'search' => "AND campaign.title LIKE '%[VALUE]%'"
			),	
			'user' => array(
				'type' => 'input:text',
				'search' => "AND u.full_name LIKE '%[VALUE]%'"
			),
			'status' => array(
				'type' => 'select',
				'options' => $aStatus,				
				'search' => "AND campaign.status LIKE '[VALUE]'"
			),
			'featured' => array(
				'type' => 'select',
				'options' => $aFeatured,
				'search' => "AND campaign.is_featured LIKE '[VALUE]'",
			),
			'approved' => array(
				'type' => 'select',
				'options' => $aFeatured,
				'search' => "AND campaign.is_approved LIKE '[VALUE]'",
			),
			'active' => array(
				'type' => 'select',
				'options' => $aFeatured,
				'search' => "AND campaign.is_active LIKE '[VALUE]'",
			),
			'pages' => array(
				'type' => 'select',
				'options' => array('%'=>_p('any'),
								'pages'=>_p('core.yes'),
								'fundraising'=>_p('core.no')
							    ),
				'search' => "AND campaign.module_id LIKE '[VALUE]'",
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
				'alias' => 'campaign'
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
				'type' => 'fundraising',
				'filters' => $aFilters,
				'search' => 'search'
			)
		);
		
		$iLimit = $oSearch->getDisplay();
		
		list($iCnt, $aCampaigns) = Phpfox::getService('fundraising.campaign')->searchCampaigns($oSearch->getConditions(), $oSearch->getSort(), $oSearch->getPage(), $iLimit);
				
		Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $oSearch->getSearchTotal($iCnt)));

		foreach($aCampaigns as &$aCampaign)
		{
			$aCampaign = Phpfox::getService('fundraising.campaign')->retrieveMoreInfoFromCampaign($aCampaign, true);
		}
		
		$this->template()->setTitle(_p('fundraising'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_fundraising'), $this->url()->makeUrl('admincp.app').'?id=__module_fundraising')
			->setBreadcrumb(_p('manage_campaigns'), $this->url()->makeUrl('admincp.fundraising'))
			->assign(array(
					'aCampaigns' 	=> $aCampaigns,
					'aStatus'	=> $aStatus,
					'iTotalResults' => $iCnt,
					'aCampaignStatus' => Phpfox::getService('fundraising.campaign')->getAllStatus()
				)
			)
			->setHeader('cache', array(
				'quick_edit.js' => 'static_script',
                        'admin.js'      => 'module_fundraising'
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
