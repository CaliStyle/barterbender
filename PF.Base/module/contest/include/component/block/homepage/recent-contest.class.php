<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Homepage_Recent_Contest extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iLimit = 6;

        list($iCnt, $aContests) = Phpfox::getService('contest.contest')->getTopContests($sType = 'recent', $iLimit);

        if ($iCnt <= 0)
        {
            return false;
        }
        
        $aContests = $this->_implementStyle($aContests);
        
        $this->template()->assign(array(
            'aRecentContests' => $aContests,
            'iCntRecentContests' => $iCnt,
            'iLimit' => $iLimit,
            'sUrlNoImagePhoto'	=> Phpfox::getParam('core.path_file').'module/contest/static/image/no_photo_small.png',
            'sView' => 'recent'));

        return 'block';
    }

    private function _implementStyle($aContests)
    {
        $aStyleType = array(
            '1' => 'enblog',
            '2' => 'enphoto',
            '3' => 'envideo',
            '4' => 'enmusic',
        );

        if (!empty($aContests))
        {
            foreach ($aContests as $k => $aContest)
            {
                $aContests[$k]['style_type'] = $aStyleType[$aContest['type']];
            }
        }

        return $aContests;
    }
}

?>
