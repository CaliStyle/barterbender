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
class Fevent_Service_Gapi_Process extends Phpfox_Service 
{
	/**
	 * Class constructor
	 */	
	public function __construct()
	{	
		$this->_sTable = Phpfox::getT('fevent_gapi');
	}
    
    public function add($aVals)
    {
        $aInsert = array();
        $aInsert['oauth2_client_id'] = $aVals['oauth2_client_id'];
        $aInsert['oauth2_client_secret'] = $aVals['oauth2_client_secret'];
        $aInsert['developer_key'] = $aVals['developer_key'];
        
        $iId = $this->database()->insert($this->_sTable, $aInsert);
        return $iId;
    }

    public function update($aVals, $iId)
    {
        $aUpdate = array();
        $aUpdate['oauth2_client_id'] = $aVals['oauth2_client_id'];
        $aUpdate['oauth2_client_secret'] = $aVals['oauth2_client_secret'];
        $aUpdate['developer_key'] = $aVals['developer_key'];
        
        $uId = $this->database()->update($this->_sTable, $aUpdate, 'id='.(int)$iId);
        return $uId;
    }
    
    public function delete($iId)
    {
        return $this->database()->delete($this->_sTable, 'id='.(int)$iId);
    }
}

?>