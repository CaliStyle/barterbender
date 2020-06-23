<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Top_Contest extends Phpfox_Component
{

    public function process()
    {

        $iLimit = PHpfox::getParam('contest.number_of_contest_block_home_page');
        list($iCnt, $aContests) = Phpfox::getService('contest.contest')->getTopContests($sType = 'top', $iLimit);

        $this->template()->assign(array(
                'aTopContests' => $aContests,
                'iCntTopContests' => $iCnt,
                'sUrlNoImagePhoto' => Phpfox::getParam('core.path_file') . 'module/contest/static/image/no_photo_small.png',
                'iLimit' => $iLimit,
            )
        );
        if ($iCnt == 0 || defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        } else {
            $this->template()->assign(array(
                'sHeader' => _p('contest.top_contests'),
            ));
        }
        return 'block';
    }

}