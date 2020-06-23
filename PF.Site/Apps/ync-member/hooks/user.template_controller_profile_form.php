<?php

if (Phpfox_Module::instance()->getFullControllerName() == 'user.profile') {
Phpfox::getComponent('ynmember.profile_places', null, 'controller');
}
