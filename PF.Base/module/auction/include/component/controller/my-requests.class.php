<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Controller_My_Requests extends Phpfox_Component {

    public function process()
    {
    	Phpfox::getService('auction.helper')->buildMenu();
         $this->template()
                ->setTitle(_p('my-requests'))
                ->setBreadcrumb(_p('my_requests'), $this->url()->makeUrl('ecommerce.my-requests'));
       	
       	return Phpfox::getLib('module')->setController('ecommerce.my-requests');
    }
}

?>