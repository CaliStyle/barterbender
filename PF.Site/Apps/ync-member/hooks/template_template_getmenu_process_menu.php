<?php

if ((trim($aMenu['url'], '/') == 'members') &&  $oReq->get('req1') == 'ynmember') {
    $aMenus[$iKey]['is_selected'] = true;
}