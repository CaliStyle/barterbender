<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');

class Contactimporter_Component_Block_Hotmail extends Phpfox_Component
{
	public function process()
	{
		if (isset($_SESSION['contactimporter']['hotmail']))
		{
			unset($_SESSION['contactimporter']['hotmail']);
		}

		$this -> template() -> assign(array(
			'sCentralizeUrl' => Phpfox::getService('contactimporter') -> getCentralizeUrl(),
			'core_path' => phpfox::getParam('core.path'),
			'tokenName' => Phpfox::getTokenName() . '[security_token]',
			'sSecurityToken' => Phpfox::getService('log.session') -> getToken(),
			'sCallback' => urlencode(Phpfox::getLib('url') -> makeUrl('contactimporter.hotmail')),
		));
	}

}
