<?php

defined('PHPFOX') or exit('NO DICE!');

class Socialbridge_Component_Ajax_Ajax extends Phpfox_Ajax
{
    public function activeProvider()
    {
        Phpfox::getService('socialbridge.providers')->activeProvider($this->get('id'), $this->get('active'));
    }
}
