<?php
if ($sConnection == 'invite.index' && $aMenu['url'] == 'contactimporter.export' && !Phpfox::getUserParam('contactimporter.export_contact')) {
    unset($aMenus[$iKey]);
}