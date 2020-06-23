<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Controller_Admincp_Category_Index extends Phpfox_Component
{
	/**
	 * Class process method which is used to execute this component.
	 */
	public function process()
	{		
		if ($aDeleteIds = $this->request()->getArray('id'))
		{
			if (Phpfox::getService('petition.category.process')->deleteMultiple($aDeleteIds))
			{
				$this->url()->send('admincp.petition.category', null, Phpfox::getPhrase('petition.categories_successfully_deleted'));
			}
		}
		
		$iPage = $this->request()->getInt('page');
		
		$aPages = array(5, 10, 15, 20);
		$aDisplays = array();
		foreach ($aPages as $iPageCnt)
		{
			$aDisplays[$iPageCnt] = Phpfox::getPhrase('core.per_page', array('total' => $iPageCnt));
		}		
		
		$aSorts = array(
			'added' => Phpfox::getPhrase('core.time'),
			'used' => Phpfox::getPhrase('petition.most_used')
		);
		
		$aFilters = array(
			'search' => array(
				'type' => 'input:text',
				'search' => "AND c.name LIKE '%[VALUE]%'"
			),							
			'display' => array(
				'type' => 'select',
				'options' => $aDisplays,
				'default' => '10'
			),
			'sort' => array(
				'type' => 'select',
				'options' => $aSorts,
				'default' => 'added',
				'alias' => 'c'
			),
			'sort_by' => array(
				'type' => 'select',
				'options' => array(
					'DESC' => Phpfox::getPhrase('core.descending'),
					'ASC' => Phpfox::getPhrase('core.ascending')
				),
				'default' => 'DESC'
			)
		);		
		
		$oSearch = Phpfox::getLib('search')->set(array(
				'type' => 'categories',
				'filters' => $aFilters,
				'search' => 'search'
			)
		);
		
		$iLimit = $oSearch->getDisplay();
		$conditions = $oSearch->getConditions();
		$sort = $oSearch->getSort();
		$page = $oSearch->getPage();		
		
		list($iCnt, $aCategories) = Phpfox::getService('petition.category') ->get($conditions, $sort, $page, $iLimit);
		
		Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $oSearch->getSearchTotal($iCnt)));
		
		$this->template()->setTitle(Phpfox::getPhrase('petition.petition'))
			->setBreadcrumb(Phpfox::getPhrase('petition.manage_categories'), $this->url()->makeUrl('admincp.petition.category'))
			->assign(array(
					'aCategories' => $aCategories
				)
			);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_controller_admincp_category_index_clean')) ? eval($sPlugin) : false);
	}
}

?>
