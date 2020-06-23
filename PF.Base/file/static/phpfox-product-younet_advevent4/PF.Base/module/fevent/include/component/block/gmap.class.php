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
class Fevent_Component_Block_Gmap extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$name="default_location";
		
		$aRow=phpfox::getService("fevent")->getSetting($name);
		
		$this->template()->setBreadcrumb('Set default location of Google Map');
		$aCoords = Phpfox::getService('fevent')->getEventCoordinates();
		
		$lat=10;
		$lng=3;
		$zoom=8;
		if(isset($aRow['default_value']) && $aRow['default_value']!="")
		{
			list($aCoordinates, $sGmapAddress) = phpfox::getService("fevent.process")->address2coordinates($aRow['default_value']);
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

?>