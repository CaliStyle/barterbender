<?php

class Contactimporter_Service_Integration  extends Phpfox_Service
{
	function processAfterRegister($step = 0)
	{
		if ($step == 8)
		{
			return;
		}

		if ($step == 7)
		{
			return;
		}
		// check if 3,4,5,6

		$bRedirect = Phpfox::getParam('contactimporter.redirect_after_register');

		if (1 or $bRedirect)
		{
			Phpfox::getLib('url') -> send('contactimporter.signup-success');
			exit;
		}
	}

}
