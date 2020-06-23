<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Popular_Contest extends Phpfox_Component {

    public function process() {
        $iLimit = PHpfox::getParam('contest.number_of_contest_block_home_page');
         
        list($iCnt,$aContests) = Phpfox::getService('contest.contest')->getTopContests($sType = 'popular', $iLimit);
      
        $this->template()->assign(array(
            
            'aPopularContests' => $aContests,
            'iCntPopularContests' => $iCnt,
            'iLimit' => $iLimit,
                )
        );
		 if($iCnt==0 || defined('PHPFOX_IS_USER_PROFILE'))
            return false;
		else {
			$this->template()->assign(array(
				'sHeader' => _p('contest.popular_contests'),
			));
		}
        return 'block';
    }

}