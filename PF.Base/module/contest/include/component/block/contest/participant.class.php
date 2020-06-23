<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Contest_Participant extends Phpfox_Component
{
    public function process()
    {
        if (!$aContests = $this->getParam('aContest'))
        {
            return false;
        }
        
        list($iCnt, $aParticipants) = Phpfox::getService('contest.participant')->get($aContests['contest_id'], 0, 16);
        if ($iCnt <= 0)
        {
            return false;
        }

        $sViewMoreUrl = $this->url()->permalink('contest', $aContests['contest_id'], $aContests['contest_name']).'view_participants/';

        $this->template()->assign(array(
            'aParticipants' => $aParticipants,
            'corepath' => phpfox::getParam('core.path'),
            'aContests' => $aContests,
            'sHeader' => _p('contest.participants'),
            'sViewMoreUrl' => $sViewMoreUrl
        ));

        return 'block';
    }
}
