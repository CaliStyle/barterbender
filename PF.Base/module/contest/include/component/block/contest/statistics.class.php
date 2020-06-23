<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Contest_Statistics extends Phpfox_Component
{
    public function process()
    {
        $sView = $this->getParam('sView');
        if (!$sView || ($sView != 'add' && $sView != 'entry'))
        {
            return false;
        }
        
        $aContest = $this->getParam('aContest');
        if (!$aContest)
        {
            return false;
        }

        $this->template()->assign(array(
            'aItem' => $aContest,
            'sHeader' => _p('contest.contest_statistics')
        ));
        
        return 'block';
    }
}

?>