<?php

/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          
 * @version          
 */
defined('PHPFOX') or exit('NO DICE!');

class Gettingstarted_Service_Unsubscribe extends Phpfox_Service
{
    /**
	 * Class constructor
	 */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('gettingstarted_unsubscribe');
    }
    
    public function add($iUserId)
    {
        $bUnsubscribe = $this->isUnsubscribe($iUserId);
        if (!$bUnsubscribe)
        {
            $this->database()->insert($this->_sTable, array('user_id' => $iUserId));
        }
        
        return true;
    }
    
    public function delete($iUserId)
    {
        $this->database()->delete($this->_sTable, 'user_id = '.$iUserId);
        
        return true;
    }
    
    public function isUnsubscribe($iUserId)
    {
        $iId = $this->database()->select('unsubscribe_id')->from($this->_sTable)->where('user_id = '.$iUserId)->execute('getSlaveField');
        
        if (!empty($iId))
        {
            return true;
        }
        
        return false;
    }
}

?>