<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Entry_Most_Viewed extends Phpfox_Component
{
    public function process()
    {
        if (!$aContest = $this->getParam('aContest'))
        {
            return false;
        }
        
        $iLimit = Phpfox::getParam('contest.number_of_entries_block_most_voted_most_viewed');
        $aEntries = Phpfox::getService('contest.entry')->getTopByContestId($aContest['contest_id'], 'view', $iLimit);
        if (empty($aEntries))
        {
            return false;
        }
        
        $sContestUrl = $this->url()->permalink('contest', $aContest['contest_id'], $aContest['contest_name']);
        $sViewMoreUrl = $sContestUrl.'sort_most-viewed/';

        $this->template()->assign(array(
            'sHeader' => _p('contest.most_viewed_entries'),
            'aEntries' => $aEntries,
            'sContestUrl' => $sContestUrl,
            'sViewMoreUrl' => $sViewMoreUrl,
            'sUrlNoImagePhoto'	=> Phpfox::getParam('core.path_file').'module/contest/static/image/no_photo_small.png'
        ));
        
        return 'block';
    }
}

?>