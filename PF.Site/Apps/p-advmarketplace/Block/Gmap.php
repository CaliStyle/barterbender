<?php

namespace Apps\P_AdvMarketplace\Block;

use Phpfox_Component;
use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class Gmap extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $aRow = phpfox::getService('advancedmarketplace')->getSettings();

        $this->template()->setBreadcrumb('Set default location of Google Map');
        $aCoords = Phpfox::getService('advancedmarketplace')->getListingCoordinates();
        $lat = 10;
        $lng = 3;
        $zoom = 8;
        if (count($aCoords)) {
            $aCoords[0]['event_id'] = $aCoords[0]['listing_id'];
        }

        if (isset($aRow['location_setting']) && $aRow['location_setting'] != "") {
            list($aCoordinates,) = phpfox::getService("advancedmarketplace.process")->address2coordinates($aRow['location_setting']);
            $lat = $aCoordinates[1];
            $lng = $aCoordinates[0];
            $zoom = 13;
        }

        $this->template()->assign(array(
            'aCoords' => $aCoords,
            'lat' => $lat,
            'lng' => $lng,
            'zoom' => $zoom,
            'aRow' => $aRow,
            'googleApiKey' => Phpfox::getParam('core.google_api_key')
        ));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_block_gmap_clean')) ? eval($sPlugin) : false);
    }
}
