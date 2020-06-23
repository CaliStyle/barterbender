<?php

if ($sType == 'job')
{
    Phpfox::getService('foxfavorite.process')->add('jobposting', $iId);
}

?>