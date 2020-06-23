<?php

function contactimporter_401()
{

    $sTable = Phpfox::getT('contactimporter_providers');
    $aOldData = Phpfox::getLib('phpfox.database')->select('name')
        ->from($sTable,'b')
        ->where('b.name like \'%gmail%\' or b.name like \'%yahoo%\' or b.name like \'%linkedin%\' or b.name like \'%facebook%\' or b.name like \'%hotmail%\' or b.name like \'%twitter%\'')
        ->execute('getSlaveRows');
    $aSelected = [];
    foreach ($aOldData as $key => $aData)
    {
        if(isset($aData['name']))
        {
            $aSelected[] = '\''.$aData['name'].'\'';
        }
    }
    $sSelected = implode(',',$aSelected);
    if(!empty($sSelected))
        Phpfox::getLib('phpfox.database')->delete($sTable,'name NOT IN ('.$sSelected.')');

}

contactimporter_401();

