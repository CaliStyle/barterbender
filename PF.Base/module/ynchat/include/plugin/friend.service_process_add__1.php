<?php
;

if (Phpfox::isModule('ynchat'))
{
    Phpfox::getService('ynchat')->removeCache();
}

;
?>