<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_Checkout extends Phpfox_Component {

    public function process()
    {
      	Phpfox::getService('auction.helper')->buildMenu();
      	Phpfox::getService('auction.helper')->buildSectionMenu();
       	return Phpfox::getLib('module')->setController('ecommerce.checkout');
    }

}

?>