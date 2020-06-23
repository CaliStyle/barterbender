<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Service_Classhelper extends Phpfox_Service
{

    private $_aObject = array();
    private $_aAPIs = array();    
    /**
    * Fine and load an payment API class and make sure it exists. (used to include interface into implementing class)
    *
    * @param string $sClass API class name.
    * @return bool TRUE if API has loaded, FALSE if not.
    */
    public function getAPIClass($sClass)
    {
            if (isset($this->_aAPIs[$sClass]))
            {
                    return true;
            }

            $this->_aAPIs[$sClass] = md5($sClass);		

            $sClass = str_replace('.', PHPFOX_DS, $sClass);
            $sFile = PHPFOX_DIR_MODULE . $sClass . '.class.php';

            if (file_exists($sFile))
            {			
                    require($sFile);
                    return true;
            }		


            Phpfox_Error::trigger('Unable to load class: ' . $sClass, E_USER_ERROR);

            return false;
    }

    /**
    * Get a younet payment gateway API. This includes the class file and creates the object for you.
    * @param string $sClass Library class name.
    * @param array $aParams ARRAY of params you can pass to the library.
    * @return object Object of the library class is returned.
    */
    public function &getAPI($sClass, $aParams = array())
    {				
            $sHash = md5($sClass . serialize($aParams));
            $this->getAPIClass($sClass);		
            $sClass = str_replace('.include', '', $sClass);
            $this->_aObject[$sHash] = $this->getObject($sClass, $aParams);

            return $this->_aObject[$sHash];
    }


    /**
    * Gets and creates an object for a class.
    *
    * @param string $sClass Class name.
    * @param array $aParams Params to pass to the class.
    * @return object Object created will be returned.
    */
    public function &getObject($sClass, $aParams = array())
    {		
            $sHash = md5($sClass . serialize($aParams));

            if (isset($this->_aObject[$sHash]))
            {
                    return $this->_aObject[$sHash];
            }	

            (PHPFOX_DEBUG ? Phpfox_Debug::start('object') : false);

            $sClass = str_replace(array('.', '-'), '_', $sClass);		

            if (!class_exists($sClass))
            {
                    Phpfox_Error::trigger('Unable to call class: ' . $sClass, E_USER_ERROR);
            }		

            if ($aParams)
            {
                    $this->_aObject[$sHash] = new $sClass($aParams);
            }
            else 
            {		
                    $this->_aObject[$sHash] = new $sClass();
            }

            (PHPFOX_DEBUG ? Phpfox_Debug::end('object', array('name' => $sClass)) : false);

            if (method_exists($this->_aObject[$sHash], 'getInstance'))
            {
                    return $this->_aObject[$sHash]->getInstance();
            }				

            return $this->_aObject[$sHash];
    }
}

?>
