<?php

namespace Apps\P_AdvMarketplace\Block;

use Phpfox_Component;
use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class Photo extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $aListing = $this->getParam('aListing');

        $aImages = Phpfox::getService('advancedmarketplace')->getImages($aListing['listing_id']);

        $iTotalImage = Phpfox::getService('advancedmarketplace')->countImages($aListing['listing_id']);

        $this->template()->assign(array(
                'aImages' => $aImages,
                'aForms' => $aListing,
                'iListingId' => $aListing['listing_id'],
                'iTotalImage' => $iTotalImage,
                'iTotalImageLimit' => Phpfox::getUserParam('advancedmarketplace.total_photo_upload_limit'),
                'aParamsUpload' => array(
                    'id' => $aListing['listing_id'],
                    'total_image' => $iTotalImage,
                    'total_image_limit' => Phpfox::getUserParam('advancedmarketplace.total_photo_upload_limit'),
                    'remain_upload' => Phpfox::getUserParam('advancedmarketplace.total_photo_upload_limit') - $iTotalImage
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
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_block_photo_clean')) ? eval($sPlugin) : false);
    }
}
