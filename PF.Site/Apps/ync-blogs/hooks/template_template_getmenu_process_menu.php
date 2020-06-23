<?php
if (trim($aMenu['url'], '/') == 'advanced-blog' && $oReq->get('req1') == 'ynblog') {
    $aMenus[$iKey]['is_selected'] = true;
}
