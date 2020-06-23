<?php

defined('PHPFOX') or exit('NO DICE!');


class Auction_Component_Controller_Profile extends Phpfox_Component
{
	public function process()
	{
		Phpfox::getService('auction.helper')->buildMenu();
		$this->setParam('bIsProfile', true);
		$aUser = $this->getParam('aUser');
				
		Phpfox::getComponent('auction.index', array('bNoTemplate' => true), 'controller');		
	}

	public function clean()
	{

	}

}
?>