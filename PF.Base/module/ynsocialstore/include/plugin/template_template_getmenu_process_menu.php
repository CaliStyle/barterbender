<?php
if ($sConnection == 'main') {
    if (function_exists('materialParseMobileIcon') && !empty($aMenus[$iKey]['module']) && $aMenus[$iKey]['module'] == 'ynsocialstore') {
        $aMenus[$iKey]['mobile_icon'] = 'ico ico-cart-o';
    }
}
