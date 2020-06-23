<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Block_Stats extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
            $sHeader = Phpfox::getPhrase('petition.petitions_stats');
            
            if($aPetition = $this->getParam('aPetition'))
            {
                $aStats = $aPetition;
                $aStats['type']='item';
                $sHeader = Phpfox::getPhrase('petition.stats');
            }
            else
            {
                $aStats = Phpfox::getService('petition')->getStats();
                $aStats['type']='full';   
            }            
            
            if(($aStats['victories'] + $aStats['ongoing'] + $aStats['closed']) == 0)
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
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_block_signnow_clean')) ? eval($sPlugin) : false);
	}
}

?>