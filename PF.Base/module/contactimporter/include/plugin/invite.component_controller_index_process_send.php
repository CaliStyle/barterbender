<?php

if (Phpfox::isModule('contactimporter') && isset($iInvite) && $iInvite > 0)
{
    Phpfox::getService('contactimporter')->updateStatistic(Phpfox::getUserId(), 1, 0, 'manual');
}
?>
