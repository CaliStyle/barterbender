<?php
namespace Apps\P_AdvEvent\Block;

use Phpfox;
use Phpfox_Component;

class ImageBlock extends Phpfox_Component
{
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