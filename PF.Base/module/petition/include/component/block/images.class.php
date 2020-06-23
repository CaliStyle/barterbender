<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Block_Images extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
            $aPetition = $this->getParam('aPetition');
            
            if (!$aPetition)
            {
                    return false;
            }
        
            $aImages = Phpfox::getService('petition')->getImages($aPetition['petition_id']);	
           
            $this->template()->assign(array(
                    'aImages' => $aImages,
                    'corepath' => Phpfox::getParam('core.path')
                )
            );
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('petition.component_block_images_clean')) ? eval($sPlugin) : false);
	}
}

?>