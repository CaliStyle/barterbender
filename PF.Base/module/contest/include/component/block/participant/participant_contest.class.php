<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Participant_Participant_Contest extends Phpfox_Component {

    public function process() {

        $iPage = $this->request()->get('page', 0);

        $iPage = ($iPage == 0 || $iPage == 1)?1:($iPage);
        
        $iSize = 10;

        $aContest = $this->getParam('aContest');
        list($iCnt,$aParticipant) = Phpfox::getService('contest.participant')->get($aContest['contest_id'],$iPage,$iSize);


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
        ));
        /*set up paging*/

        $this->template()->assign(array(
                'aParticipant' => $aParticipant,
            )
        );
    }

}