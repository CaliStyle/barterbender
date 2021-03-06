<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Report_Component_Controller_Admincp_Index
 */
class Report_Component_Controller_Admincp_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
        if ($iId = $this->request()->getInt('view')) {
            if ($sRedirect = Phpfox::getService('report')->getRedirect($iId)) {
                $this->url()->forward($sRedirect);
            }
        }

        if ($aIds = $this->request()->getArray('id')) {
            if ($this->request()->get('ignore')) {
                foreach ($aIds as $iId) {
                    if (!is_numeric($iId)) {
                        continue;
                    }

                    Phpfox::getService('report.data.process')->ignore($iId);
                }

                $this->url()->send('admincp.report', null, _p('report_s_successfully_ignored'));
            } elseif ($this->request()->get('process')) {
                foreach ($aIds as $iId) {
                    if (!is_numeric($iId)) {
                        continue;
                    }

                    Phpfox::getService('report.data.process')->process($iId);
                }

                $this->url()->send('admincp.report', null, _p('report_s_successfully_processed'));
            }
        }
		
		$iPage = $this->request()->getInt('page');
		
		$aPages = array(5, 10, 15, 20);
		$aDisplays = array();
		foreach ($aPages as $iPageCnt)
		{
			$aDisplays[$iPageCnt] = _p('per_page', array('total' => $iPageCnt));
		}		
		
		$aSorts = array(
			'added' => _p('time')
		);
		
		$aFilters = array(
			'display' => array(
				'type' => 'select',
				'options' => $aDisplays,
				'default' => '10'
			),
			'sort' => array(
				'type' => 'select',
				'options' => $aSorts,
				'default' => 'added',
				'alias' => 'rd'
			),
			'sort_by' => array(
				'type' => 'select',
				'options' => array(
					'DESC' => _p('descending'),
					'ASC' => _p('ascending')
				),
				'default' => 'DESC'
			)
		);		
		
		$oSearch = Phpfox_Search::instance()->set(array(
				'type' => 'reports',
				'filters' => $aFilters,
				'search' => 'search'
			)
		);
		
		$iLimit = $oSearch->getDisplay();
		
		list($iCnt, $aReports) = Phpfox::getService('report')->get($oSearch->getConditions(), $oSearch->getSort(), $oSearch->getPage(), $iLimit);
		
		Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $oSearch->getSearchTotal($iCnt)));
		
		$this->template()->setTitle(_p('reports'))
            ->setActiveMenu('admincp.maintain.report')
			->setBreadCrumb(_p('reports'), $this->url()->makeUrl('admincp.report'))
			->assign(array(
					'aReports' => $aReports
				)
			);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('report.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}