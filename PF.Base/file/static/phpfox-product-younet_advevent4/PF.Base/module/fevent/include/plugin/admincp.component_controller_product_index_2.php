<?php

if (Phpfox::isModule('admincp') && isset($sUpgrade) == true && 'younetevent' == $sUpgrade)
{
	$aProducts = Phpfox::getService('admincp.product')->getForEdit('younetevent');
	if(isset($aProducts['version']) && $aProducts['version'] == '3.04')
	{
		$this->url()->send('admincp.product', null, _p('u_warning'));
		$mReturnFromPlugin = true;
	}
}

?> 