
<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class TourGuides_Service_User extends Phpfox_Service 
{
    /**
     * Class constructor
     */    
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('tourguides_usersetting');
        
    }    
    public function init()
    {
        
    }
    public function addUserSetting($aUserSetting = array())
    {
        return $this->database()->insert($this->_sTable,$aUserSetting);
    }
    public function removeUserSetting($iUserId = 0 , $iTourId = 0)
    {
        return $this->database()->delete($this->_sTable,'user_id = '.(int)$iUserId. ' AND tour_id = '.(int)$iTourId);
    }
    public function updateUserSetting($iUserId, $iTourId,$aParams)
    {
        return $this->database()->update($this->_sTable,$aParams,'user_id = '.(int)$iUserId. ' AND tour_id = '.(int)$iTourId);
    }
    public function getUserSetting($iUserId = 0,$iTourId)
    {
        $aStep = $this->database()->select('*')
                    ->from($this->_sTable)
                    ->where('user_id = '.(int)$iUserId. ' AND tour_id = '.(int)$iTourId)
                    ->execute('getRow');
        return $aStep;
    }
    public function __call($sMethod, $aArguments)
    {
        if ($sPlugin = Phpfox_Plugin::get('tourguides.service_process__call'))
        {
            return eval($sPlugin);
        }
        
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }    
}

?>