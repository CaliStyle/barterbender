<?php

/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		YouNetCo Company
 * @author  		AnNT
 * @package 		FoxFavorite_Module
 */
class Foxfavorite_Service_Log_Process extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('foxfavorite_log');
    }
    
    public function addLogNotifyFollower($sModule, $iItemId, $iStatus = 0)
    {
        $aSql = array(
            'type' => 'notify_follower',
            'module' => $sModule,
            'item_id' => $iItemId,
            'status' => $iStatus
        );
        
        return $this->database()->insert($this->_sTable, $aSql);
    }
    
    public function setNotifiedFollower($sModule, $iItemId)
    {
        $iLogId = $this->database()->select('log_id')
        ->from($this->_sTable)
        ->where('type = "notify_follower" AND module = "'.$sModule.'" AND item_id = '.$iItemId)
        ->execute('getSlaveField');
        
        if (!empty($iLogId))
        {
            return $this->database()->update($this->_sTable, array('status' => 1), 'log_id = '.$iLogId);
        }
        else
        {
            return $this->addLogNotifyFollower($sModule, $iItemId, 1);
        }
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing 
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('foxfavorite.service_log_process__call'))
        {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method '.__class__.'::'.$sMethod.'()', E_USER_ERROR);
    }
}

?>