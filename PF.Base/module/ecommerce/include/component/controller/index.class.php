<?php

defined('PHPFOX') or exit('NO DICE!');

class ecommerce_Component_Controller_Index extends Phpfox_Component
{
	public function process()
	{
       	return Phpfox::getLib('module')->setController('ecommerce.my-orders');
	}
}
?>