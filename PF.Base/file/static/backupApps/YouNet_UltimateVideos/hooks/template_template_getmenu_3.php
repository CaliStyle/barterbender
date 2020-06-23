<?php

if (setting('ynuv_app_enabled') != '1' && $sConnection == 'main') {
    foreach ($aMenus as $key => $menu) {
        if ($menu['url'] == '/ultimatevideo') {
            unset($aMenus[$key]);
        }
    }
}
