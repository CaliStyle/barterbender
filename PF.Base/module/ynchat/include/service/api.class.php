<?php

defined('PHPFOX') or exit('NO DICE!');

class Ynchat_Service_Api extends Phpfox_Service
{
	public function __construct()
	{
	}
	
    public function hasService($name)
    {
        static $aServices;

        if (NULL == $aServices)
        {
            $aServices = $this->getAvaliableServices();
        }

        return isset($aServices[$name]) ? $aServices[$name] : 0;
    }

    public function getAvaliableServices()
    {
        return array(
            'ynchat.process' => 1,
            'ynchat.ynchat' => 1,
        );
    }
	
}
?>