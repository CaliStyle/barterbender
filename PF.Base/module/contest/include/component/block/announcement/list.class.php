<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_announcement_List extends Phpfox_Component
{
    public function process()
    {
        $aContest = $this->getParam('aContest');

        $iLimit = 50;
        $iPage = 0;
        list($iCnt, $aAnnouncement) = Phpfox::getService('contest.announcement')->get($aContest['contest_id'], $iPage, $iLimit);

        $this->template()->assign(array(
            'aAnnouncement' => $aAnnouncement
        ));
    }

}