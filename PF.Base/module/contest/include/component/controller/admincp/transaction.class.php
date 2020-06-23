<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Controller_Admincp_Transaction extends Phpfox_Component
{
	/**
	 * Class process method which is used to execute this component.
	 */
	public function process()
	{	
		
		$iPage = $this->request()->getInt('page');
		
		$aPages = array(5, 10, 15, 20);
		$aDisplays = array();
		foreach ($aPages as $iPageCnt)
		{
			$aDisplays[$iPageCnt] = _p('core.per_page', array('total' => $iPageCnt));
		}		
		
		$aStatus = array('%' => _p('contest.any'),);

		$aAllTransactionStatuses = Phpfox::getService('contest.constant')->getAllTransactionStatuses();

		foreach ($aAllTransactionStatuses as $aTransactionStatus) {
			$aStatus[$aTransactionStatus['id']] = _p('contest.' . $aTransactionStatus['name']);
		}

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
				'search' => "AND transaction.status LIKE '[VALUE]'"
			),
			'display' => array(
				'type' => 'select',
				'options' => $aDisplays,
				'alias' => 'p',
				'default' => '10'
			)
		);		
		
		$oSearch = Phpfox::getLib('search')->set(array(
				'type' => 'contest',
				'filters' => $aFilters,
				'search' => 'search'
			)
		);
		
		$iLimit = $oSearch->getDisplay();
		
		list($iCnt, $aTransactions) = Phpfox::getService('contest.transaction')->searchTransactions($oSearch->getConditions(), 'transaction.time_stamp DESC ' , $oSearch->getPage(), $iLimit);

		Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $oSearch->getSearchTotal($iCnt)));
		$this->template()->setTitle(_p('contest.contest'))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_contest'), $this->url()->makeUrl('admincp.app').'?id=__module_contest')
			->setBreadcrumb(_p('contest.manage_transactions'), $this->url()->makeUrl('admincp.contest'))
			->assign(array(
					'aTransactions' 	=> $aTransactions,
					'aStatus'	=> $aStatus,
					'iTotalResults' => $iCnt,
					'aContestStatus' => Phpfox::getService('contest.constant')->getAllContestStatus()
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
		(($sPlugin = Phpfox_Plugin::get('transaction.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}

?>
