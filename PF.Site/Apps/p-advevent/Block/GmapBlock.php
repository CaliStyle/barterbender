<?php
namespace Apps\P_AdvEvent\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class GmapBlock extends Phpfox_Component
{
    public function process()
    {
        $name="default_location";

        $aRow=Phpfox::getService("fevent")->getSetting($name);

        $this->template()->setBreadcrumb('Set default location of Google Map');
        $aCoords = Phpfox::getService('fevent')->getEventCoordinates();

        $lat=10;
        $lng=3;
        $zoom=8;
        if(isset($aRow['default_value']) && $aRow['default_value']!="")
        {
            list($aCoordinates, $sGmapAddress) = Phpfox::getService("fevent.process")->address2coordinates($aRow['default_value']);
            $lat = $aCoordinates[1];
            $lng = $aCoordinates[0];
            $zoom=13;
        }


        $this->template()->assign(array(
            'aCoords' => $aCoords,
            'lat' => $lat,
            'lng' => $lng,
            'zoom' => $zoom,
            'aRow' => $aRow
        ));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('fevent.component_block_gmap_clean')) ? eval($sPlugin) : false);
    }
}