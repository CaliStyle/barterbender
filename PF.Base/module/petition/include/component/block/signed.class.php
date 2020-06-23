<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Block_Signed extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
         
         if (defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_PAGES_VIEW'))
         {
            return false;
         }
         list($iTotal, $aSigned) = Phpfox::getService('petition')->getMostSigned();
         if (!$iTotal)
         {
               return false;
         }
         
         $this->template()->assign(array(
                     'sHeader' => Phpfox::getPhrase('petition.most_signed'),
                     'aSigned' => $aSigned,
                     'iTotal' => $iTotal
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
		(($sPlugin = Phpfox_Plugin::get('petition.component_block_signed_clean')) ? eval($sPlugin) : false);
	}
}

?>
