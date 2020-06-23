<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Contest_Winning_Entry extends Phpfox_Component
{
    public function process()
    {
        if (!$aContest = $this->getParam('aContest'))
        {
            return false;
        }
        
        list($iCnt, $aWinningEntries) = Phpfox::getService('contest.entry')->get($aContest['contest_id'], 0, 3);
        if ($iCnt <= 0)
        {
            return false;
        }
        
        $sContestUrl = $this->url()->permalink('contest', $aContest['contest_id'], $aContest['contest_name']);
        $sViewMoreUrl = $sContestUrl.'view_winning/';
        
        $this->template()->assign(array(
            'sHeader' => _p('contest.wining_entries'),
            'aWinningEntries' => $aWinningEntries,
            'sContestUrl' => $sContestUrl,
            'sViewMoreUrl' => $sViewMoreUrl
        ));

        return 'block';
    }
}
