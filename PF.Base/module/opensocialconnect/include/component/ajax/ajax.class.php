<?php

defined('PHPFOX') or exit('NO DICE!');

class OpenSocialConnect_Component_Ajax_Ajax extends Phpfox_Ajax
{
    public function updateActivity()
    {
        Phpfox::getService('opensocialconnect.providers')->updateActivity($this->get('id'), $this->get('active'));
    }

    public function ordering()
    {
        Phpfox::getService('opensocialconnect.providers')->updateOrder($this->get('val'));
    }

    public function viewMore()
    {
        Phpfox::getBlock('opensocialconnect.viewmore', array());
    }

    public function mappingData()
    {
        $sProvider = $this->get('provider');

        /* Load new display with additional params */
        Phpfox::getBlock('opensocialconnect.mapservices', array(
            "provider" => $sProvider,
        ));
    }

}

?>