<?php
/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_GettingStarted
 * @version          2.01
 */

defined('PHPFOX') or exit('NO DICE!');

class Gettingstarted_component_controller_allarticle extends Phpfox_Component
{
	public function process()
	{
		$this->url()->send('gettingstarted');
	}
}
