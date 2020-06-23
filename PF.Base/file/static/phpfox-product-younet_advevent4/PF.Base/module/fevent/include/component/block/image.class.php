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
class Fevent_Component_Block_Image extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if (!($aEvent = $this->getParam('aEvent')))
		{
			return false;
		}
        
        if (empty($aEvent['image_path']))
        {
            // return false;
        }

        $aImages = Phpfox::getService('fevent')->getImages($aEvent['event_id']);

        $this->template()->assign(array(
                'aImages' => $aImages ,
                'sCorePath' => Phpfox::getParam('core.path'),
                'sDefaultPhoto' => Phpfox::getService('fevent')->getDefaultPhoto()
            )
        );
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fevent.component_block_image_clean')) ? eval($sPlugin) : false);
	}
}

?>