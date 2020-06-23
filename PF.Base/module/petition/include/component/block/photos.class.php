<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Block_Photos extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iId = $this->getParam('iId');
        
        if (!($aImages = Phpfox::getService('petition')->getImages($iId)))
        {
            return false;
        }
        $this->template()->assign(array(
                'aImages' => $aImages
            )
        );
    }
    
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('petition.component_block_photos_clean')) ? eval($sPlugin) : false);
    }
}

?>