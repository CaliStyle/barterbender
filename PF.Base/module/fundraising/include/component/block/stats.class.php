<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Stats extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
            $sHeader = _p('statistics');
            
			$aStats = Phpfox::getService('fundraising')->getStats();
            
            if(($aStats['reached'] + $aStats['expired'] + $aStats['ongoing'] + $aStats['closed']) == 0)
            {
                return false;
            }
            
            $this->template()->assign(array(
                            'sHeader'   => $sHeader,
                            'aStats'    => $aStats
                    )
            );
            
            return 'block';		
	}
	
}

?>