<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Homepage_Featured_Slideshow extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iLimit = Phpfox::getParam('contest.number_of_contest_featured_block_home_page');
        
        list($iCnt, $aContests) = Phpfox::getService('contest.contest')->getTopContests($sType = 'featured', $iLimit);
        
        if ($iCnt <= 0)
        {
            return false;
        }
        
        $aContests = $this->_implementStyle($aContests);

        $autoPlay = 'false';
        if (Phpfox::getParam('contest.contest_autorun_featured_slideshow')) {
            $autoPlay = Phpfox::getParam('contest.contest_featured_slideshow_speed') ? Phpfox::getParam('contest.contest_featured_slideshow_speed') : 5000;
        }
        
        $this->template()->assign(array(
            'corepath' => phpfox::getParam('core.path'),
            'aFeaturedContests' => $aContests,
            'autoPlay' => $autoPlay,
            ));

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
