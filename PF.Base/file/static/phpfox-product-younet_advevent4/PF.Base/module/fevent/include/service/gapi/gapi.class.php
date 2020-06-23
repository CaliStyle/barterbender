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
class Fevent_Service_Gapi_Gapi extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('fevent_gapi');
	}
	
    public function getForManage()
    {
        $aGapi = $this->database()->select('*')->from($this->_sTable)->limit(1)->execute('getRow');
        return $aGapi;
    }
}

?>