<?php

defined('PHPFOX') or exit('NO DICE!');

function ynd_install401p8()
{
    $oDatabase = Phpfox::getLib('database') ;

    if(!$oDatabase->isField(Phpfox::getT('document'),'image_server_id'))
    {
        $oDatabase->query("ALTER TABLE `".Phpfox::getT('document')."` ADD COLUMN `image_server_id` tinyint(1) unsigned DEFAULT 0;");
        $oDatabase->query("UPDATE `".Phpfox::getT('document')."` SET `image_server_id` = `server_id`");
    }

}

ynd_install401p8();

?>