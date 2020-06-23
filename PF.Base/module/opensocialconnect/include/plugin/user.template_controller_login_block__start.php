<?php
if (Phpfox::isModule('opensocialconnect')) {
    echo Phpfox::getService('opensocialconnect.providers')->viewLoginHeaderVisitor();
}
