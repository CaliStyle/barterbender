<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Homepage_Entries extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $aRecentEntries = array();
        $aMostVotedEntries = array();
        $iCnt = 0;
        
        $aContestType = Phpfox::getService('contest.constant')->getAllContestTypes();
        foreach ($aContestType as $aType)
        {
            $sType = $aType['name'];
            $aRecentEntries[$sType] = Phpfox::getService('contest.entry')->getRecentByContestType($sType, 8);
            $aMostVotedEntries[$sType] = Phpfox::getService('contest.entry')->getMostVotedByContestType($sType, 8);
            $iCnt = $iCnt + count($aRecentEntries[$sType]) + count($aMostVotedEntries[$sType]);
        }
        
        if ($iCnt == 0)
        {
            return false;
        }
        
        $this->template()->assign(array(
            'aRecentEntries' => $aRecentEntries,
            'aMostVotedEntries' => $aMostVotedEntries,
            'sUrlNoImagePhoto'	=> Phpfox::getParam('core.path_file').'module/contest/static/image/no_photo_small.png'
        ));

        return 'block';
    }
}

?>
