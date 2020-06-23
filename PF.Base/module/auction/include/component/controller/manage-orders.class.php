<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_Manage_Orders extends Phpfox_Component {

    public function process()
    {
      	Phpfox::getService('auction.helper')->buildMenu();
      	$this->template()->setBreadcrumb(_p('manage_orders'), $this->url()->makeUrl('ecommerce.manage-orders'));
       	return Phpfox::getLib('module')->setController('ecommerce.manage-orders');
    }

}

?>