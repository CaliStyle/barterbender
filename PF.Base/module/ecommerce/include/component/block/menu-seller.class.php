<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Block_Menu_Seller extends Phpfox_Component
{

    public function process()
    {
        $sFullControllerName = Phpfox::getLib('module')->getFullControllerName();
        
        $this->template()->assign(array('sFullControllerName' => $sFullControllerName));
        
        return 'block';
    }

}

?>