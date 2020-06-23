<?php

if(Phpfox::isModule('ultimatevideo'))
{
    if($sClass == 'ultimatevideo.admincp.customfield')
    {
        $sClass = 'ultimatevideo.admincp.customfield.index';
    }
    if($sClass == 'ultimatevideo.admincp.category')
    {
        $sClass = 'ultimatevideo.admincp.category.index';
    }
}