<?php

defined('PHPFOX') or exit('NO DICE!');

class Contactimporter_Component_Block_SendAllPopup extends Phpfox_Component
{

    public function process()
    {
        $this->template()->assign(array(
            "sMainUrl" => Phpfox::getLib('url')->makeUrl('contactimporter'),
            "sCorePath" => Phpfox::getParam('core.path'),
            "provider" => $this->getParam("provider"),
            "friends_count" => $this->getParam("friends_count")
        ));
    }

}

?>