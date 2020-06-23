<?php
namespace Apps\P_AdvEvent\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class PhotoBlock extends Phpfox_Component
{
    public function process()
    {
        $aEvent = $this->getParam('aEvent');

        $aImages = Phpfox::getService('fevent')->getImages($aEvent['event_id']);
        $iTotalImage = Phpfox::getService('fevent')->countImages($aEvent['event_id']);

        $this->template()->assign(array(
                'aImages' => $aImages,
                'aForms' => $aEvent,
                'iEventId' => $aEvent['event_id'],
                'iTotalImage' => $iTotalImage,
                'iTotalImageLimit' => Phpfox::getUserParam('fevent.total_photo_upload_limit'),
                'aParamsUpload' => array(
                    'id' => $aEvent['event_id'],
                    'total_image' => $iTotalImage,
                    'total_image_limit' => Phpfox::getUserParam('fevent.max_upload_image_event'),
                    'remain_upload' => Phpfox::getUserParam('fevent.max_upload_image_event') - $iTotalImage
                ),
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