<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
class Fevent_Component_Block_Photo extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $aEvent = $this->getParam('aEvent');
        
        if (!($aImages = Phpfox::getService('fevent')->getImages($aEvent['event_id'])))
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
        (($sPlugin = Phpfox_Plugin::get('fevent.component_block_photo_clean')) ? eval($sPlugin) : false);
    }
}

?>