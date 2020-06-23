<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Entry_Add extends Phpfox_Component{

	private function _checkIsSearchingAndForward()
	{	
		$oUrl = Phpfox::getLib('url');

		if(isset($_POST['search']) && isset($_POST['search']['search']))
		{
			$iId = Phpfox::getService('contest.helper')->setSearchKeyword($_POST['search']['search']);
			$oUrl->setParam('search-id', $iId);
			$oUrl->forward($oUrl->getFullUrl());
		}
	}

	public function process ()
	{

		$this->_checkIsSearchingAndForward();

		$iContestId  = $this->request()->getInt('req2');
        $aContest = Phpfox::getService('contest.contest')->getContestById($iContestId);
		$iSourceSelected = $this->request()->getInt('source');
		if(($iSourceSelected) == 0 && ($aContest['type'] == 3)){
			if(Phpfox::isModule('ultimatevideo')){
				$iSourceSelected = 1;
			}
			elseif(Phpfox::isModule('videochannel')){
				$iSourceSelected = 2;
			}
		}
		if(!Phpfox::getService('contest.permission')->canSubmitEntry($iContestId, Phpfox::getUserId()))
		{
			return false;
		}

		$iItemId = $this->request()->get('itemid');
		$sChosenItemTitle = '';
		if ($iItemId) {
			//remove session stored
			Phpfox::getService('contest.helper')->removeSessionAddNewItemOfUser();
			$aChosenItem = Phpfox::getService('contest.entry')->getItemDataFromFox($aContest['type'], $iItemId);
			$sChosenItemTitle = $aChosenItem['title'];
		}


		$aAddEntryTemplateData = Phpfox::getService('contest.entry')->getDataOfAddEntryTemplate($iContestId,$iSourceSelected);

		$aAddEntryTemplateData['iChosenItemId'] = $iItemId ? $iItemId : 0;

		$aAddEntryTemplateData['sChosenItemTitle'] = $sChosenItemTitle;

		$sActionUrl = Phpfox::getLib('url')->permaLink('contest', $iContestId, $sTitle = '', $bRedirect = false, $sMessage = null, $aExtra = array('action' => 'add'));

		if(isset($_POST['search']))
		{

		}
		$aYnContestItemSearchTool = array(
			'search' => array(
				'action' => $sActionUrl,
				'default_value' => _p('Search Items'),
				'name' => 'search'
				)
			);

		if($iSearchId = Phpfox::getLib('request')->get('search-id') )
		{
			$sKeyword = Phpfox::getService('contest.helper')->getSearchKeyword($iSearchId);
			$aYnContestItemSearchTool['search']['actual_value'] = $sKeyword;
		}
                
		$this->template()->assign(array(
			'aAddEntryTemplateData' => $aAddEntryTemplateData,
			'aYnContestItemSearchTool' => $aYnContestItemSearchTool,
			'sUrlNoImagePhoto'	=> Phpfox::getParam('core.path_file').'module/contest/static/image/no_photo_small.png'
			)
		);			

		Phpfox::getLib('pager')->set(array('page' => $aAddEntryTemplateData['iPage'], 'size' => $aAddEntryTemplateData['iPageSize'], 'count' => $aAddEntryTemplateData['iTotalItems']));
		
		list($iPagePrev,$iPageNext,$bDisablePrev,$bDisableNext,$bHideAll) = Phpfox::getService('contest.helper')->buildPaging($aAddEntryTemplateData['iPage'],$aAddEntryTemplateData['iPageSize'],$aAddEntryTemplateData['iTotalItems']);

		/*set up paging*/
        $sCurrentUrl = Phpfox::getLib('url')->makeUrl('current');
        $sCurrentUrl = preg_replace("/&page=[0-9]+/", "", $sCurrentUrl);
        $sCurrentUrl = preg_replace("/\?page=[0-9]+/", "", $sCurrentUrl);

        $sCurrentUrlPagePrev = $sCurrentUrl.'&page='.$iPagePrev;
        $sCurrentUrlPageNext = $sCurrentUrl.'&page='.$iPageNext;
         $this->template()->assign(array(
            'iPage' => $aAddEntryTemplateData['iPage'],
            'sCurrentUrl' => $sCurrentUrl,
            'sCurrentUrlPagePrev' => $sCurrentUrlPagePrev,
            'sCurrentUrlPageNext' => $sCurrentUrlPageNext,
            'bDisablePrev' => $bDisablePrev,
            'bDisableNext' => $bDisableNext,
            'bHideAll' => $bHideAll,
            'iSourceSelected' => $iSourceSelected,
        ));

	}
}