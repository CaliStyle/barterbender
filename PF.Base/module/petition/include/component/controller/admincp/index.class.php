<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Controller_Admincp_Index extends Phpfox_Component
{
	/**
	 * Class process method which is used to execute this component.
	 */
	public function process()
	{						
		if (($iId = $this->request()->getInt('approve')))
		{			
			if (Phpfox::getService('petition.process')->approve($iId))
			{
				$this->url()->send('admincp.petition', null, Phpfox::getPhrase('petition.petition_successfully_approved'));
			}
		}		
						
		if (($iId = $this->request()->getInt('delete')))
		{
			if (Phpfox::getService('petition.process')->delete($iId))
			{
				$this->url()->send('admincp.petition', null, Phpfox::getPhrase('petition.petition_successfully_deleted'));
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
			'end_time' => Phpfox::getPhrase('petition.end_date'),
			'total_view' => Phpfox::getPhrase('petition.most_viewed'),
			'total_like' => Phpfox::getPhrase('petition.most_liked'),
			'total_sign' => Phpfox::getPhrase('petition.most_signed')
		);
		
		$aStatus = array('%' => Phpfox::getPhrase('petition.any'),
				 '1' => Phpfox::getPhrase('petition.closed'),
				 '2' => Phpfox::getPhrase('petition.on_going'),
				 '3' => Phpfox::getPhrase('petition.victory')
				);
		$aFeatured = array('%'=>Phpfox::getPhrase('petition.any'),				   
				   '%1%'=>Phpfox::getPhrase('core.yes'),
				   '%0%'=>Phpfox::getPhrase('core.no')
				  );
		$aFilters = array(
			'search' => array(
				'type' => 'input:text',
				'search' => "AND p.title LIKE '%[VALUE]%'"
			),	
			'user' => array(
				'type' => 'input:text',
				'search' => "AND u.full_name LIKE '%[VALUE]%'"
			),
			'status' => array(
				'type' => 'select',
				'options' => $aStatus,				
				'search' => "AND p.petition_status LIKE '[VALUE]'"
			),
			'featured' => array(
				'type' => 'select',
				'options' => $aFeatured,
				'search' => "AND p.is_featured LIKE '[VALUE]'",
			),
			'approved' => array(
				'type' => 'select',
				'options' => $aFeatured,
				'search' => "AND p.is_approved LIKE '[VALUE]'",
			),
			'pages' => array(
				'type' => 'select',
				'options' => array('%'=>Phpfox::getPhrase('petition.any'),				   
								'pages'=>Phpfox::getPhrase('core.yes'),
								'petition'=>Phpfox::getPhrase('core.no')
							    ),
				'search' => "AND p.module_id LIKE '[VALUE]'",
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
				'default' => 'end_time',
				'alias' => 'p'
			),
			'sort_by' => array(
				'type' => 'select',
				'options' => array(
					'DESC' => Phpfox::getPhrase('core.descending'),
					'ASC' => Phpfox::getPhrase('core.ascending')
				),
				'default' => 'DESC'
			),
		);
		
		//die();
		$oSearch = Phpfox::getLib('search')->set(array(
				'type' => 'petition',
				'filters' => $aFilters,
				'search' => 'search'
			)
		);
		
		$iLimit = $oSearch->getDisplay();
		/*
		$bGetSession  = false;		 
		if ($this->search()->getPage() >= 1)
		{
			if (isset($_SESSION["ynpetition_aFilters_condition"]))
				$bGetSession  = true;
		}			
		if ($bGetSession)
		{
			//$aFilters = $_SESSION["ynpetition_aFilters"];
			$conditions = $_SESSION["ynpetition_aFilters_condition"];
			$sort = $_SESSION["ynpetition_aFilters_sort"];
			$page =$_SESSION["ynpetition_aFilters_page"];
			$iLimit = $_SESSION["ynpetition_aFilters_iLimit"];
		}else
		{
			$conditions = $oSearch->getConditions();
			$sort = $oSearch->getSort();
			$page = $oSearch->getPage();
			
			$_SESSION["ynpetition_aFilters_condition"] = $conditions ;
			$_SESSION["ynpetition_aFilters_sort"] = $sort ;
			$_SESSION["ynpetition_aFilters_page"] = $page ;
			$_SESSION["ynpetition_aFilters_iLimit"] = $iLimit ;
		}		
		*/
		$conditions = $oSearch->getConditions();
			$sort = $oSearch->getSort();
			$page = $oSearch->getPage();
		
		
		list($iCnt, $aPetitions) = Phpfox::getService('petition')
		->searchPetition($conditions,$sort , $page, $iLimit);
				
		Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $oSearch->getSearchTotal($iCnt)));
		
		$this->template()->setTitle(Phpfox::getPhrase('petition.petition'))
			->setBreadcrumb(Phpfox::getPhrase('petition.manage_petitions'), $this->url()->makeUrl('admincp.petition'))
			->assign(array(
					'aPetitions' 	=> $aPetitions,
					'aStatus'	=> $aStatus
				)
			)
			->setHeader('cache', array(
				'quick_edit.js' => 'static_script',
                        'admin.js'      => 'module_petition'
			)			
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}

?>
