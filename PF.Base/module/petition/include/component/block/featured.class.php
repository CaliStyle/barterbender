<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Block_Featured extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
         $iLimit = 10;
         list($iTotal, $aFeatured) = Phpfox::getService('petition')->getFeatured($iLimit);
         
         if (!$iTotal)
         {
                 return false;
         }
         
         $this->template()->assign(array(               
             'sHeader' => '',
             'corepath' => Phpfox::getParam('core.path'),
             'aFeatured' => $aFeatured,             
             ));
         return 'block';
     }
     
     public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_block_featured_clean')) ? eval($sPlugin) : false);
	}
}
?>
