<?php
namespace Apps\P_AdvEvent\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class LocationController extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $name="default_location";
        if(isset($_POST['submit']))
        {

            if ($aVals = $this->request()->getArray('val'))
            {
                $default_value=$aVals['location'];
                $phrase_location=_p('location')."...";
                if($default_value==$phrase_location)
                    $default_value="";
                phpfox::getService("fevent")->updateSetting($name,$default_value);
            }
        }
        $aRow=phpfox::getService("fevent")->getSetting($name);

        $this->template()
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("module_fevent"), $this->url()->makeUrl('admincp.app',['id' => '__module_fevent']))
            ->setBreadcrumb(_p('admin_menu_manage_location'));
        $aCoords = Phpfox::getService('fevent')->getEventCoordinates();

        $oFilter = Phpfox::getLib('parse.input');
        $lat=10;
        $lng=3;
        $zoom=8;

        if(isset($aRow['default_value']) && $aRow['default_value']!="")
        {
            $aCoordinates=[];
            $aCoords=[];
            list($aCoordinates, $sGmapAddress) = phpfox::getService("fevent.process")->address2coordinates($aRow['default_value']);
            $lat = $aCoords[0]['lat'] = $aCoordinates[1];
            $lng = $aCoords[0]['lng'] = $aCoordinates[0];
            $zoom=13;
            $aCoords[0]['gmap_address'] = $oFilter->prepare($sGmapAddress);
        }


        $this->template()->assign(array(
            'aCoords' => $aCoords,
            'lat' => $lat,
            'lng' => $lng,
            'zoom' => $zoom,
            'aRow' => $aRow,
            'apiKey' => Phpfox::getParam('core.google_api_key'),
        ));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('fevent.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }
}