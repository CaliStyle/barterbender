<?php

defined('PHPFOX') or exit('NO DICE!');

class SocialBridge_Service_Callback extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {

    }

    public function onDeleteUser($iUser)
    {
        $this->database()->delete(Phpfox::getT('socialbridge_agents'), 'user_id = ' . (int)$iUser);
        $this->database()->delete(Phpfox::getT('socialbridge_token'), 'user_id = ' . (int)$iUser);
    }
}
