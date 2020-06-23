<?php
    Phpfox::getLib('phpfox.database')->query("UPDATE " . Phpfox::getT('contactimporter_providers') . " SET enable = 0 WHERE name = 'fdcareer';");
    Phpfox::getLib('phpfox.database')->query("UPDATE " . Phpfox::getT('contactimporter_providers') . " SET enable = 0 WHERE name = 'famiva';");
?>