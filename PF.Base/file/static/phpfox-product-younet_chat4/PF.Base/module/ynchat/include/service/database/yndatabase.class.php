<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Ynchat_Service_Database_Yndatabase
{
    /**
     * Holds the drivers object
     *
     * @var object
     */
    private $_oObject = null;

    /**
     * Loads and initiates the SQL driver that we need to use.
     *
     */
    public function __construct()
    {
        if (!$this->_oObject)
        {
            switch(Phpfox::getParam(array('db', 'driver')))
            {
                case 'mysqli':
                    $this->_oObject = new Ynchat_Service_Database_Ynmysqli();
                    break;
                case 'postgres':
                    $this->_oObject = Phpfox::getLib('database.driver.postgres');
                    break;
                default:
                    $this->_oObject = new Ynchat_Service_Database_Ynmysql();
                    break;
            }
            $this->_oObject->connect(Phpfox::getParam(array('db', 'host')), Phpfox::getParam(array('db', 'user')), Phpfox::getParam(array('db', 'pass')), Phpfox::getParam(array('db', 'name')), Phpfox::getParam(array('db', 'port')));
        }
    }

    /**
     * Return the object of the storage object.
     *
     * @return object Object provided by the storage class we loaded earlier.
     */
    public function &getInstance()
    {
        return $this->_oObject;
    }
}

?>