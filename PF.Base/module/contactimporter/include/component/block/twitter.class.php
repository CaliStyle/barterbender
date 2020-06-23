<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');

class Contactimporter_Component_Block_Twitter extends Phpfox_Component
{
	public function process()
	{
		$this -> template() -> assign(array(
			'core_path' => Phpfox::getService('socialbridge')->getStaticPath(),
			'sCallback' => urlencode(Phpfox::getLib('url') -> makeUrl('contactimporter.twitter')),
		));

	}

}
