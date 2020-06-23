<?php

if ($sType == 'job')
{
    Phpfox::getService('foxfavorite.process')->UnFavorite('jobposting', $iId);
}

?>