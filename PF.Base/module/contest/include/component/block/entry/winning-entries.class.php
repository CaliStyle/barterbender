<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Entry_Winning_Entries extends Phpfox_Component {

    public function process() {
        
        $aContest = $this->getParam('aContest');
		
		$sUrl = Phpfox::getLib('url')->permaLink('contest', $aContest['contest_id'],$aContest['contest_name']);
        
        $iPage = $this->request()->get('page', 0);

        $iPage = ($iPage == 0 || $iPage == 1)?1:($iPage);
        
        $iSize = 10;

        list($iCnt,$aEntries) = Phpfox::getService('contest.entry')->get($aContest['contest_id'],$iPage,$iSize);
    	
        foreach($aEntries as $key=>$aEntry){
			$aEntry['delete'] = 1;
            $aEntry = Phpfox::getService('contest.entry')->retrieveEntryPermission($aEntry);
    		$aEntries[$key] = $aEntry;
    	}
		

        list($iPagePrev,$iPageNext,$bDisablePrev,$bDisableNext,$bHideAll) = Phpfox::getService('contest.helper')->buildPaging($iPage,$iSize,$iCnt);

        /*set up paging*/
        $sCurrentUrl = Phpfox::getLib('url')->makeUrl('current');
        $sCurrentUrl = preg_replace("/&page=[0-9]+/", "", $sCurrentUrl);
        $sCurrentUrl = preg_replace("/\?page=[0-9]+/", "", $sCurrentUrl);
        
        $sCurrentUrlPagePrev = $sCurrentUrl.'&page='.$iPagePrev;
        $sCurrentUrlPageNext = $sCurrentUrl.'&page='.$iPageNext;
    

         $this->template()->assign(array(
            'iPage' => $iPage,
            'sCurrentUrl' => $sCurrentUrl,
            'sCurrentUrlPagePrev' => $sCurrentUrlPagePrev,
            'sCurrentUrlPageNext' => $sCurrentUrlPageNext,
            'bDisablePrev' => $bDisablePrev,
            'bDisableNext' => $bDisableNext,
            'bHideAll' => $bHideAll,
             'sUrlNoImagePhoto'	=> Phpfox::getParam('core.path_file').'module/contest/static/image/no_photo_small.png'
        ));

		
        $this->template()->assign(array(
                'aEntries' => $aEntries,
                'sUrl' => $sUrl,
                'is_hidden_action' => Phpfox::getService('contest.permission')->canHideAction($aContest),
            )
        );
		
		
    }

}